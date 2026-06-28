<?php
class AuthController extends BaseController
{
    public function showLogin(): void
    {
        if (Session::get('user')) $this->redirect('/dashboard');
        $this->view('auth/login', ['title' => 'Sign In'], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $ip       = Helpers::getIp();

        if (RateLimiter::tooManyAttempts('login_' . $ip, MAX_LOGIN_ATTEMPTS, RATE_LIMIT_WINDOW)) {
            Session::flash('error', 'Too many login attempts. Please wait 15 minutes and try again.');
            $this->redirect('/login');
        }

        if (!Helpers::validateEmail($email) || empty($password)) {
            Session::flash('error', 'Please enter a valid email and password.');
            $this->redirect('/login');
        }

        $user = User::findByEmail($email);
        if (!$user || !Helpers::verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        if ($user['status'] === 'suspended') {
            Session::flash('error', 'Your account has been suspended. Please contact support.');
            $this->redirect('/login');
        }

        if (!$user['email_verified_at']) {
            Session::flash('error', 'Please verify your email address before logging in. Check your inbox.');
            $this->redirect('/login');
        }

        RateLimiter::clearAttempts('login_' . $ip);
        Session::regenerate();
        Session::set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        User::update($user['id'], ['last_login_at' => date('Y-m-d H:i:s'), 'last_login_ip' => $ip]);
        ActivityLog::log('login', 'User logged in', $user['id']);

        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        } else {
            $this->redirect('/dashboard');
        }
    }

    public function showRegister(): void
    {
        if (Session::get('user')) $this->redirect('/dashboard');
        $plans = Plan::active();
        $this->view('auth/register', ['title' => 'Create Account', 'plans' => $plans], 'auth');
    }

    public function register(): void
    {
        $this->validateCsrf();

        $name         = Helpers::sanitize($_POST['name'] ?? '');
        $email        = strtolower(trim($_POST['email'] ?? ''));
        $password     = $_POST['password'] ?? '';
        $planSlug     = $_POST['plan'] ?? 'starter';
        $billingCycle = ($_POST['cycle'] ?? 'monthly') === 'annual' ? 'annual' : 'monthly';

        // Validation
        $errors = [];
        if (strlen($name) < 2) $errors[] = 'Name must be at least 2 characters.';
        if (!Helpers::validateEmail($email)) $errors[] = 'Please enter a valid email address.';
        if (strlen($password) < PASSWORD_MIN_LENGTH) $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        if (User::findByEmail($email)) $errors[] = 'An account with this email already exists.';

        if ($errors) {
            Session::flash('error', implode(' ', $errors));
            Session::flash('old', ['name' => $name, 'email' => $email]);
            $qs = http_build_query(array_filter(['plan' => $planSlug, 'cycle' => $billingCycle]));
            $this->redirect('/register' . ($qs ? '?' . $qs : ''));
        }

        $plan = Plan::findBySlug($planSlug) ?? Plan::findBySlug('starter');

        // Validate coupon if provided
        $couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));
        $couponRow  = null;
        if ($couponCode) {
            [$couponRow, $couponError] = Coupon::check($couponCode, $email, $plan['slug']);
            if (!$couponRow) {
                Session::flash('error', "Coupon error: $couponError");
                Session::flash('old', ['name' => $name, 'email' => $email]);
                $qs = http_build_query(array_filter(['plan' => $planSlug, 'cycle' => $billingCycle]));
                $this->redirect('/register' . ($qs ? '?' . $qs : ''));
            }
        }

