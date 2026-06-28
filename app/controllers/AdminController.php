<?php
/**
 * Admin Controller
 */
class AdminController extends BaseController
{
    public function dashboard(): void
    {
        $this->requireAdmin();
        $totalCustomers  = User::countByRole('customer');
        $activeSubCount  = Subscription::countActive();
        $monthlyRevenue  = Subscription::monthlyRevenue();
        $totalChannels   = Channel::totalCount();
        $recentSignups   = User::recentSignups(8);
        $openTickets     = SupportTicket::count(['status' => 'open']);

        $this->view('admin/dashboard', [
            'title'          => 'Admin Dashboard',
            'totalCustomers' => $totalCustomers,
            'activeSubCount' => $activeSubCount,
            'monthlyRevenue' => $monthlyRevenue,
            'totalChannels'  => $totalChannels,
            'recentSignups'  => $recentSignups,
            'openTickets'    => $openTickets,
        ], 'admin');
    }

    // ── Customers ──────────────────────────────────────────────────────────

    public function customers(): void
    {
        $this->requireAdmin();
        $search    = Helpers::sanitize($_GET['q'] ?? '');
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $customers = User::allWithSubscription($page, 20, $search);

        $this->view('admin/customers', [
            'title'     => 'Customers',
            'customers' => $customers,
            'search'    => $search,
            'page'      => $page,
        ], 'admin');
    }

    public function viewCustomer(string $id): void
    {
        $this->requireAdmin();
        $user     = User::getWithSubscription((int)$id);
        if (!$user) $this->abort(404);
        $channels = Channel::forUser((int)$id);
        $payments = Payment::forUser((int)$id);
        $tickets  = SupportTicket::forUser((int)$id);

        $this->view('admin/customer-detail', [
            'title'    => $user['name'],
            'customer' => $user,
            'channels' => $channels,
            'payments' => $payments,
            'tickets'  => $tickets,
        ], 'admin');
    }

    public function suspendCustomer(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        User::suspend((int)$id);
        ActivityLog::log('admin_suspend_user', "Suspended user #$id");
        Session::flash('success', 'Customer suspended.');
        $this->redirect('/admin/customers');
    }

    public function activateCustomer(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        User::activate((int)$id);
        ActivityLog::log('admin_activate_user', "Activated user #$id");
        Session::flash('success', 'Customer activated.');
        $this->redirect('/admin/customers');
    }

    public function deleteCustomer(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $user = User::find((int)$id);
        if ($user && $user['role'] !== 'admin') {
            User::delete((int)$id);
            ActivityLog::log('admin_delete_user', "Deleted user #$id ({$user['email']})");
            Session::flash('success', 'Customer deleted.');
        }
        $this->redirect('/admin/customers');
    }

    public function resetCustomerPassword(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $user = User::find((int)$id);
        if (!$user) $this->abort(404);

        $token = Helpers::generateToken(32);
        Database::execute('DELETE FROM password_resets WHERE user_id = ?', [$id]);
        Database::insert(
            'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)',
            [(int)$id, $token, date('Y-m-d H:i:s', strtotime('+24 hours'))]
        );
        Mailer::sendPasswordReset($user, $token);
        ActivityLog::log('admin_reset_password', "Sent password reset for user #$id");
        Session::flash('success', 'Password reset email sent to customer.');
        $this->redirect('/admin/customers/' . $id);
    }

    // ── Plans ──────────────────────────────────────────────────────────────

    public function plans(): void
    {
        $this->requireAdmin();
        $plans = Plan::all();
        $this->view('admin/plans', ['title' => 'Plans', 'plans' => $plans], 'admin');
    }

    public function editPlan(string $id): void
    {
        $this->requireAdmin();
        $plan = Plan::find((int)$id);
        if (!$plan) $this->abort(404);
        $this->view('admin/plan-edit', ['title' => 'Edit Plan', 'plan' => $plan], 'admin');
    }

