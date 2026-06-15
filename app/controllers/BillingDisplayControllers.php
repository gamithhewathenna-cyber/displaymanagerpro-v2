<?php
/**
 * Billing Controller
 */
class BillingController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user     = $this->currentUser();
        $sub      = Subscription::forUser($user['id']);
        $plans    = Plan::active();
        $payments = Payment::forUser($user['id']);

        $this->view('billing/index', [
            'title'    => 'Billing',
            'user'     => User::find($user['id']),
            'sub'      => $sub,
            'plans'    => $plans,
            'payments' => $payments,
        ]);
    }

    public function checkout(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $planId  = (int)($_POST['plan_id'] ?? 0);
        $cycle   = ($_POST['billing_cycle'] ?? 'monthly') === 'annual' ? 'annual' : 'monthly';
        $plan    = Plan::find($planId);

        if (!$plan || !$plan['is_active']) {
            Session::flash('error', 'Plan not found.');
            $this->redirect('/billing');
        }

        $priceId = $cycle === 'annual'
            ? $plan['stripe_price_id_annual']
            : $plan['stripe_price_id_monthly'];

        if (!$priceId) {
            Session::flash('error', 'This plan is not available for billing yet. Please contact support.');
            $this->redirect('/billing');
        }

        try {
            $stripe   = new StripeService();
            $fullUser = User::find($user['id']);

            // Create or retrieve customer
            $sub = Subscription::forUser($user['id']);
            $customerId = $sub['stripe_customer_id'] ?? null;

            if (!$customerId) {
                $customer   = $stripe->createCustomer($fullUser['email'], $fullUser['name']);
                $customerId = $customer['id'];
            }

            $session = $stripe->createCheckoutSession([
                'customer'                   => $customerId,
                'payment_method_types[]'     => 'card',
                'mode'                       => 'subscription',
                'line_items[0][price]'       => $priceId,
                'line_items[0][quantity]'    => '1',
                'subscription_data[trial_period_days]' => TRIAL_DAYS,
                'success_url'                => Helpers::baseUrl('billing/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url'                 => Helpers::baseUrl('billing'),
                'client_reference_id'        => $user['id'],
            ]);

            $this->redirect($session['url']);
        } catch (Exception $e) {
            error_log('Checkout error: ' . $e->getMessage());
            Session::flash('error', 'Could not start checkout. Please try again.');
            $this->redirect('/billing');
        }
    }

    public function checkoutSuccess(): void
    {
        $this->requireAuth();
        Session::flash('success', 'Subscription activated! Welcome aboard.');
        $this->redirect('/dashboard');
    }

    public function portal(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        if (!$sub || !$sub['stripe_customer_id']) {
            Session::flash('error', 'No billing account found. Please subscribe first.');
            $this->redirect('/billing');
        }

        try {
            $stripe  = new StripeService();
            $session = $stripe->createBillingPortalSession(
                $sub['stripe_customer_id'],
                Helpers::baseUrl('billing')
            );
            $this->redirect($session['url']);
        } catch (Exception $e) {
            error_log('Portal error: ' . $e->getMessage());
            Session::flash('error', 'Could not open billing portal. Please contact support.');
            $this->redirect('/billing');
        }
    }

    public function cancel(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        if (!$sub || !$sub['stripe_subscription_id']) {
            Session::flash('error', 'No active subscription found.');
            $this->redirect('/billing');
        }

        try {
            $stripe = new StripeService();
            $stripe->cancelSubscription($sub['stripe_subscription_id'], true);
            Subscription::update($sub['id'], ['canceled_at' => date('Y-m-d H:i:s')]);
            ActivityLog::log('subscription_canceled', 'Subscription canceled');
            Session::flash('success', 'Your subscription has been canceled. Access continues until the end of your billing period.');
        } catch (Exception $e) {
            error_log('Cancel error: ' . $e->getMessage());
            Session::flash('error', 'Could not cancel subscription. Please contact support.');
        }

        $this->redirect('/billing');
    }

    public function webhook(): void
    {
        $payload    = file_get_contents('php://input');
        $sigHeader  = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret     = Settings::get('stripe_webhook_secret');

        try {
            $stripe = new StripeService();
            $event  = $stripe->constructWebhookEvent($payload, $sigHeader, $secret);
        } catch (Exception $e) {
            error_log('Webhook verification failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        $data   = $event['data']['object'];
        $type   = $event['type'];

        try {
            match($type) {
                'checkout.session.completed'       => $this->handleCheckoutCompleted($data),
                'customer.subscription.created'    => $this->handleSubscriptionCreated($data),
                'customer.subscription.updated'    => $this->handleSubscriptionUpdated($data),
                'customer.subscription.deleted'    => $this->handleSubscriptionDeleted($data),
                'invoice.paid'                     => $this->handleInvoicePaid($data),
                'invoice.payment_failed'           => $this->handleInvoicePaymentFailed($data),
                default                            => null,
            };
        } catch (Exception $e) {
            error_log("Webhook handler error [$type]: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Handler error']);
            return;
        }

        http_response_code(200);
        echo json_encode(['received' => true]);
    }

    private function handleCheckoutCompleted(array $data): void
    {
        $userId     = (int)($data['client_reference_id'] ?? 0);
        $customerId = $data['customer'] ?? '';
        $subStripeId = $data['subscription'] ?? '';
        if (!$userId || !$customerId || !$subStripeId) return;

        $stripe = new StripeService();
        $stripeSub = $stripe->retrieveSubscription($subStripeId);
        $priceId   = $stripeSub['items']['data'][0]['price']['id'] ?? null;
        $plan      = $priceId ? Plan::findByStripePrice($priceId) : null;

        $existingSub = Subscription::forUser($userId);
        if ($existingSub) {
            Subscription::update($existingSub['id'], [
                'plan_id'              => $plan['id'] ?? $existingSub['plan_id'],
                'stripe_customer_id'   => $customerId,
                'stripe_subscription_id' => $subStripeId,
                'stripe_price_id'      => $priceId,
                'status'               => $stripeSub['status'],
                'trial_ends_at'        => isset($stripeSub['trial_end']) ? date('Y-m-d H:i:s', $stripeSub['trial_end']) : null,
                'current_period_start' => date('Y-m-d H:i:s', $stripeSub['current_period_start']),
                'current_period_end'   => date('Y-m-d H:i:s', $stripeSub['current_period_end']),
            ]);
        } else {
            Subscription::create([
                'user_id'              => $userId,
                'plan_id'              => $plan['id'] ?? 1,
                'stripe_customer_id'   => $customerId,
                'stripe_subscription_id' => $subStripeId,
                'stripe_price_id'      => $priceId,
                'status'               => $stripeSub['status'],
                'trial_ends_at'        => isset($stripeSub['trial_end']) ? date('Y-m-d H:i:s', $stripeSub['trial_end']) : null,
                'current_period_start' => date('Y-m-d H:i:s', $stripeSub['current_period_start']),
                'current_period_end'   => date('Y-m-d H:i:s', $stripeSub['current_period_end']),
            ]);
        }
    }

    private function handleSubscriptionCreated(array $data): void
    {
        $this->syncSubscription($data);
    }

    private function handleSubscriptionUpdated(array $data): void
    {
        $this->syncSubscription($data);
    }

    private function handleSubscriptionDeleted(array $data): void
    {
        $sub = Subscription::findByStripeId($data['id'] ?? '');
        if ($sub) {
            Subscription::update($sub['id'], [
                'status'   => 'canceled',
                'ends_at'  => date('Y-m-d H:i:s', $data['ended_at'] ?? time()),
            ]);
        }
    }

    private function handleInvoicePaid(array $data): void
    {
        $customerId = $data['customer'] ?? '';
        $sub = Subscription::findByStripeCustomer($customerId);
        if (!$sub) return;

        Payment::create([
            'user_id'                 => $sub['user_id'],
            'subscription_id'         => $sub['id'],
            'stripe_invoice_id'       => $data['id'] ?? null,
            'stripe_payment_intent_id'=> $data['payment_intent'] ?? null,
            'amount'                  => ($data['amount_paid'] ?? 0) / 100,
            'currency'                => strtoupper($data['currency'] ?? 'aud'),
            'status'                  => 'succeeded',
            'description'             => $data['description'] ?? 'Subscription payment',
            'invoice_url'             => $data['hosted_invoice_url'] ?? null,
            'invoice_pdf'             => $data['invoice_pdf'] ?? null,
            'paid_at'                 => date('Y-m-d H:i:s', $data['status_transitions']['paid_at'] ?? time()),
        ]);
    }

    private function handleInvoicePaymentFailed(array $data): void
    {
        $customerId = $data['customer'] ?? '';
        $sub = Subscription::findByStripeCustomer($customerId);
        if ($sub) {
            Subscription::update($sub['id'], ['status' => 'past_due']);
            Payment::create([
                'user_id'           => $sub['user_id'],
                'subscription_id'   => $sub['id'],
                'stripe_invoice_id' => $data['id'] ?? null,
                'amount'            => ($data['amount_due'] ?? 0) / 100,
                'currency'          => strtoupper($data['currency'] ?? 'aud'),
                'status'            => 'failed',
                'description'       => 'Payment failed',
            ]);
        }
    }

    private function syncSubscription(array $data): void
    {
        $sub = Subscription::findByStripeId($data['id'] ?? '');
        if (!$sub) return;

        $priceId = $data['items']['data'][0]['price']['id'] ?? null;
        $plan    = $priceId ? Plan::findByStripePrice($priceId) : null;

        $updates = [
            'status'               => $data['status'],
            'current_period_start' => date('Y-m-d H:i:s', $data['current_period_start'] ?? time()),
            'current_period_end'   => date('Y-m-d H:i:s', $data['current_period_end'] ?? time()),
        ];
        if ($plan) $updates['plan_id'] = $plan['id'];
        if (isset($data['trial_end'])) {
            $updates['trial_ends_at'] = date('Y-m-d H:i:s', $data['trial_end']);
        }
        if ($data['cancel_at_period_end'] ?? false) {
            $updates['canceled_at'] = date('Y-m-d H:i:s');
        }
        Subscription::update($sub['id'], $updates);
    }
}

/**
 * Display Controller – Public TV player
 */
class DisplayController extends BaseController
{
    public function show(string $slug): void
    {
        $channel = Channel::findBySlug($slug);
        if (!$channel || !$channel['is_active']) {
            http_response_code(404);
            $this->view('display/offline', ['title' => 'Channel Unavailable'], null);
            return;
        }

        // Verify subscription is active
        $sub = Subscription::forUser($channel['user_id']);
        if (!Subscription::isActive($sub)) {
            http_response_code(403);
            $this->view('display/offline', ['title' => 'Subscription Expired'], null);
            return;
        }

        $slides = Slide::forChannel($channel['id']);
        if (empty($slides)) {
            $this->view('display/empty', ['title' => 'No Content', 'channel' => $channel], null);
            return;
        }

        Channel::touchDisplayed($channel['id']);

        // No layout for display page (full screen)
        $this->view('display/player', [
            'title'   => Helpers::e($channel['name']),
            'channel' => $channel,
            'slides'  => $slides,
        ], null);
    }

    public function data(string $slug): void
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $channel = Channel::findBySlug($slug);
        if (!$channel || !$channel['is_active']) {
            $this->json(['error' => 'Channel not found'], 404);
        }

        $sub = Subscription::forUser($channel['user_id']);
        if (!Subscription::isActive($sub)) {
            $this->json(['error' => 'Subscription expired'], 403);
        }

        $slides = Slide::forChannel($channel['id']);
        $this->json([
            'channel' => [
                'id'                   => $channel['id'],
                'name'                 => $channel['name'],
                'orientation'          => $channel['orientation'],
                'slide_duration'       => $channel['slide_duration'],
                'transition_effect'    => $channel['transition_effect'],
                'auto_refresh_minutes' => $channel['auto_refresh_minutes'],
                'background_color'     => $channel['background_color'],
            ],
            'slides'  => array_map(fn($s) => [
                'id'       => $s['id'],
                'url'      => $s['public_url'],
                'duration' => $s['duration_override'] ?? $channel['slide_duration'],
                'name'     => $s['original_name'],
            ], $slides),
            'updated_at' => date('c'),
        ]);
    }
}

/**
 * Support Ticket Controller
 */
class SupportController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $tickets = SupportTicket::forUser($user['id']);
        $this->view('support/index', ['title' => 'Support', 'tickets' => $tickets]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('support/create', ['title' => 'Open Ticket']);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $subject = Helpers::sanitize($_POST['subject'] ?? '');
        $message = Helpers::sanitize($_POST['message'] ?? '');

        if (strlen($subject) < 5 || strlen($message) < 10) {
            Session::flash('error', 'Please provide a subject and detailed message.');
            $this->redirect('/support/create');
        }

        $ticketId = SupportTicket::create($user['id'], [
            'subject'  => $subject,
            'priority' => in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium',
        ]);
        SupportTicket::addReply($ticketId, $user['id'], $message, false);

        Session::flash('success', 'Support ticket created. We\'ll be in touch soon.');
        $this->redirect('/support');
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $user   = $this->currentUser();
        $ticket = SupportTicket::find((int)$id);
        if (!$ticket || ($ticket['user_id'] !== $user['id'] && $user['role'] !== 'admin')) {
            $this->abort(404);
        }
        $replies = SupportTicket::getReplies((int)$id);
        $this->view('support/show', ['title' => 'Ticket #' . $ticket['ticket_number'], 'ticket' => $ticket, 'replies' => $replies]);
    }

    public function reply(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user   = $this->currentUser();
        $ticket = SupportTicket::find((int)$id);
        if (!$ticket || ($ticket['user_id'] !== $user['id'] && $user['role'] !== 'admin')) {
            $this->abort(404);
        }

        $message = Helpers::sanitize($_POST['message'] ?? '');
        if (strlen($message) < 2) {
            Session::flash('error', 'Reply cannot be empty.');
            $this->redirect('/support/' . $id);
        }

        SupportTicket::addReply((int)$id, $user['id'], $message, $user['role'] === 'admin');
        Session::flash('success', 'Reply sent.');
        $this->redirect('/support/' . $id);
    }
}
