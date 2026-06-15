<?php
/**
 * Stripe Service
 * Handles all Stripe API calls using raw cURL (no SDK required)
 */
class StripeService
{
    private string $secretKey;
    private string $apiBase = 'https://api.stripe.com/v1/';

    public function __construct(?string $secretKey = null)
    {
        $this->secretKey = $secretKey ?? Settings::stripeKey();
    }

    private function request(string $method, string $endpoint, array $data = []): array
    {
        $ch = curl_init();
        $url = $this->apiBase . ltrim($endpoint, '/');
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_USERPWD        => $this->secretKey . ':',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => ['Stripe-Version: 2023-10-16'],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($method === 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Stripe cURL error');
        }
        $decoded = json_decode($response, true);
        if (!$decoded) {
            throw new Exception('Invalid Stripe response');
        }
        if (isset($decoded['error'])) {
            throw new Exception($decoded['error']['message'] ?? 'Stripe error', $httpCode);
        }
        return $decoded;
    }

    public function createCustomer(string $email, string $name): array
    {
        return $this->request('POST', 'customers', ['email' => $email, 'name' => $name]);
    }

    public function createCheckoutSession(array $params): array
    {
        return $this->request('POST', 'checkout/sessions', $params);
    }

    public function createSubscription(string $customerId, string $priceId, bool $trial = true): array
    {
        $data = [
            'customer' => $customerId,
            'items'    => [['price' => $priceId]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings[save_default_payment_method]' => 'on_subscription',
            'expand[]' => 'latest_invoice.payment_intent',
        ];
        if ($trial) {
            $data['trial_period_days'] = TRIAL_DAYS;
        }
        return $this->request('POST', 'subscriptions', $data);
    }

    public function retrieveSubscription(string $subId): array
    {
        return $this->request('GET', 'subscriptions/' . $subId);
    }

    public function cancelSubscription(string $subId, bool $atPeriodEnd = true): array
    {
        if ($atPeriodEnd) {
            return $this->request('POST', 'subscriptions/' . $subId, ['cancel_at_period_end' => 'true']);
        }
        return $this->request('DELETE', 'subscriptions/' . $subId);
    }

    public function updateSubscription(string $subId, string $newPriceId): array
    {
        $sub = $this->retrieveSubscription($subId);
        $itemId = $sub['items']['data'][0]['id'] ?? null;
        if (!$itemId) throw new Exception('Cannot find subscription item');

        return $this->request('POST', 'subscriptions/' . $subId, [
            'items[0][id]'    => $itemId,
            'items[0][price]' => $newPriceId,
            'proration_behavior' => 'create_prorations',
        ]);
    }

    public function retrieveInvoice(string $invoiceId): array
    {
        return $this->request('GET', 'invoices/' . $invoiceId);
    }

    public function listInvoices(string $customerId): array
    {
        return $this->request('GET', 'invoices', ['customer' => $customerId, 'limit' => 20]);
    }

    public function createBillingPortalSession(string $customerId, string $returnUrl): array
    {
        return $this->request('POST', 'billing_portal/sessions', [
            'customer'   => $customerId,
            'return_url' => $returnUrl,
        ]);
    }

    public function constructWebhookEvent(string $payload, string $sigHeader, string $webhookSecret): array
    {
        // Verify Stripe signature
        $parts     = explode(',', $sigHeader);
        $timestamp = null;
        $signatures = [];
        foreach ($parts as $part) {
            if (strncmp($part, 't=', 2) === 0) $timestamp = substr($part, 2);
            if (strncmp($part, 'v1=', 3) === 0) $signatures[] = substr($part, 3);
        }
        if (!$timestamp || empty($signatures)) {
            throw new Exception('Invalid Stripe signature header');
        }
        if (abs(time() - (int)$timestamp) > 300) {
            throw new Exception('Stripe webhook timestamp too old');
        }
        $signedPayload = $timestamp . '.' . $payload;
        $expected      = hash_hmac('sha256', $signedPayload, $webhookSecret);
        $verified = false;
        foreach ($signatures as $sig) {
            if (hash_equals($expected, $sig)) { $verified = true; break; }
        }
        if (!$verified) throw new Exception('Stripe signature verification failed');

        $event = json_decode($payload, true);
        if (!$event) throw new Exception('Invalid webhook payload');
        return $event;
    }
}

/**
 * Mailer Service using PHPMailer
 * Falls back gracefully if PHPMailer is not available
 */
class Mailer
{
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            // Check if PHPMailer is available
            $phpmailerPath = ROOT_PATH . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
            if (!file_exists($phpmailerPath)) {
                // Fallback to PHP mail()
                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $headers .= "From: " . Settings::get('smtp_from_name') . " <" . Settings::get('smtp_from_email') . ">\r\n";
                return mail($toEmail, $subject, $htmlBody, $headers);
            }

