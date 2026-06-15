<?php
/**
 * SignageCloud – Application Configuration
 */

define('APP_NAME',    'SignageCloud');
define('APP_VERSION', '1.0.0');
define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('VIEWS_PATH',  APP_PATH . '/views');

// ── Load .env ────────────────────────────────────────────────────────────────
$envFile = ROOT_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$key, $val] = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($val));
        }
    }
}

// ── Database ──────────────────────────────────────────────────────────────────
define('DB_HOST',    getenv('DB_HOST') ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT') ?: '3306');
define('DB_NAME',    getenv('DB_NAME') ?: 'signagecloud');
define('DB_USER',    getenv('DB_USER') ?: 'root');
define('DB_PASS',    getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ── Session ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'sc_session');
define('SESSION_LIFETIME', 7200);

// ── Security ──────────────────────────────────────────────────────────────────
define('CSRF_TOKEN_NAME',       '_csrf_token');
define('PASSWORD_MIN_LENGTH',   8);
define('MAX_LOGIN_ATTEMPTS',    5);
define('LOGIN_LOCKOUT_MINUTES', 15);

// ── Media ─────────────────────────────────────────────────────────────────────
define('MAX_UPLOAD_BYTES',   5242880); // 5 MB
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// ── Display ───────────────────────────────────────────────────────────────────
define('DISPLAY_SLUG_LENGTH', 10);

// ── Trial ─────────────────────────────────────────────────────────────────────
define('TRIAL_DAYS', 14);

// ── Rate Limiting ─────────────────────────────────────────────────────────────
define('RATE_LIMIT_LOGIN',    10);
define('RATE_LIMIT_REGISTER', 5);
define('RATE_LIMIT_WINDOW',   900);

// ── Timezone ──────────────────────────────────────────────────────────────────
date_default_timezone_set('UTC');

// ── Error Reporting ───────────────────────────────────────────────────────────
$logDir = STORAGE_PATH . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', $logDir . '/php_errors.log');
