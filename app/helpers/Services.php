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
 * PayPal Subscriptions REST API — no SDK required.
 * Stores PayPal IDs in stripe_* columns to avoid DB migration.
 */
class PayPalService
{
    private string $clientId;
    private string $secret;
    private string $base;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = Settings::get('paypal_client_id', '');
        $this->secret   = Settings::get('paypal_secret', '');
        $mode           = Settings::get('paypal_mode', 'sandbox');
        $this->base     = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function curlExec($ch): string
    {
        $res     = curl_exec($ch);
        $errNo   = curl_errno($ch);
        $errStr  = curl_error($ch);
        $code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res === false) {
            throw new Exception("PayPal cURL error ($errNo): $errStr");
        }
        // Store HTTP code for callers that need it
        $this->lastHttpCode = $code;
        return (string)$res;
    }

    private int $lastHttpCode = 0;

    private function token(): string
    {
        if ($this->accessToken) return $this->accessToken;

        if (!$this->clientId || !$this->secret) {
            throw new Exception('PayPal Client ID or Secret is not configured. Please check Settings → PayPal.');
        }

        $ch = curl_init($this->base . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_USERPWD        => $this->clientId . ':' . $this->secret,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded', 'Accept: application/json'],
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $res  = $this->curlExec($ch);
        $data = json_decode($res, true);

        if (!isset($data['access_token'])) {
            $desc = $data['error_description'] ?? ($data['message'] ?? $res);
            throw new Exception('PayPal auth failed: ' . $desc);
        }
        $this->accessToken = $data['access_token'];
        return $this->accessToken;
    }

    private function request(string $method, string $path, array $body = []): array
    {
        $ch = curl_init($this->base . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->token(),
                'Content-Type: application/json',
                'Accept: application/json',
                'Prefer: return=representation',
            ],
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);
        if ($method !== 'GET' && !empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $res  = $this->curlExec($ch);
        $code = $this->lastHttpCode;

        if ($code === 204) return ['status' => 'ok'];

        $data = json_decode($res, true);
        if (!is_array($data)) {
            throw new Exception("PayPal API: unexpected response (HTTP $code): " . substr($res, 0, 200));
        }
        if ($code >= 400) {
            $msg = $data['message'] ?? ($data['error_description'] ?? ($data['name'] ?? "HTTP $code"));
            $details = '';
            if (!empty($data['details'])) {
                $details = ' — ' . ($data['details'][0]['description'] ?? json_encode($data['details'][0]));
            }
            throw new Exception("PayPal: $msg$details");
        }
        return $data;
    }

    public function createSubscription(string $paypalPlanId, array $user, string $returnUrl, string $cancelUrl): string
    {
        $data = $this->request('POST', '/v1/billing/subscriptions', [
            'plan_id'             => $paypalPlanId,
            'application_context' => [
                'brand_name'          => Settings::get('company_name', 'DisplayNex'),
                'locale'              => 'en-US',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action'         => 'SUBSCRIBE_NOW',
                'return_url'          => $returnUrl,
                'cancel_url'          => $cancelUrl,
            ],
        ]);

        foreach ($data['links'] ?? [] as $link) {
            if ($link['rel'] === 'approve') return $link['href'];
        }
        throw new Exception('PayPal returned no approval URL. Check that the PayPal Plan ID is correct and the plan is ACTIVE in your PayPal dashboard.');
    }

    public function getSubscription(string $subId): array
    {
        return $this->request('GET', '/v1/billing/subscriptions/' . $subId);
    }

    public function cancelSubscription(string $subId, string $reason = 'Customer requested cancellation'): void
    {
        $this->request('POST', '/v1/billing/subscriptions/' . $subId . '/cancel', ['reason' => $reason]);
    }

    public function verifyWebhook(string $payload, array $headers): bool
    {
        $webhookId = Settings::get('paypal_webhook_id', '');
        if (!$webhookId) return false;

        try {
            $result = $this->request('POST', '/v1/notifications/verify-webhook-signature', [
                'auth_algo'         => $headers['paypal-auth-algo']    ?? $headers['PAYPAL-AUTH-ALGO'] ?? '',
                'cert_url'          => $headers['paypal-cert-url']     ?? $headers['PAYPAL-CERT-URL'] ?? '',
                'transmission_id'   => $headers['paypal-transmission-id']  ?? $headers['PAYPAL-TRANSMISSION-ID'] ?? '',
                'transmission_sig'  => $headers['paypal-transmission-sig'] ?? $headers['PAYPAL-TRANSMISSION-SIG'] ?? '',
                'transmission_time' => $headers['paypal-transmission-time'] ?? $headers['PAYPAL-TRANSMISSION-TIME'] ?? '',
                'webhook_id'        => $webhookId,
                'webhook_event'     => json_decode($payload, true),
            ]);
            return ($result['verification_status'] ?? '') === 'SUCCESS';
        } catch (Exception $e) {
            error_log('PayPal webhook verify error: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * PayHere hosted-checkout gateway — one-time payments only (no recurring/preapproval API).
 * Separate from PayPal; used as an alternate checkout option.
 */
class PayHereService
{
    private string $merchantId;
    private string $merchantSecret;
    private string $mode;

    public function __construct()
    {
        $this->merchantId     = Settings::get('payhere_merchant_id', '');
        $this->merchantSecret = Settings::get('payhere_merchant_secret', '');
        $this->mode           = Settings::get('payhere_mode', 'sandbox');
    }

    public function isConfigured(): bool
    {
        return $this->merchantId !== '' && $this->merchantSecret !== '';
    }

    public function merchantId(): string
    {
        return $this->merchantId;
    }

    public function isSandbox(): bool
    {
        return $this->mode === 'sandbox';
    }

    public function checkoutUrl(): string
    {
        return $this->mode === 'live'
            ? 'https://www.payhere.lk/pay/checkout'
            : 'https://sandbox.payhere.lk/pay/checkout';
    }

    // PayHere checkout hash: MD5(merchant_id + order_id + amount + currency + MD5(merchant_secret)), uppercased
    public function hash(string $orderId, string $amountFormatted, string $currency): string
    {
        $secretHash = strtoupper(md5($this->merchantSecret));
        return strtoupper(md5($this->merchantId . $orderId . $amountFormatted . $currency . $secretHash));
    }

    // Verifies the server-to-server notify_url POST using the same hash formula plus status_code
    public function verifyNotify(array $post): bool
    {
        if (!$this->isConfigured()) return false;
        if (($post['merchant_id'] ?? '') !== $this->merchantId) return false;

        $secretHash = strtoupper(md5($this->merchantSecret));
        $localSig = strtoupper(md5(
            $this->merchantId .
            ($post['order_id'] ?? '') .
            ($post['payhere_amount'] ?? '') .
            ($post['payhere_currency'] ?? '') .
            ($post['status_code'] ?? '') .
            $secretHash
        ));

        return hash_equals($localSig, strtoupper($post['md5sig'] ?? ''));
    }
}

/**
 * Lightweight SMTP client — no external dependencies required.
 * Supports SSL (port 465) and STARTTLS (port 587).
 */
class SmtpClient
{
    private $sock;

    public function __construct(
        private string $host,
        private int    $port,
        private string $enc,   // 'ssl' | 'tls' | 'none'
        private string $user,
        private string $pass
    ) {}

    public function sendMail(
        string $fromEmail, string $fromName,
        string $toEmail,   string $toName,
        string $subject,   string $html,   string $text = ''
    ): void {
        $this->connect();
        $this->authenticate();

        $this->put('MAIL FROM:<' . $fromEmail . '>');  $this->expect(250);
        $this->put('RCPT TO:<' . $toEmail . '>');       $this->expect(250);
        $this->put('DATA');                              $this->expect(354);

        $boundary = 'b_' . bin2hex(random_bytes(8));
        $plain    = $text ?: strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html));

        $headers  = 'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $fromEmail . ">\r\n";
        $headers .= 'To: =?UTF-8?B?' . base64_encode($toName) . '?= <' . $toEmail . ">\r\n";
        $headers .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"' . "\r\n";
        $headers .= 'Date: ' . date('r') . "\r\n";

        $body  = "\r\n--" . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($plain));
        $body .= '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($html));
        $body .= '--' . $boundary . "--\r\n";

        // Dot-stuffing: escape lines that are just a dot
        $message = preg_replace('/^\.$/m', '..', $headers . $body);
        fwrite($this->sock, $message);

        $this->put('.');       $this->expect(250);
        $this->put('QUIT');
        fclose($this->sock);
    }

    private function connect(): void
    {
        $transport = ($this->enc === 'ssl') ? 'ssl' : 'tcp';
        $ctx = stream_context_create(['ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ]]);
        $this->sock = @stream_socket_client(
            $transport . '://' . $this->host . ':' . $this->port,
            $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx
        );
        if (!$this->sock) {
            throw new Exception("Cannot connect to {$this->host}:{$this->port} — $errstr ($errno)");
        }
        stream_set_timeout($this->sock, 15);

        $this->expect(220); // server greeting

        $ehlo = gethostname() ?: 'localhost';
        $this->put('EHLO ' . $ehlo);
        $this->readLines(250);

        if ($this->enc === 'tls') {
            $this->put('STARTTLS');
            $this->expect(220);
            if (!stream_socket_enable_crypto($this->sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new Exception('STARTTLS negotiation failed');
            }
            $this->put('EHLO ' . $ehlo);
            $this->readLines(250);
        }
    }

    private function authenticate(): void
    {
        $this->put('AUTH LOGIN');          $this->expect(334);
        $this->put(base64_encode($this->user)); $this->expect(334);
        $this->put(base64_encode($this->pass)); $this->expect(235);
    }

    private function put(string $cmd): void
    {
        fwrite($this->sock, $cmd . "\r\n");
    }

    private function expect(int $code): string
    {
        return $this->readLines($code);
    }

    private function readLines(int $expected): string
    {
        $response = '';
        while (($line = fgets($this->sock, 4096)) !== false) {
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') break; // final line of multi-line reply
        }
        $actual = (int) substr($response, 0, 3);
        if ($actual !== $expected) {
            throw new Exception("SMTP: expected $expected, got $actual — " . trim($response));
        }
        return $response;
    }
}

/**
 * Mailer Service — uses built-in SmtpClient; falls back to PHPMailer if installed.
 */
class Mailer
{
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            $phpmailerPath = ROOT_PATH . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
            if (file_exists($phpmailerPath)) {
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
            }

            // Built-in SMTP client
            $smtp = new SmtpClient(
                Settings::get('smtp_host', 'localhost'),
                (int) Settings::get('smtp_port', 587),
                Settings::get('smtp_encryption', 'tls'),
                Settings::get('smtp_user', ''),
                Settings::get('smtp_pass', '')
            );
            $smtp->sendMail(
                Settings::get('smtp_from_email', 'noreply@localhost'),
                Settings::get('smtp_from_name', APP_NAME),
                $toEmail, $toName, $subject, $htmlBody
            );
            return true;
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
                <a href='$url' style='background:#ec4899;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Verify Email Address</a>
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
                <a href='$url' style='background:#ec4899;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Reset Password</a>
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
            <p>Your account is ready — you're all set to get started, no credit card required.</p>
            <p style='text-align:center;margin:32px 0'>
                <a href='$dashUrl' style='background:#ec4899;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Go to Dashboard</a>
            </p>
            <p>Questions? Reply to this email — we're happy to help.</p>"
        );
        return self::send($user['email'], $user['name'], "Welcome to $company", $body);
    }

    public static function sendPaymentInvoice(array $user, array $payment, string $planName, string $billingCycle): bool
    {
        $company   = Settings::get('company_name', APP_NAME);
        $amount    = number_format((float)($payment['amount'] ?? 0), 2);
        $currency  = strtoupper($payment['currency'] ?? 'AUD');
        $txnId     = $payment['stripe_invoice_id'] ?? 'N/A';
        $paidAt    = !empty($payment['paid_at']) ? date('d M Y', strtotime($payment['paid_at'])) : date('d M Y');
        $cycle     = ucfirst($billingCycle);
        $dashUrl   = Helpers::baseUrl('billing');
        $contactUrl = Helpers::baseUrl('contact');

        $body = self::emailTemplate(
            "Payment Receipt – $planName",
            "<p>Hi {$user['name']},</p>
            <p>Thank you for your payment. Here is your receipt for your $company subscription.</p>
            <div style='background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:24px 0'>
              <table style='width:100%;font-size:14px;border-collapse:collapse'>
                <tr>
                  <td style='padding:6px 0;color:#6b7280;width:140px'>Plan</td>
                  <td style='padding:6px 0;font-weight:600;color:#111827'>$planName ($cycle)</td>
                </tr>
                <tr>
                  <td style='padding:6px 0;color:#6b7280'>Date</td>
                  <td style='padding:6px 0;color:#111827'>$paidAt</td>
                </tr>
                <tr>
                  <td style='padding:6px 0;color:#6b7280'>Transaction ID</td>
                  <td style='padding:6px 0;color:#374151;font-family:monospace;font-size:12px'>$txnId</td>
                </tr>
                <tr style='border-top:2px solid #e5e7eb'>
                  <td style='padding:12px 0 6px;color:#111827;font-weight:700;font-size:16px'>Amount Paid</td>
                  <td style='padding:12px 0 6px;color:#111827;font-weight:700;font-size:18px'>\$$amount $currency</td>
                </tr>
              </table>
            </div>
            <p style='text-align:center;margin:24px 0'>
              <a href='$dashUrl' style='background:#ec4899;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>View Billing History</a>
            </p>
            <p style='color:#6b7280;font-size:13px'>Questions about this payment? <a href='$contactUrl' style='color:#ec4899'>Contact support</a>.</p>"
        );

        return self::send($user['email'], $user['name'], "Payment receipt – $planName – \$$amount $currency", $body);
    }

    public static function sendTicketCreated(array $user, array $ticket, string $message): bool
    {
        $company   = Settings::get('company_name', APP_NAME);
        $ticketUrl = Helpers::baseUrl('support/' . $ticket['id']);
        $subject   = htmlspecialchars($ticket['subject']);
        $msgHtml   = nl2br(htmlspecialchars($message));

        // Notify the user
        $userBody = self::emailTemplate(
            "Support Ticket #{$ticket['ticket_number']} Created",
            "<p>Hi {$user['name']},</p>
             <p>Your support ticket has been received. We'll get back to you as soon as possible.</p>
             <div style='background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:24px 0'>
               <div style='font-weight:600;color:#111827;margin-bottom:8px'>{$subject}</div>
               <div style='color:#4b5563;font-size:14px'>{$msgHtml}</div>
             </div>
             <p style='text-align:center;margin:24px 0'>
               <a href='{$ticketUrl}' style='background:#ec4899;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>View Ticket</a>
             </p>"
        );
        self::send($user['email'], $user['name'], "Support ticket received – #{$ticket['ticket_number']}", $userBody);

        // Notify admin
        $admin = Database::fetchOne("SELECT email, name FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1");
        if ($admin) {
            $adminBody = self::emailTemplate(
                "New Support Ticket #{$ticket['ticket_number']}",
                "<p>A new support ticket has been submitted.</p>
                 <table style='width:100%;font-size:14px;border-collapse:collapse;margin:16px 0'>
                   <tr><td style='padding:6px 0;color:#6b7280;width:120px'>Ticket #</td><td style='padding:6px 0;font-weight:600'>{$ticket['ticket_number']}</td></tr>
                   <tr><td style='padding:6px 0;color:#6b7280'>Customer</td><td style='padding:6px 0'>{$user['name']} &lt;{$user['email']}&gt;</td></tr>
                   <tr><td style='padding:6px 0;color:#6b7280'>Subject</td><td style='padding:6px 0'>{$subject}</td></tr>
                   <tr><td style='padding:6px 0;color:#6b7280'>Priority</td><td style='padding:6px 0'>" . ucfirst($ticket['priority'] ?? 'medium') . "</td></tr>
                 </table>
                 <div style='background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:16px 0'>
                   <div style='color:#4b5563;font-size:14px'>{$msgHtml}</div>
                 </div>
                 <p style='text-align:center;margin:24px 0'>
                   <a href='" . Helpers::baseUrl("admin/tickets/{$ticket['id']}") . "' style='background:#ec4899;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Reply in Admin Panel</a>
                 </p>"
            );
            self::send($admin['email'], $admin['name'], "New ticket #{$ticket['ticket_number']}: {$subject}", $adminBody);
        }

        return true;
    }

    public static function sendTicketReply(array $ticket, array $author, string $message, bool $isAdminReply): bool
    {
        $company = Settings::get('company_name', APP_NAME);
        $msgHtml = nl2br(htmlspecialchars($message));
        $subject = htmlspecialchars($ticket['subject']);

        if ($isAdminReply) {
            // Admin replied → notify the customer
            $customer = Database::fetchOne('SELECT email, name FROM users WHERE id = ? LIMIT 1', [$ticket['user_id']]);
            if (!$customer) return false;

            $ticketUrl = Helpers::baseUrl('support/' . $ticket['id']);
            $body = self::emailTemplate(
                "Reply to Ticket #{$ticket['ticket_number']}",
                "<p>Hi {$customer['name']},</p>
                 <p>The support team has replied to your ticket.</p>
                 <div style='background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:16px;margin:24px 0'>
                   <div style='font-size:12px;color:#ec4899;font-weight:600;margin-bottom:8px'>SUPPORT TEAM REPLY</div>
                   <div style='color:#831843;font-size:14px'>{$msgHtml}</div>
                 </div>
                 <p style='text-align:center;margin:24px 0'>
                   <a href='{$ticketUrl}' style='background:#ec4899;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>View &amp; Reply</a>
                 </p>"
            );
            return self::send($customer['email'], $customer['name'], "Reply on ticket #{$ticket['ticket_number']}: {$subject}", $body);
        } else {
            // Customer replied → notify admin
            $admin = Database::fetchOne("SELECT email, name FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1");
            if (!$admin) return false;

            $customer = Database::fetchOne('SELECT email, name FROM users WHERE id = ? LIMIT 1', [$ticket['user_id']]);
            $customerName = $customer['name'] ?? 'Customer';

            $body = self::emailTemplate(
                "Customer Reply – Ticket #{$ticket['ticket_number']}",
                "<p>A customer has replied to a support ticket.</p>
                 <table style='width:100%;font-size:14px;border-collapse:collapse;margin:16px 0'>
                   <tr><td style='padding:6px 0;color:#6b7280;width:120px'>Ticket #</td><td style='padding:6px 0;font-weight:600'>{$ticket['ticket_number']}</td></tr>
                   <tr><td style='padding:6px 0;color:#6b7280'>From</td><td style='padding:6px 0'>{$customerName}</td></tr>
                   <tr><td style='padding:6px 0;color:#6b7280'>Subject</td><td style='padding:6px 0'>{$subject}</td></tr>
                 </table>
                 <div style='background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:16px 0'>
                   <div style='color:#4b5563;font-size:14px'>{$msgHtml}</div>
                 </div>
                 <p style='text-align:center;margin:24px 0'>
                   <a href='" . Helpers::baseUrl("admin/tickets/{$ticket['id']}") . "' style='background:#ec4899;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block'>Reply in Admin Panel</a>
                 </p>"
            );
            return self::send($admin['email'], $admin['name'], "Customer reply on ticket #{$ticket['ticket_number']}: {$subject}", $body);
        }
    }

    private static function emailTemplate(string $title, string $content): string
    {
        $company  = Settings::get('company_name', APP_NAME);
        $logoPath = Settings::get('company_logo', '');
        $logoHtml = $logoPath
            ? '<div style="background:#ffffff;padding:20px 32px;border-bottom:1px solid #e5e7eb;text-align:left">
                 <img src="' . Helpers::baseUrl(ltrim($logoPath, '/')) . '" alt="' . htmlspecialchars($company) . '" style="max-height:44px;max-width:180px;object-fit:contain;display:block">
               </div>'
            : '<div style="background:#ec4899;padding:24px 32px">
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