            require_once $phpmailerPath;
            require_once ROOT_PATH . '/vendor/phpmailer/phpmailer/src/SMTP.php';
            require_once ROOT_PATH . '/vendor/phpmailer/phpmailer/src/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = Settings::get('smtp_host', 'localhost');
            $mail->SMTPAuth   = true;
            $mail->Username   = Settings::get('smtp_user');
            $mail->Password   = Settings::get('smtp_pass');
            $mail->SMTPSecure = Settings::get('smtp_encryption', 'tls');
            $mail->Port       = (int) Settings::get('smtp_port', 587);
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(Settings::get('smtp_from_email', 'noreply@localhost'), Settings::get('smtp_from_name', APP_NAME));
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            return $mail->send();
        } catch (Exception $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendVerification(array $user, string $token): bool
    {
        $url     = Helpers::baseUrl('verify-email/' . $token);
        $company = Settings::get('company_name', APP_NAME);
        $body    = self::emailTemplate(
            "Verify Your Email Address",
            "<p>Hi {$user['name']},</p>
            <p>Welcome to $company! Please verify your email address to get started.</p>
            <p style='text-align:center;margin:32px 0'>
                <a href='$url' style='background:#6366f1;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Verify Email Address</a>
            </p>
            <p style='color:#6b7280;font-size:14px'>If you didn't create an account, you can ignore this email. The link expires in 24 hours.</p>"
        );
        return self::send($user['email'], $user['name'], "Verify your $company account", $body);
    }

    public static function sendPasswordReset(array $user, string $token): bool
    {
        $url     = Helpers::baseUrl('reset-password/' . $token);
        $company = Settings::get('company_name', APP_NAME);
        $body    = self::emailTemplate(
            "Reset Your Password",
            "<p>Hi {$user['name']},</p>
            <p>We received a request to reset your $company password.</p>
            <p style='text-align:center;margin:32px 0'>
                <a href='$url' style='background:#6366f1;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Reset Password</a>
            </p>
            <p style='color:#6b7280;font-size:14px'>This link expires in 1 hour. If you didn't request a reset, you can safely ignore this email.</p>"
        );
        return self::send($user['email'], $user['name'], "Reset your $company password", $body);
    }

    public static function sendWelcome(array $user): bool
    {
        $company  = Settings::get('company_name', APP_NAME);
        $dashUrl  = Helpers::baseUrl('dashboard');
        $body     = self::emailTemplate(
            "Welcome to $company!",
            "<p>Hi {$user['name']},</p>
            <p>Your account is ready. Your 14-day free trial has started — no credit card required yet.</p>
            <p style='text-align:center;margin:32px 0'>
                <a href='$dashUrl' style='background:#6366f1;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Go to Dashboard</a>
            </p>
            <p>Questions? Reply to this email — we're happy to help.</p>"
        );
        return self::send($user['email'], $user['name'], "Welcome to $company", $body);
    }

    private static function emailTemplate(string $title, string $content): string
    {
        $company  = Settings::get('company_name', APP_NAME);
        $logoPath = Settings::get('company_logo', '');
        $logoHtml = $logoPath
            ? '<div style="background:#ffffff;padding:20px 32px;border-bottom:1px solid #e5e7eb;text-align:left">
                 <img src="' . Helpers::baseUrl(ltrim($logoPath, '/')) . '" alt="' . htmlspecialchars($company) . '" style="max-height:44px;max-width:180px;object-fit:contain;display:block">
               </div>'
            : '<div style="background:#6366f1;padding:24px 32px">
                 <h1 style="margin:0;color:#fff;font-size:20px;font-weight:700">' . htmlspecialchars($company) . '</h1>
               </div>';

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>$title</title></head>
<body style="margin:0;padding:0;background:#f9fafb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif">
  <div style="max-width:560px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1)">
    $logoHtml
    <div style="padding:32px">$content</div>
    <div style="padding:16px 32px;background:#f9fafb;border-top:1px solid #e5e7eb">
      <p style="margin:0;color:#9ca3af;font-size:12px">© {$company}. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
HTML;
    }
}
