<?php
/**
 * Settings Model
 */
class Settings extends BaseModel
{
    protected static string $table = 'settings';

    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!isset(self::$cache[$key])) {
            $row = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', [$key]);
            self::$cache[$key] = $row ? $row['value'] : null;
        }
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        Database::execute(
            'INSERT INTO settings (`key`, `value`, `group`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `value` = ?, `group` = ?',
            [$key, $value, $group, $value, $group]
        );
        self::$cache[$key] = $value;
    }

    public static function getGroup(string $group): array
    {
        $rows = Database::fetchAll('SELECT `key`, `value` FROM settings WHERE `group` = ?', [$group]);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    public static function setMany(array $data, string $group = 'general'): void
    {
        foreach ($data as $key => $value) {
            self::set($key, $value, $group);
        }
    }

    public static function stripeKey(): string
    {
        $mode = self::get('stripe_mode', 'test');
        return $mode === 'live'
            ? self::get('stripe_live_sk', '')
            : self::get('stripe_test_sk', '');
    }

    public static function stripePublicKey(): string
    {
        $mode = self::get('stripe_mode', 'test');
        return $mode === 'live'
            ? self::get('stripe_live_pk', '')
            : self::get('stripe_test_pk', '');
    }

    public static function storageDriver(): string
    {
        return self::get('storage_driver', 'local');
    }

    public static function isLiveMode(): bool
    {
        return self::get('stripe_mode', 'test') === 'live';
    }
}

/**
 * User Model
 */
class User extends BaseModel
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE email = ? LIMIT 1', [strtolower($email)]);
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO users (name, email, password, role, status) VALUES (?,?,?,?,?)',
            [
                $data['name'],
                strtolower($data['email']),
                Helpers::hashPassword($data['password']),
                $data['role'] ?? 'customer',
                $data['status'] ?? 'pending',
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        $sets   = [];
        $params = [];
        foreach ($data as $col => $val) {
            $sets[]   = '`' . $col . '` = ?';
            $params[] = $val;
        }
        $params[] = $id;
        Database::execute('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    public static function updatePassword(int $id, string $newPassword): void
    {
        Database::execute(
            'UPDATE users SET password = ? WHERE id = ?',
            [Helpers::hashPassword($newPassword), $id]
        );
    }

    public static function verifyEmail(int $id): void
    {
        Database::execute(
            'UPDATE users SET email_verified_at = NOW(), status = "active" WHERE id = ?',
            [$id]
        );
    }

    public static function suspend(int $id): void
    {
        Database::execute('UPDATE users SET status = "suspended" WHERE id = ?', [$id]);
    }

    public static function activate(int $id): void
    {
        Database::execute('UPDATE users SET status = "active" WHERE id = ?', [$id]);
    }

    public static function countByRole(string $role): int
    {
        return self::count(['role' => $role]);
    }

    public static function getWithSubscription(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT u.*, p.name as plan_name, s.status as sub_status, s.trial_ends_at,
                    s.current_period_end, s.stripe_customer_id
             FROM users u
             LEFT JOIN subscriptions s ON s.user_id = u.id
             LEFT JOIN plans p ON p.id = s.plan_id
             WHERE u.id = ?
             ORDER BY s.id DESC
             LIMIT 1',
            [$id]
        );
    }

    public static function recentSignups(int $limit = 10): array
    {
        return Database::fetchAll(
            'SELECT u.*, p.name as plan_name, s.status as sub_status
             FROM users u
             LEFT JOIN subscriptions s ON s.user_id = u.id
             LEFT JOIN plans p ON p.id = s.plan_id
             WHERE u.role = "customer"
             ORDER BY u.created_at DESC
             LIMIT ?',
            [$limit]
        );
    }

    public static function allWithSubscription(int $page = 1, int $perPage = 20, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where  = "WHERE u.role = 'customer'";
        if ($search) {
            $where   .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        $params[] = $perPage;
        $params[] = $offset;
        return Database::fetchAll(
            "SELECT u.*, p.name as plan_name, s.status as sub_status
             FROM users u
             LEFT JOIN subscriptions s ON s.user_id = u.id AND s.id = (
                 SELECT MAX(id) FROM subscriptions WHERE user_id = u.id
             )
             LEFT JOIN plans p ON p.id = s.plan_id
             $where
             ORDER BY u.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }
}
