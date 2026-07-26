<?php
/**
 * InstallerController – web-based setup wizard
 * Handles /install routes. Redirects to /login if install.lock exists.
 */
class InstallerController extends BaseController
{
    private string $lockFile;

    public function __construct()
    {
        $this->lockFile = ROOT_PATH . '/install/install.lock';
    }

    public function index(): void
    {
        if (file_exists($this->lockFile)) {
            $this->redirect('/login');
        }
        $this->view('installer/index', ['title' => 'Install DisplayNex'], 'installer');
    }

    public function step(string $step): void
    {
        if (file_exists($this->lockFile)) $this->redirect('/login');
        $safe = preg_replace('/[^a-z0-9\-]/', '', strtolower($step));
        $this->view('installer/index', ['title' => 'Install – Step ' . $safe], 'installer');
    }

    public function process(): void
    {
        if (file_exists($this->lockFile)) {
            $this->json(['success' => false, 'message' => 'Already installed.']);
            return;
        }

        $action = $_POST['action'] ?? '';

        match($action) {
            'test_db'       => $this->testDatabase(),
            'run_migration' => $this->runMigration(),
            'create_admin'  => $this->createAdmin(),
            'save_smtp'     => $this->saveSmtp(),
            'save_stripe'   => $this->saveStripe(),
            'finish'        => $this->finish(),
            default         => $this->json(['success' => false, 'message' => 'Unknown action.'], 400),
        };
    }

    // ── Steps ─────────────────────────────────────────────────────────────────

    private function testDatabase(): void
    {
        $host = trim($_POST['db_host'] ?? 'localhost');
        $port = trim($_POST['db_port'] ?? '3306');
        $name = trim($_POST['db_name'] ?? '');
        $user = trim($_POST['db_user'] ?? '');
        $pass = $_POST['db_pass'] ?? '';

        if (!$name || !$user) {
            $this->json(['success' => false, 'message' => 'Database name and username are required.']);
            return;
        }

        try {
            new PDO(
                "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
            );

            // Write .env
            $env  = "DB_HOST=$host\n";
            $env .= "DB_PORT=$port\n";
            $env .= "DB_NAME=$name\n";
            $env .= "DB_USER=$user\n";
            $env .= "DB_PASS=$pass\n";
            file_put_contents(ROOT_PATH . '/.env', $env);

            $this->json(['success' => true, 'message' => 'Connected! Database credentials saved.']);
        } catch (PDOException $e) {
            $this->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        }
    }

    private function runMigration(): void
    {
        $schemaFile = ROOT_PATH . '/install/schema.sql';

        if (!file_exists($schemaFile)) {
            $this->json(['success' => false, 'message' => 'Schema file not found at install/schema.sql']);
            return;
        }

        try {
            $db  = Database::getInstance();
            $sql = file_get_contents($schemaFile);

            // Split on semicolons, skip empty / comment-only lines
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                fn($s) => strlen($s) > 5
            );

            foreach ($statements as $stmt) {
                $db->exec($stmt);
            }

            $this->json(['success' => true, 'message' => 'All tables created and default data seeded!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Migration failed: ' . $e->getMessage()]);
        }
    }

    private function createAdmin(): void
    {
        $name     = Helpers::sanitize($_POST['name'] ?? '');
        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$name || !Helpers::validateEmail($email) || strlen($password) < 8) {
            $this->json(['success' => false, 'message' => 'All fields required. Password must be at least 8 characters.']);
            return;
        }

        try {
            if (User::findByEmail($email)) {
                $this->json(['success' => false, 'message' => 'An account with this email already exists.']);
                return;
            }

            $userId = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
                'role'     => 'admin',
                'status'   => 'active',
            ]);

            User::verifyEmail($userId); // No email verification needed for admin
            $this->json(['success' => true, 'message' => 'Admin account created successfully!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()]);
        }
    }

    private function saveSmtp(): void
    {
        try {
            $encryption = in_array($_POST['smtp_encryption'] ?? 'tls', ['tls', 'ssl', 'none'])
                ? $_POST['smtp_encryption']
                : 'tls';

            Settings::setMany([
                'smtp_host'       => Helpers::sanitize($_POST['smtp_host'] ?? ''),
                'smtp_port'       => (int)($_POST['smtp_port'] ?? 587),
                'smtp_user'       => Helpers::sanitize($_POST['smtp_user'] ?? ''),
                'smtp_pass'       => $_POST['smtp_pass'] ?? '',
                'smtp_from_email' => Helpers::sanitize($_POST['smtp_from_email'] ?? ''),
                'smtp_from_name'  => Helpers::sanitize($_POST['smtp_from_name'] ?? 'DisplayNex'),
                'smtp_encryption' => $encryption,
            ]);

            $this->json(['success' => true, 'message' => 'SMTP settings saved!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function saveStripe(): void
    {
        try {
            Settings::setMany([
                'stripe_mode'           => 'test',
                'stripe_test_pk'        => Helpers::sanitize($_POST['stripe_test_pk'] ?? ''),
                'stripe_test_sk'        => Helpers::sanitize($_POST['stripe_test_sk'] ?? ''),
                'stripe_live_pk'        => '',
                'stripe_live_sk'        => '',
                'stripe_webhook_secret' => Helpers::sanitize($_POST['webhook_secret'] ?? ''),
            ]);

            $this->json(['success' => true, 'message' => 'Stripe keys saved!']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function finish(): void
    {
        try {
            Settings::setMany([
                'company_name' => Helpers::sanitize($_POST['company_name'] ?? 'DisplayNex'),
                'company_url'  => Helpers::sanitize($_POST['company_url'] ?? ''),
            ]);

            // Write lock file – prevents re-running the installer
            file_put_contents(
                $this->lockFile,
                date('Y-m-d H:i:s') . " Installation completed.\n"
            );

            $this->json([
                'success'  => true,
                'message'  => 'Installation complete! Redirecting to login…',
                'redirect' => '/login',
            ]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

/**
 * Error Controller
 */
class ErrorController extends BaseController
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', ['title' => 'Page Not Found'], 'marketing');
    }
}
