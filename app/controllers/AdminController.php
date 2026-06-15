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

        Database::execute(
            'UPDATE plans SET name=?, price_monthly=?, price_annual=?, max_screens=?,
             stripe_price_id_monthly=?, stripe_price_id_annual=?, is_active=? WHERE id=?',
            [
                Helpers::sanitize($_POST['name'] ?? ''),
                (float)($_POST['price_monthly'] ?? 0),
                (float)($_POST['price_annual'] ?? 0),
                (int)($_POST['max_screens'] ?? 1),
                Helpers::sanitize($_POST['stripe_price_id_monthly'] ?? ''),
                Helpers::sanitize($_POST['stripe_price_id_annual'] ?? ''),
                isset($_POST['is_active']) ? 1 : 0,
                (int)$id,
            ]
        );
        Session::flash('success', 'Plan updated.');
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
            'general' => Settings::getGroup('general'),
            'mail'    => Settings::getGroup('mail'),
            'stripe'  => Settings::getGroup('stripe'),
            'storage' => Settings::getGroup('storage'),
            'maintenance' => Settings::getGroup('maintenance'),
            'media'   => Settings::getGroup('media'),
        ];
        $this->view('admin/settings', ['title' => 'System Settings', 'settings' => $settings], 'admin');
    }

    public function saveSettings(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $group = Helpers::sanitize($_POST['group'] ?? 'general');

        $allowed = [
            'general' => ['company_name','company_url','app_env'],
            'mail'    => ['smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_email','smtp_from_name','smtp_encryption'],
            'stripe'  => ['stripe_mode','stripe_test_pk','stripe_test_sk','stripe_live_pk','stripe_live_sk','stripe_webhook_secret'],
            'storage' => ['storage_driver','s3_bucket','s3_region','s3_access_key','s3_secret_key','s3_url',
                          'r2_bucket','r2_account_id','r2_access_key','r2_secret_key','r2_url'],
            'media'   => ['max_upload_size_kb'],
            'maintenance' => ['maintenance_mode','maintenance_message'],
        ];

        if (!isset($allowed[$group])) $this->abort(400, 'Invalid settings group.');

        // Checkbox fields default to '0' if not submitted
        $checkboxFields = ['maintenance_mode'];
        foreach ($allowed[$group] as $key) {
            if (in_array($key, $checkboxFields)) {
                Settings::set($key, isset($_POST[$key]) ? '1' : '0');
            } elseif (isset($_POST[$key])) {
                Settings::set($key, Helpers::sanitize($_POST[$key]));
            }
        }

        ActivityLog::log('admin_settings_saved', "Saved $group settings");
        Session::flash('success', 'Settings saved.');
        $this->redirect('/admin/settings#' . $group);
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
}
