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

    public function subscribe(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user   = $this->currentUser();
        $planId = (int)($_POST['plan_id'] ?? 0);
        $cycle  = ($_POST['billing_cycle'] ?? 'monthly') === 'annual' ? 'annual' : 'monthly';
        $plan   = Plan::find($planId);

        if (!$plan || !$plan['is_active']) {
            Session::flash('error', 'Plan not found.');
            $this->redirect('/billing');
        }

        $paypalPlanId = $cycle === 'annual'
            ? $plan['stripe_price_id_annual']
            : $plan['stripe_price_id_monthly'];

        if (!$paypalPlanId) {
            Session::flash('error', 'This plan is not available for billing yet. Please contact support.');
            $this->redirect('/billing');
        }

        // Validate coupon if provided
        $couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));
        $couponRow  = null;
        if ($couponCode) {
            $fullUser = User::find($user['id']);
            [$couponRow, $couponError] = Coupon::check($couponCode, $fullUser['email'] ?? '', $plan['slug']);
            if (!$couponRow) {
                Session::flash('error', "Coupon error: $couponError");
                $this->redirect('/billing');
            }
        }

        // Store pending info in session so return URL can link it to the user
        Session::set('paypal_pending', [
            'plan_id'    => $planId,
            'cycle'      => $cycle,
            'user_id'    => $user['id'],
            'coupon_id'  => $couponRow['id'] ?? null,
            'coupon_code'=> $couponRow['code'] ?? null,
        ]);

        try {
            $paypal      = new PayPalService();
            $fullUser    = User::find($user['id']);
            $approvalUrl = $paypal->createSubscription(
                $paypalPlanId,
                $fullUser,
                Helpers::baseUrl('billing/paypal/return'),
                Helpers::baseUrl('billing/paypal/cancel')
            );
            $this->redirect($approvalUrl);
        } catch (Exception $e) {
            error_log('PayPal subscribe error: ' . $e->getMessage());
            Session::flash('error', 'PayPal error: ' . $e->getMessage());
            $this->redirect('/billing');
        }
    }

    public function paypalReturn(): void
    {
        $this->requireAuth();
        $subId = Helpers::sanitize($_GET['subscription_id'] ?? '');

        if (!$subId) {
            Session::flash('error', 'PayPal returned an incomplete response. Please try again.');
            $this->redirect('/billing');
        }

        $pending = Session::get('paypal_pending');
        Session::forget('paypal_pending');

        try {
            $paypal  = new PayPalService();
            $ppSub   = $paypal->getSubscription($subId);
            $status  = strtolower($ppSub['status'] ?? 'pending');
            $planId  = $pending['plan_id'] ?? null;
            $userId  = $pending['user_id'] ?? $this->currentUser()['id'];
            $cycle   = $pending['cycle'] ?? 'monthly';

            // Resolve plan from PayPal plan_id if session lost
            if (!$planId) {
                $ppPlanId = $ppSub['plan_id'] ?? '';
                $dbPlan   = $ppPlanId ? Plan::findByStripePrice($ppPlanId) : null;
                $planId   = $dbPlan['id'] ?? 1;
            }

            $nextBillingTime = $ppSub['billing_info']['next_billing_time'] ?? null;
            $periodEnd       = $nextBillingTime ? date('Y-m-d H:i:s', strtotime($nextBillingTime)) : null;

            $existingSub = Subscription::forUser($userId);
            $data = [
                'plan_id'               => $planId,
                'stripe_subscription_id'=> $subId,
                'stripe_price_id'       => $ppSub['plan_id'] ?? '',
                'stripe_customer_id'    => $ppSub['subscriber']['payer_id'] ?? '',
                'status'                => in_array($status, ['active','approved']) ? 'active' : $status,
                'billing_cycle'         => $cycle,
                'current_period_start'  => date('Y-m-d H:i:s'),
                'current_period_end'    => $periodEnd,
            ];

            if ($existingSub) {
                Subscription::update($existingSub['id'], $data);
            } else {
                Subscription::create(array_merge($data, ['user_id' => $userId]));
            }

            // Record coupon redemption after successful payment
            $couponId = $pending['coupon_id'] ?? null;
            if ($couponId) {
                $fullUser2 = User::find($userId);
                $plan2     = Plan::find($planId);
                $price     = $cycle === 'annual' ? (float)($plan2['price_annual'] ?? 0) : (float)($plan2['price_monthly'] ?? 0);
                $couponRow2 = Coupon::find((int)$couponId);
                if ($couponRow2) {
                    $discount = Coupon::discountAmount($couponRow2, $price);
                    Coupon::recordUse($couponId, $fullUser2['email'] ?? '', $userId, $discount);
                }
            }

            ActivityLog::log('subscription_activated', "PayPal subscription $subId activated");
            Session::flash('success', 'Subscription activated! Welcome aboard.');
        } catch (Exception $e) {
            error_log('PayPal return error: ' . $e->getMessage());
            Session::flash('error', 'PayPal error: ' . $e->getMessage());
        }

        $this->redirect('/billing');
    }

    public function paypalCancel(): void
    {
        $this->requireAuth();
        Session::forget('paypal_pending');
        Session::flash('error', 'PayPal subscription was cancelled. You can try again any time.');
        $this->redirect('/billing');
    }

    public function cancelSubscription(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        if (!$sub || empty($sub['stripe_subscription_id'])) {
            Session::flash('error', 'No active subscription found.');
            $this->redirect('/billing');
        }

        try {
            $paypal = new PayPalService();
            $paypal->cancelSubscription($sub['stripe_subscription_id']);
            Subscription::update($sub['id'], [
                'status'      => 'canceled',
                'canceled_at' => date('Y-m-d H:i:s'),
            ]);
            ActivityLog::log('subscription_canceled', 'PayPal subscription canceled');
            Session::flash('success', 'Your subscription has been canceled.');
        } catch (Exception $e) {
            error_log('PayPal cancel error: ' . $e->getMessage());
            Session::flash('error', 'PayPal error: ' . $e->getMessage());
        }

        $this->redirect('/billing');
    }

    public function paypalWebhook(): void
    {
        $payload = file_get_contents('php://input');
        if (!$payload) { http_response_code(400); return; }

        // Collect PayPal headers
        $ppHeaders = [];
        foreach ($_SERVER as $k => $v) {
            if (str_starts_with($k, 'HTTP_PAYPAL_')) {
                $header = strtolower(str_replace('HTTP_', '', $k));
                $header = str_replace('_', '-', $header);
                $ppHeaders[$header] = $v;
            }
        }

        try {
            $paypal = new PayPalService();
            if (!$paypal->verifyWebhook($payload, $ppHeaders)) {
                error_log('PayPal webhook: signature verification failed');
                http_response_code(400);
                echo json_encode(['error' => 'Signature verification failed']);
                return;
            }

            $event    = json_decode($payload, true);
            $type     = $event['event_type'] ?? '';
            $resource = $event['resource'] ?? [];

            match($type) {
                'BILLING.SUBSCRIPTION.ACTIVATED'  => $this->ppHandleActivated($resource),
                'BILLING.SUBSCRIPTION.UPDATED'    => $this->ppHandleUpdated($resource),
                'BILLING.SUBSCRIPTION.CANCELLED'  => $this->ppHandleCancelled($resource),
                'BILLING.SUBSCRIPTION.EXPIRED'    => $this->ppHandleCancelled($resource),
                'PAYMENT.SALE.COMPLETED'          => $this->ppHandlePayment($resource),
                default                           => null,
            };
        } catch (Exception $e) {
            error_log('PayPal webhook error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Handler error']);
            return;
        }

        http_response_code(200);
        echo json_encode(['received' => true]);
    }

    private function ppHandleActivated(array $res): void
    {
        $subId = $res['id'] ?? '';
        if (!$subId) return;
        $sub = Subscription::findByStripeId($subId);
        if ($sub) {
            Subscription::update($sub['id'], ['status' => 'active']);
        }
    }

    private function ppHandleUpdated(array $res): void
    {
        $subId = $res['id'] ?? '';
        if (!$subId) return;
        $sub = Subscription::findByStripeId($subId);
        if (!$sub) return;

        $status = strtolower($res['status'] ?? 'active');
        $nextBilling = $res['billing_info']['next_billing_time'] ?? null;
        $updates = ['status' => $status];
        if ($nextBilling) $updates['current_period_end'] = date('Y-m-d H:i:s', strtotime($nextBilling));
        Subscription::update($sub['id'], $updates);
    }

    private function ppHandleCancelled(array $res): void
    {
        $subId = $res['id'] ?? '';
        if (!$subId) return;
        $sub = Subscription::findByStripeId($subId);
        if ($sub) {
            Subscription::update($sub['id'], [
                'status'      => 'canceled',
                'canceled_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function ppHandlePayment(array $res): void
    {
        $billingAgreementId = $res['billing_agreement_id'] ?? '';
        if (!$billingAgreementId) return;
        $sub = Subscription::findByStripeId($billingAgreementId);
        if (!$sub) return;

        $amount   = $res['amount']['total'] ?? '0';
        $currency = strtoupper($res['amount']['currency'] ?? 'AUD');
        $txnId    = $res['id'] ?? null;
        $paidAt   = date('Y-m-d H:i:s');

        Payment::create([
            'user_id'           => $sub['user_id'],
            'subscription_id'   => $sub['id'],
            'stripe_invoice_id' => $txnId,
            'amount'            => (float)$amount,
            'currency'          => $currency,
            'status'            => 'succeeded',
            'description'       => 'PayPal subscription payment',
            'paid_at'           => $paidAt,
        ]);

        $user = User::find($sub['user_id']);
        $plan = Plan::find((int)($sub['plan_id'] ?? 0));
        if ($user && $plan) {
            try {
                Mailer::sendPaymentInvoice($user, [
                    'amount'            => (float)$amount,
                    'currency'          => $currency,
                    'stripe_invoice_id' => $txnId,
                    'paid_at'           => $paidAt,
                ], $plan['name'], $sub['billing_cycle'] ?? 'monthly');
            } catch (Exception $e) {
                error_log('Invoice email error: ' . $e->getMessage());
            }
        }
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

        $sub = Subscription::forUser($channel['user_id']);
        if (!Subscription::isActive($sub)) {
            http_response_code(403);
            $this->view('display/offline', ['title' => 'Subscription Expired'], null);
            return;
        }

        $slides = $this->resolveSlides($channel, $sub);

        if (empty($slides)) {
            $this->view('display/empty', ['title' => 'No Content', 'channel' => $channel], null);
            return;
        }

        Channel::touchDisplayed($channel['id']);

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

        $slides = $this->resolveSlides($channel, $sub);

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
                'rotation' => (int)($s['rotation'] ?? 0),
            ], $slides),
            'updated_at' => date('c'),
        ]);
    }

    /**
     * Returns the slide list for the player: scheduled media when a Pro schedule
     * is active right now, otherwise the channel's default slides.
     */
    private function resolveSlides(array $channel, ?array $sub): array
    {
        if (!empty($sub['scheduling_enabled'])) {
            $schedule = ContentSchedule::activeForChannel($channel['id']);
            if ($schedule) {
                $mediaIds = json_decode($schedule['media_ids'], true) ?: [];
                if (!empty($mediaIds)) {
                    $placeholders = implode(',', array_fill(0, count($mediaIds), '?'));
                    $rows  = Database::fetchAll(
                        "SELECT * FROM media WHERE id IN ($placeholders)",
                        $mediaIds
                    );
                    $byId  = array_column($rows, null, 'id');
                    // Preserve schedule-defined order
                    $slides = array_values(array_filter(
                        array_map(fn($id) => $byId[$id] ?? null, $mediaIds)
                    ));
                    if (!empty($slides)) return $slides;
                }
            }
        }

        return Slide::forChannel($channel['id']);
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

        $priority = in_array($_POST['priority'] ?? '', ['low','medium','high']) ? $_POST['priority'] : 'medium';
        $ticketId = SupportTicket::create($user['id'], [
            'subject'  => $subject,
            'priority' => $priority,
        ]);
        SupportTicket::addReply($ticketId, $user['id'], $message, false);

        $ticket   = SupportTicket::find($ticketId);
        $fullUser = User::find($user['id']);
        if ($ticket && $fullUser) {
            try { Mailer::sendTicketCreated($fullUser, $ticket, $message); } catch (Exception $e) { error_log('Ticket email error: ' . $e->getMessage()); }
        }

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

        $isAdminReply = $user['role'] === 'admin';
        SupportTicket::addReply((int)$id, $user['id'], $message, $isAdminReply);

        $fullUser = User::find($user['id']);
        if ($fullUser) {
            try { Mailer::sendTicketReply($ticket, $fullUser, $message, $isAdminReply); } catch (Exception $e) { error_log('Reply email error: ' . $e->getMessage()); }
        }

        Session::flash('success', 'Reply sent.');
        $this->redirect('/support/' . $id);
    }
}