        Database::beginTransaction();
        try {
            $userId = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'role'     => 'customer',
                'status'   => 'pending',
            ]);

            // Create trial subscription
            $trialEnd = date('Y-m-d H:i:s', strtotime('+' . TRIAL_DAYS . ' days'));
            Subscription::create([
                'user_id'        => $userId,
                'plan_id'        => $plan['id'],
                'status'         => 'trialing',
                'billing_cycle'  => $billingCycle,
                'trial_ends_at'  => $trialEnd,
            ]);

            // Email verification token
            $token = Helpers::generateToken(32);
            Database::insert(
                'INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?,?,?)',
                [$userId, $token, date('Y-m-d H:i:s', strtotime('+24 hours'))]
            );

            // Record coupon use if applied
            if ($couponRow) {
                $price    = $billingCycle === 'annual' ? (float)$plan['price_annual'] : (float)$plan['price_monthly'];
                $discount = Coupon::discountAmount($couponRow, $price);
                Coupon::recordUse($couponRow['id'], $email, $userId, $discount);
            }

            Database::commit();

            $user = User::find($userId);
            Mailer::sendVerification($user, $token);
            $couponNote = $couponRow ? " (coupon: {$couponRow['code']})" : '';
            ActivityLog::log('register', "New customer registered: $email$couponNote", $userId);

            Session::flash('success', 'Account created! Please check your email to verify your address.');
            $this->redirect('/login');
        } catch (Exception $e) {
            Database::rollback();
            error_log('Registration error: ' . $e->getMessage());
            Session::flash('error', 'Registration failed. Please try again.');
            $this->redirect('/register');
        }
    }

    public function verifyEmail(string $token): void
    {
        // Query without used_at in WHERE — works even if the column doesn't exist yet
        $row = Database::fetchOne(
            'SELECT * FROM email_verifications WHERE token = ? AND expires_at > NOW()',
            [$token]
        );

        if (!$row) {
            Session::flash('error', 'Verification link is invalid or has expired.');
            $this->redirect('/login');
        }

        // If used_at column exists and is already set, the token was already used
        if (isset($row['used_at']) && $row['used_at'] !== null) {
            Session::flash('error', 'This verification link has already been used. Please sign in.');
            $this->redirect('/login');
        }

        Database::beginTransaction();
        try {
            User::verifyEmail($row['user_id']);

            // Mark token as used — silently skip if column doesn't exist yet
            try {
                Database::execute(
                    'UPDATE email_verifications SET used_at = NOW() WHERE id = ?',
                    [$row['id']]
                );
            } catch (Exception $e) {
                // Column not yet added — verification still succeeds
                error_log('email_verifications.used_at missing. Run: ALTER TABLE email_verifications ADD COLUMN used_at TIMESTAMP NULL DEFAULT NULL;');
            }

            Database::commit();

            $user = User::find($row['user_id']);
            Mailer::sendWelcome($user);

            Session::flash('success', 'Email verified! You can now sign in.');
        } catch (Exception $e) {
            Database::rollback();
            error_log('verifyEmail error: ' . $e->getMessage());
            Session::flash('error', 'Verification failed. Please try again.');
        }

        $this->redirect('/login');
    }

    public function showForgotPassword(): void
    {
        $this->view('auth/forgot-password', ['title' => 'Forgot Password'], 'auth');
    }

    public function forgotPassword(): void
    {
        $this->validateCsrf();
        $email = strtolower(trim($_POST['email'] ?? ''));

        // Always show success to prevent email enumeration
        Session::flash('success', 'If an account exists with that email, a reset link has been sent.');

        if (!Helpers::validateEmail($email)) {
            $this->redirect('/forgot-password');
        }

        $user = User::findByEmail($email);
        if ($user) {
            $token = Helpers::generateToken(32);
            Database::execute(
                'DELETE FROM password_resets WHERE user_id = ?', [$user['id']]
            );
            Database::insert(
                'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)',
                [$user['id'], $token, date('Y-m-d H:i:s', strtotime('+1 hour'))]
            );
            Mailer::sendPasswordReset($user, $token);
        }

        $this->redirect('/forgot-password');
    }

    public function showResetPassword(string $token): void
    {
        $row = Database::fetchOne(
            'SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL',
            [$token]
        );
        if (!$row) {
            Session::flash('error', 'Password reset link is invalid or has expired.');
            $this->redirect('/forgot-password');
        }
        $this->view('auth/reset-password', ['title' => 'Reset Password', 'token' => $token], 'auth');
    }

    public function resetPassword(): void
    {
        $this->validateCsrf();
        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $row = Database::fetchOne(
            'SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL',
            [$token]
        );
        if (!$row) {
            Session::flash('error', 'Reset link is invalid or has expired.');
            $this->redirect('/forgot-password');
        }

        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            Session::flash('error', 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.');
            $this->redirect('/reset-password/' . $token);
        }
        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('/reset-password/' . $token);
        }

        User::updatePassword($row['user_id'], $password);
        Database::execute(
            'UPDATE password_resets SET used_at = NOW() WHERE id = ?', [$row['id']]
        );
        ActivityLog::log('password_reset', 'Password reset completed', $row['user_id']);

        Session::flash('success', 'Password updated successfully. You can now sign in.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        ActivityLog::log('logout', 'User logged out');
        Session::destroy();
        $this->redirect('/login');
    }
}