    public function updatePlan(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $plan = Plan::find((int)$id);
        if (!$plan) $this->abort(404);

        // Auto-add optional columns if they don't exist yet
        if (!Database::fetchOne("SHOW COLUMNS FROM plans LIKE 'max_slides'")) {
            try { Database::execute('ALTER TABLE plans ADD COLUMN max_slides TINYINT UNSIGNED NOT NULL DEFAULT 15'); } catch (Exception $e) { error_log('max_slides add: ' . $e->getMessage()); }
        }
        if (!Database::fetchOne("SHOW COLUMNS FROM plans LIKE 'has_trial'")) {
            try { Database::execute('ALTER TABLE plans ADD COLUMN has_trial TINYINT UNSIGNED NOT NULL DEFAULT 1'); } catch (Exception $e) { error_log('has_trial add: ' . $e->getMessage()); }
        }

        Database::execute(
            'UPDATE plans SET name=?, price_monthly=?, price_annual=?, max_screens=?, max_slides=?,
             max_storage_mb=?, stripe_price_id_monthly=?, stripe_price_id_annual=?, has_trial=?, is_active=? WHERE id=?',
            [
                Helpers::sanitize($_POST['name'] ?? ''),
                (float)($_POST['price_monthly'] ?? 0),
                (float)($_POST['price_annual'] ?? 0),
                (int)($_POST['max_screens'] ?? 1),
                max(1, min(50, (int)($_POST['max_slides'] ?? 15))),
                max(100, (int)($_POST['max_storage_mb'] ?? 512)),
                Helpers::sanitize($_POST['stripe_price_id_monthly'] ?? ''),
                Helpers::sanitize($_POST['stripe_price_id_annual'] ?? ''),
                isset($_POST['has_trial']) ? 1 : 0,
                isset($_POST['is_active']) ? 1 : 0,
                (int)$id,
            ]
        );
        Session::flash('success', 'Plan updated.');
        $this->redirect('/admin/plans');
    }

    public function createPlan(): void
    {
        $this->requireAdmin();
        $this->view('admin/plan-create', ['title' => 'Create Plan'], 'admin');
    }

    public function storePlan(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $name = trim(Helpers::sanitize($_POST['name'] ?? ''));
        if ($name === '') {
            Session::flash('error', 'Plan name is required.');
            $this->redirect('/admin/plans/create');
        }

        // Auto-add optional columns if they don't exist yet
        if (!Database::fetchOne("SHOW COLUMNS FROM plans LIKE 'max_slides'")) {
            try { Database::execute('ALTER TABLE plans ADD COLUMN max_slides TINYINT UNSIGNED NOT NULL DEFAULT 15'); } catch (Exception $e) { error_log('max_slides add: ' . $e->getMessage()); }
        }
        if (!Database::fetchOne("SHOW COLUMNS FROM plans LIKE 'has_trial'")) {
            try { Database::execute('ALTER TABLE plans ADD COLUMN has_trial TINYINT UNSIGNED NOT NULL DEFAULT 1'); } catch (Exception $e) { error_log('has_trial add: ' . $e->getMessage()); }
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));

