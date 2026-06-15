<?php
/**
 * Session helper
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                ini_set('session.cookie_secure', '1');
            }
            session_name(SESSION_NAME);
            session_set_cookie_params(['lifetime' => SESSION_LIFETIME]);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $val = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}

/**
 * CSRF protection
 */
class Csrf
{
    public static function token(): string
    {
        if (!Session::has(CSRF_TOKEN_NAME)) {
            Session::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
        return Session::get(CSRF_TOKEN_NAME);
    }

    public static function verify(string $token): bool
    {
        return hash_equals(Session::get(CSRF_TOKEN_NAME, ''), $token);
    }

    public static function refresh(): void
    {
        Session::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
    }
}

/**
 * General utility functions
 */
class Helpers
{
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public static function e(string $val): string
    {
        return htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function generateToken(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    public static function generateSlug(int $length = 10): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $slug  = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $slug;
    }

    public static function uniqueChannelSlug(): string
    {
        do {
            $slug = self::generateSlug(DISPLAY_SLUG_LENGTH);
            $exists = Database::fetchOne('SELECT id FROM channels WHERE slug = ?', [$slug]);
        } while ($exists);
        return $slug;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)   return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)      return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public static function formatMoney(float $amount, string $currency = 'AUD'): string
    {
        return '$' . number_format($amount, 2) . ' ' . $currency;
    }

    public static function timeAgo(string $datetime): string
    {
        $diff = time() - strtotime($datetime);
        if ($diff < 60)     return $diff . 's ago';
        if ($diff < 3600)   return floor($diff / 60) . 'm ago';
        if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
        return floor($diff / 86400) . 'd ago';
    }

    public static function getIp(): string
    {
        return $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function baseUrl(string $path = ''): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }

    public static function displayUrl(string $slug): string
    {
        return self::baseUrl('display/' . $slug);
    }

    public static function generateTicketNumber(): string
    {
        return 'TKT-' . strtoupper(substr(uniqid(), -6));
    }

    public static function validateEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Rate Limiter (database-free, uses session + apc/file fallback)
 */
class RateLimiter
{
    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $cacheKey  = 'rl_' . md5($key);
        $data      = Session::get($cacheKey, ['count' => 0, 'reset_at' => 0]);
        if (time() > $data['reset_at']) {
            $data = ['count' => 0, 'reset_at' => time() + $decaySeconds];
        }
        $data['count']++;
        Session::set($cacheKey, $data);
        return $data['count'] > $maxAttempts;
    }

    public static function clearAttempts(string $key): void
    {
        Session::forget('rl_' . md5($key));
    }
}

/**
 * Activity Logger
 */
class ActivityLog
{
    public static function log(string $action, string $description = '', ?int $userId = null): void
    {
        try {
            Database::insert(
                'INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) VALUES (?,?,?,?,?)',
                [
                    $userId ?? (Session::get('user')['id'] ?? null),
                    $action,
                    $description,
                    Helpers::getIp(),
                    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                ]
            );
        } catch (Exception $e) {
            // Non-fatal; don't break the app
            error_log('ActivityLog error: ' . $e->getMessage());
        }
    }
}