        Database::insert(
            'INSERT INTO plans (name, slug, price_monthly, price_annual, max_screens, max_slides,
             max_storage_mb, stripe_price_id_monthly, stripe_price_id_annual, has_trial, is_active)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)',
            [
                $name,
                $slug,
                (float)($_POST['price_monthly'] ?? 0),
                (float)($_POST['price_annual'] ?? 0),
                (int)($_POST['max_screens'] ?? 1),
                max(1, min(50, (int)($_POST['max_slides'] ?? 15))),
                max(100, (int)($_POST['max_storage_mb'] ?? 512)),
                Helpers::sanitize($_POST['stripe_price_id_monthly'] ?? ''),
                Helpers::sanitize($_POST['stripe_price_id_annual'] ?? ''),
                isset($_POST['has_trial']) ? 1 : 0,
                isset($_POST['is_active']) ? 1 : 0,
            ]
        );

        ActivityLog::log('admin_create_plan', "Created plan: $name");
        Session::flash('success', 'Plan created successfully.');
        $this->redirect('/admin/plans');
    }

    public function deletePlan(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $plan = Plan::find((int)$id);
        if (!$plan) $this->abort(404);

        $activeSubscribers = Database::fetchOne(
            "SELECT COUNT(*) as cnt FROM subscriptions WHERE plan_id = ? AND status = 'active'",
            [(int)$id]
        );
        if ($activeSubscribers && $activeSubscribers['cnt'] > 0) {
            Session::flash('error', 'Cannot delete a plan with active subscribers. Deactivate it instead.');
            $this->redirect('/admin/plans');
        }

        Database::execute('DELETE FROM plans WHERE id = ?', [(int)$id]);
        ActivityLog::log('admin_delete_plan', "Deleted plan #{$id}: {$plan['name']}");
        Session::flash('success', 'Plan deleted.');
        $this->redirect('/admin/plans');
    }

    // ── Support ────────────────────────────────────────────────────────────

    public function tickets(): void
    {
        $this->requireAdmin();
        $status  = $_GET['status'] ?? '';
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $tickets = SupportTicket::allWithUser($status, $page, 20);

        $this->view('admin/tickets', [
            'title'   => 'Support Tickets',
            'tickets' => $tickets,
            'status'  => $status,
        ], 'admin');
    }

    public function closeTicket(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        Database::execute(
            "UPDATE support_tickets SET status='closed', closed_at=NOW() WHERE id=?", [(int)$id]
        );
        Session::flash('success', 'Ticket closed.');
        $this->redirect('/admin/tickets');
    }

    public function reopenTicket(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        Database::execute(
            "UPDATE support_tickets SET status='open', closed_at=NULL WHERE id=?", [(int)$id]
        );
        Session::flash('success', 'Ticket reopened.');
        $this->redirect('/admin/tickets/' . $id);
    }

    // ── Settings ───────────────────────────────────────────────────────────

    public function settings(): void
    {
        $this->requireAdmin();
        $settings = [
            'general'     => Settings::getGroup('general'),
            'mail'        => Settings::getGroup('mail'),
            'paypal'      => Settings::getGroup('paypal'),
            'storage'     => Settings::getGroup('storage'),
            'maintenance' => Settings::getGroup('maintenance'),
            'media'       => Settings::getGroup('media'),
            'seo'         => Settings::getGroup('seo'),
            'social'      => Settings::getGroup('social'),
        ];
        $this->view('admin/settings', ['title' => 'System Settings', 'settings' => $settings], 'admin');
    }

    public function saveSettings(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $group = Helpers::sanitize($_POST['group'] ?? 'general');

        $allowed = [
            'general'     => ['company_name','company_url','app_url','app_env'],
            'mail'        => ['smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_email','smtp_from_name','smtp_encryption'],
            'paypal'      => ['paypal_mode','paypal_client_id','paypal_secret','paypal_webhook_id'],
            'storage'     => ['storage_driver','s3_bucket','s3_region','s3_access_key','s3_secret_key','s3_url',
                              'r2_bucket','r2_account_id','r2_access_key','r2_secret_key','r2_url'],
            'media'       => ['max_upload_size_kb'],
            'maintenance' => ['maintenance_mode','maintenance_message'],
            'seo'         => ['ga_measurement_id','gsc_verification','meta_pixel_code'],
            'social'      => ['facebook_url','instagram_url','google_business_url'],
        ];

        if (!isset($allowed[$group])) $this->abort(400, 'Invalid settings group.');

        // Checkbox fields default to '0' if not submitted
        $checkboxFields = ['maintenance_mode'];
        foreach ($allowed[$group] as $key) {
            if (in_array($key, $checkboxFields)) {
                Settings::set($key, isset($_POST[$key]) ? '1' : '0', $group);
            } elseif (isset($_POST[$key])) {
                // meta_pixel_code contains raw HTML/JS — skip strip_tags
                if ($key === 'meta_pixel_code') {
                    $val = trim($_POST[$key]);
                } else {
                    $val = Helpers::sanitize($_POST[$key]);
                    // If the user pasted the full <meta> tag instead of just the content value,
                    // strip_tags() in sanitize() would have erased it — re-extract from the raw input.
                    if ($key === 'gsc_verification' && $val === '') {
                        if (preg_match('/content=["\']([^"\']+)["\']/', $_POST[$key], $m)) {
                            $val = trim($m[1]);
                        }
                    }
                }
                Settings::set($key, $val, $group);
            }
        }

        ActivityLog::log('admin_settings_saved', "Saved $group settings");
        Session::flash('success', 'Settings saved.');
        $this->redirect('/admin/settings#' . $group);
    }

    // ── Test Email ─────────────────────────────────────────────────────────

    public function testEmail(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $to = Helpers::sanitize($_POST['test_email'] ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('/admin/settings#mail');
        }

        $host = Settings::get('smtp_host', '');
        $user = Settings::get('smtp_user', '');
        $pass = Settings::get('smtp_pass', '');
        $port = (int) Settings::get('smtp_port', 587);
        $enc  = Settings::get('smtp_encryption', 'tls');
        $from = Settings::get('smtp_from_email', '');

        if (!$host || !$user || !$from) {
            Session::flash('error', 'SMTP settings are incomplete. Fill in Host, Username, and From Email first.');
            $this->redirect('/admin/settings#mail');
        }

        $company = Settings::get('company_name', APP_NAME);

        try {
            $smtp = new SmtpClient($host, $port, $enc, $user, $pass);
            $smtp->sendMail(
                $from, $company,
                $to, $to,
                "Test Email from $company",
                "<p>This is a test email sent from <strong>$company</strong> via your SMTP settings.</p>
                 <p>If you received this, your email configuration is working correctly.</p>
                 <p style='color:#6b7280;font-size:13px'>Host: $host &nbsp;|&nbsp; Port: $port &nbsp;|&nbsp; Encryption: $enc</p>"
            );
            Session::flash('success', "Test email sent successfully to $to. Check your inbox.");
        } catch (Exception $e) {
            Session::flash('error', 'SMTP error: ' . $e->getMessage());
        }

        $this->redirect('/admin/settings#mail');
    }

    // ── Logo ───────────────────────────────────────────────────────────────

    public function uploadLogo(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        if (empty($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/settings#branding');
        }

        $file = $_FILES['logo'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/settings#branding');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg' => true, 'jpeg' => true, 'png' => true, 'webp' => true];

        if (!in_array($file['type'], $allowedMimes) || !isset($extMap[$ext])) {
            Session::flash('error', 'Only JPG, PNG, and WebP files are allowed.');
            $this->redirect('/admin/settings#branding');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Session::flash('error', 'Logo must be under 2MB.');
            $this->redirect('/admin/settings#branding');
        }

        // Delete old logo file
        $oldLogo = Settings::get('company_logo', '');
        if ($oldLogo && str_starts_with($oldLogo, '/uploads/logo/')) {
            $oldPath = PUBLIC_PATH . $oldLogo;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $logoDir = UPLOAD_PATH . '/logo/';
        if (!is_dir($logoDir)) mkdir($logoDir, 0755, true);

        $storedName = 'logo_' . time() . '.' . $ext;
        $dest       = $logoDir . $storedName;

        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dest)) {
            Session::flash('error', 'Failed to save logo. Check directory permissions.');
            $this->redirect('/admin/settings#branding');
        }

        Settings::set('company_logo', '/uploads/logo/' . $storedName);
        ActivityLog::log('admin_settings_saved', 'Updated company logo');
        Session::flash('success', 'Logo updated successfully.');
        $this->redirect('/admin/settings#branding');
    }

    public function removeLogo(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $oldLogo = Settings::get('company_logo', '');
        if ($oldLogo && str_starts_with($oldLogo, '/uploads/logo/')) {
            $oldPath = PUBLIC_PATH . $oldLogo;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set('company_logo', '');
        ActivityLog::log('admin_settings_saved', 'Removed company logo');
        Session::flash('success', 'Logo removed.');
        $this->redirect('/admin/settings#branding');
    }

    // ── Favicon ────────────────────────────────────────────────────────────

    public function uploadFavicon(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        if (empty($_FILES['favicon']) || $_FILES['favicon']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/settings#branding');
        }

        $file = $_FILES['favicon'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/settings#branding');
        }

        $ext          = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedMimes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/svg+xml'];
        $extMap       = ['ico' => true, 'png' => true, 'svg' => true];

        if (!in_array($file['type'], $allowedMimes) || !isset($extMap[$ext])) {
            Session::flash('error', 'Only ICO, PNG, and SVG files are allowed.');
            $this->redirect('/admin/settings#branding');
        }

        if ($file['size'] > 1024 * 1024) {
            Session::flash('error', 'Favicon must be under 1MB.');
            $this->redirect('/admin/settings#branding');
        }

        $oldFavicon = Settings::get('site_favicon', '');
        if ($oldFavicon && str_starts_with($oldFavicon, '/uploads/favicon/')) {
            $oldPath = PUBLIC_PATH . $oldFavicon;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $faviconDir = UPLOAD_PATH . '/favicon/';
        if (!is_dir($faviconDir)) mkdir($faviconDir, 0755, true);

        $storedName = 'favicon_' . time() . '.' . $ext;
        $dest       = $faviconDir . $storedName;

        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dest)) {
            Session::flash('error', 'Failed to save favicon. Check directory permissions.');
            $this->redirect('/admin/settings#branding');
        }

        Settings::set('site_favicon', '/uploads/favicon/' . $storedName);
        ActivityLog::log('admin_settings_saved', 'Updated site favicon');
        Session::flash('success', 'Favicon updated successfully.');
        $this->redirect('/admin/settings#branding');
    }

    public function removeFavicon(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $oldFavicon = Settings::get('site_favicon', '');
        if ($oldFavicon && str_starts_with($oldFavicon, '/uploads/favicon/')) {
            $oldPath = PUBLIC_PATH . $oldFavicon;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set('site_favicon', '');
        ActivityLog::log('admin_settings_saved', 'Removed site favicon');
        Session::flash('success', 'Favicon removed.');
        $this->redirect('/admin/settings#branding');
    }

    // ── Revenue ────────────────────────────────────────────────────────────

    public function revenue(): void
    {
        $this->requireAdmin();
        $payments = Database::fetchAll(
            'SELECT p.*, u.name as user_name, u.email as user_email
             FROM payments p
             JOIN users u ON u.id = p.user_id
             WHERE p.status = "succeeded"
             ORDER BY p.created_at DESC
             LIMIT 100'
        );
        $total = Database::fetchOne('SELECT COALESCE(SUM(amount),0) as t FROM payments WHERE status="succeeded"')['t'];

        $this->view('admin/revenue', [
            'title'    => 'Revenue',
            'payments' => $payments,
            'total'    => $total,
        ], 'admin');
    }

    // ── Coupons ────────────────────────────────────────────────────────────

    public function coupons(): void
    {
        $this->requireAdmin();
        try {
            $coupons = Coupon::all('created_at DESC');
        } catch (Exception $e) {
            // Table likely not created yet
            Session::flash('error', 'Coupon tables not found. Please run install/coupon_migration.sql on your database first. Error: ' . $e->getMessage());
            $coupons = [];
        }
        $this->view('admin/coupons', [
            'title'   => 'Coupon Codes',
            'coupons' => $coupons,
        ], 'admin');
    }

    public function createCoupon(): void
    {
        $this->requireAdmin();
        $plans = Plan::active();
        $this->view('admin/coupon-form', [
            'title'  => 'Create Coupon',
            'coupon' => null,
            'plans'  => $plans,
        ], 'admin');
    }

    public function storeCoupon(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $code      = strtoupper(preg_replace('/[^A-Za-z0-9\-_]/', '', $_POST['code'] ?? ''));
        $type      = in_array($_POST['discount_type'] ?? '', ['percentage', 'fixed']) ? $_POST['discount_type'] : 'percentage';
        $value     = max(0.01, (float)($_POST['discount_value'] ?? 0));
        $maxUses   = ($_POST['max_uses'] ?? '') !== '' ? max(1, (int)$_POST['max_uses']) : null;
        $expiresAt = ($_POST['expires_at'] ?? '') !== '' ? date('Y-m-d H:i:s', strtotime($_POST['expires_at'])) : null;
        $appliesTo = implode(',', array_map('trim', (array)($_POST['applies_to'] ?? ['all'])));
        $onePerCust = isset($_POST['one_per_customer']) ? 1 : 0;
        $isActive   = isset($_POST['is_active']) ? 1 : 0;
        $desc       = Helpers::sanitize($_POST['description'] ?? '');

        if (!$code) {
            Session::flash('error', 'Coupon code is required.');
            $this->redirect('/admin/coupons/create');
        }
        if (Coupon::findByCode($code)) {
            Session::flash('error', "Coupon code \"$code\" already exists.");
            $this->redirect('/admin/coupons/create');
        }
        if ($type === 'percentage' && $value > 100) {
            Session::flash('error', 'Percentage discount cannot exceed 100%.');
            $this->redirect('/admin/coupons/create');
        }

        Database::insert(
            'INSERT INTO coupons (code, description, discount_type, discount_value, applies_to, max_uses, one_per_customer, expires_at, is_active)
             VALUES (?,?,?,?,?,?,?,?,?)',
            [$code, $desc, $type, $value, $appliesTo, $maxUses, $onePerCust, $expiresAt, $isActive]
        );

        ActivityLog::log('admin_create_coupon', "Created coupon: $code");
        Session::flash('success', "Coupon \"$code\" created.");
        $this->redirect('/admin/coupons');
    }

    public function editCoupon(string $id): void
    {
        $this->requireAdmin();
        $coupon = Coupon::find((int)$id);
        if (!$coupon) $this->abort(404);
        $plans = Plan::active();
        $this->view('admin/coupon-form', [
            'title'  => 'Edit Coupon',
            'coupon' => $coupon,
            'plans'  => $plans,
        ], 'admin');
    }

    public function updateCoupon(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $coupon = Coupon::find((int)$id);
        if (!$coupon) $this->abort(404);

        $type      = in_array($_POST['discount_type'] ?? '', ['percentage', 'fixed']) ? $_POST['discount_type'] : 'percentage';
        $value     = max(0.01, (float)($_POST['discount_value'] ?? 0));
        $maxUses   = ($_POST['max_uses'] ?? '') !== '' ? max(1, (int)$_POST['max_uses']) : null;
        $expiresAt = ($_POST['expires_at'] ?? '') !== '' ? date('Y-m-d H:i:s', strtotime($_POST['expires_at'])) : null;
        $appliesTo = implode(',', array_map('trim', (array)($_POST['applies_to'] ?? ['all'])));
        $onePerCust = isset($_POST['one_per_customer']) ? 1 : 0;
        $isActive   = isset($_POST['is_active']) ? 1 : 0;
        $desc       = Helpers::sanitize($_POST['description'] ?? '');

        if ($type === 'percentage' && $value > 100) {
            Session::flash('error', 'Percentage discount cannot exceed 100%.');
            $this->redirect("/admin/coupons/{$id}/edit");
        }

        Database::execute(
            'UPDATE coupons SET description=?, discount_type=?, discount_value=?, applies_to=?, max_uses=?,
             one_per_customer=?, expires_at=?, is_active=? WHERE id=?',
            [$desc, $type, $value, $appliesTo, $maxUses, $onePerCust, $expiresAt, $isActive, (int)$id]
        );

        ActivityLog::log('admin_update_coupon', "Updated coupon #{$id}");
        Session::flash('success', 'Coupon updated.');
        $this->redirect('/admin/coupons');
    }

    public function deleteCoupon(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $coupon = Coupon::find((int)$id);
        if (!$coupon) $this->abort(404);
        Database::execute('DELETE FROM coupons WHERE id = ?', [(int)$id]);
        ActivityLog::log('admin_delete_coupon', "Deleted coupon: {$coupon['code']}");
        Session::flash('success', 'Coupon deleted.');
        $this->redirect('/admin/coupons');
    }

    public function toggleCoupon(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $coupon = Coupon::find((int)$id);
        if (!$coupon) $this->abort(404);
        $newState = $coupon['is_active'] ? 0 : 1;
        Database::execute('UPDATE coupons SET is_active = ? WHERE id = ?', [$newState, (int)$id]);
        Session::flash('success', $newState ? 'Coupon activated.' : 'Coupon deactivated.');
        $this->redirect('/admin/coupons');
    }
}
