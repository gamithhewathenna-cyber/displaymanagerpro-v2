<?php
/**
 * Plan Model
 */
class Plan extends BaseModel
{
    protected static string $table = 'plans';

    public static function active(): array
    {
        return Database::fetchAll('SELECT * FROM plans WHERE is_active = 1 ORDER BY sort_order ASC');
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function findByStripePrice(string $priceId): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM plans WHERE stripe_price_id_monthly = ? OR stripe_price_id_annual = ? LIMIT 1',
            [$priceId, $priceId]
        );
    }
}

/**
 * Subscription Model
 */
class Subscription extends BaseModel
{
    protected static string $table = 'subscriptions';

    public static function forUser(int $userId): ?array
    {
        return Database::fetchOne(
            'SELECT s.*, p.name as plan_name, p.max_screens,
                    p.max_storage_mb, p.slug as plan_slug
             FROM subscriptions s
             JOIN plans p ON p.id = s.plan_id
             WHERE s.user_id = ?
             ORDER BY s.id DESC
             LIMIT 1',
            [$userId]
        );
    }

    public static function findByStripeId(string $stripeSubId): ?array
    {
        return self::findBy('stripe_subscription_id', $stripeSubId);
    }

    public static function findByStripeCustomer(string $customerId): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM subscriptions WHERE stripe_customer_id = ? ORDER BY id DESC LIMIT 1',
            [$customerId]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO subscriptions (user_id, plan_id, stripe_customer_id, stripe_subscription_id,
             stripe_price_id, billing_cycle, status, trial_ends_at, current_period_start, current_period_end)
             VALUES (?,?,?,?,?,?,?,?,?,?)',
            [
                $data['user_id'],
                $data['plan_id'],
                $data['stripe_customer_id'] ?? null,
                $data['stripe_subscription_id'] ?? null,
                $data['stripe_price_id'] ?? null,
                $data['billing_cycle'] ?? 'monthly',
                $data['status'] ?? 'trialing',
                $data['trial_ends_at'] ?? null,
                $data['current_period_start'] ?? null,
                $data['current_period_end'] ?? null,
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
        Database::execute('UPDATE subscriptions SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    public static function isActive(?array $sub): bool
    {
        if (!$sub) return false;
        return in_array($sub['status'], ['active', 'trialing']);
    }

    public static function isTrialing(?array $sub): bool
    {
        return $sub && $sub['status'] === 'trialing';
    }

    public static function trialDaysLeft(?array $sub): int
    {
        if (!$sub || $sub['status'] !== 'trialing' || !$sub['trial_ends_at']) return 0;
        $diff = strtotime($sub['trial_ends_at']) - time();
        return max(0, (int) ceil($diff / 86400));
    }

    public static function countActive(): int
    {
        return (int) Database::fetchOne(
            "SELECT COUNT(*) as c FROM subscriptions WHERE status IN ('active','trialing')"
        )['c'];
    }

    public static function monthlyRevenue(): float
    {
        return (float) Database::fetchOne(
            "SELECT COALESCE(SUM(p.price_monthly),0) as rev
             FROM subscriptions s
             JOIN plans p ON p.id = s.plan_id
             WHERE s.status = 'active'"
        )['rev'];
    }
}

/**
 * Channel Model
 */
class Channel extends BaseModel
{
    protected static string $table = 'channels';

    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT c.*, 
                    COUNT(DISTINCT sl.id) as slide_count,
                    MAX(sl.created_at) as last_slide_added
             FROM channels c
             LEFT JOIN slides sl ON sl.channel_id = c.id
             WHERE c.user_id = ?
             GROUP BY c.id
             ORDER BY c.created_at DESC',
            [$userId]
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function create(int $userId, array $data): int
    {
        $slug = Helpers::uniqueChannelSlug();
        return Database::insert(
            'INSERT INTO channels (user_id, name, slug, description, orientation, slide_duration,
             transition_effect, auto_refresh_minutes, background_color, is_active)
             VALUES (?,?,?,?,?,?,?,?,?,1)',
            [
                $userId,
                $data['name'],
                $slug,
                $data['description'] ?? '',
                $data['orientation'] ?? 'landscape',
                $data['slide_duration'] ?? 8,
                $data['transition_effect'] ?? 'fade',
                $data['auto_refresh_minutes'] ?? 15,
                $data['background_color'] ?? '#000000',
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['name','description','orientation','slide_duration','transition_effect',
                    'auto_refresh_minutes','background_color','is_active'];
        $sets = $params = [];
        foreach ($data as $col => $val) {
            if (in_array($col, $allowed)) {
                $sets[]   = '`' . $col . '` = ?';
                $params[] = $val;
            }
        }
        if (empty($sets)) return;
        $params[] = $id;
        Database::execute('UPDATE channels SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    public static function countForUser(int $userId): int
    {
        return self::count(['user_id' => $userId]);
    }

    public static function userOwns(int $channelId, int $userId): bool
    {
        return (bool) Database::fetchOne(
            'SELECT id FROM channels WHERE id = ? AND user_id = ?', [$channelId, $userId]
        );
    }

    public static function touchDisplayed(int $id): void
    {
        Database::execute('UPDATE channels SET last_displayed_at = NOW() WHERE id = ?', [$id]);
    }

    public static function totalCount(): int
    {
        return self::count();
    }
}

/**
 * Media Model
 */
class Media extends BaseModel
{
    protected static string $table = 'media';

    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT * FROM media WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    public static function create(int $userId, array $data): int
    {
        return Database::insert(
            'INSERT INTO media (user_id, original_name, stored_name, storage_path, storage_driver,
             mime_type, file_size, width, height, public_url)
             VALUES (?,?,?,?,?,?,?,?,?,?)',
            [
                $userId,
                $data['original_name'],
                $data['stored_name'],
                $data['storage_path'],
                $data['storage_driver'] ?? 'local',
                $data['mime_type'],
                $data['file_size'],
                $data['width'] ?? null,
                $data['height'] ?? null,
                $data['public_url'] ?? null,
            ]
        );
    }

    public static function userOwns(int $mediaId, int $userId): bool
    {
        return (bool) Database::fetchOne(
            'SELECT id FROM media WHERE id = ? AND user_id = ?', [$mediaId, $userId]
        );
    }

    public static function totalSizeForUser(int $userId): int
    {
        $row = Database::fetchOne(
            'SELECT COALESCE(SUM(file_size),0) as total FROM media WHERE user_id = ?', [$userId]
        );
        return (int) $row['total'];
    }

    public static function isUsedInSlide(int $mediaId): bool
    {
        return (bool) Database::fetchOne('SELECT id FROM slides WHERE media_id = ?', [$mediaId]);
    }
}

/**
 * Slide Model
 */
class Slide extends BaseModel
{
    protected static string $table = 'slides';

    public static function forChannel(int $channelId): array
    {
        return Database::fetchAll(
            'SELECT sl.*, m.public_url, m.original_name, m.width, m.height, m.mime_type,
                    m.storage_path, m.storage_driver
             FROM slides sl
             JOIN media m ON m.id = sl.media_id
             WHERE sl.channel_id = ? AND sl.is_active = 1
             ORDER BY sl.sort_order ASC, sl.id ASC',
            [$channelId]
        );
    }

    public static function forChannelAll(int $channelId): array
    {
        return Database::fetchAll(
            'SELECT sl.*, m.public_url, m.original_name, m.mime_type, m.width, m.height
             FROM slides sl
             JOIN media m ON m.id = sl.media_id
             WHERE sl.channel_id = ?
             ORDER BY sl.sort_order ASC, sl.id ASC',
            [$channelId]
        );
    }

    public static function addToChannel(int $channelId, int $mediaId): int
    {
        $maxOrder = Database::fetchOne(
            'SELECT COALESCE(MAX(sort_order),0)+1 as next FROM slides WHERE channel_id = ?', [$channelId]
        )['next'];
        return Database::insert(
            'INSERT INTO slides (channel_id, media_id, sort_order, is_active) VALUES (?,?,?,1)',
            [$channelId, $mediaId, $maxOrder]
        );
    }

    public static function reorder(int $channelId, array $orderedIds): void
    {
        Database::beginTransaction();
        try {
            foreach ($orderedIds as $order => $slideId) {
                Database::execute(
                    'UPDATE slides SET sort_order = ? WHERE id = ? AND channel_id = ?',
                    [$order, (int)$slideId, $channelId]
                );
            }
            Database::commit();
        } catch (Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    public static function deleteForChannel(int $channelId): void
    {
        Database::execute('DELETE FROM slides WHERE channel_id = ?', [$channelId]);
    }
}

/**
 * Payment Model
 */
class Payment extends BaseModel
{
    protected static string $table = 'payments';

    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO payments (user_id, subscription_id, stripe_invoice_id, stripe_payment_intent_id,
             amount, currency, status, description, invoice_url, invoice_pdf, paid_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)',
            [
                $data['user_id'],
                $data['subscription_id'] ?? null,
                $data['stripe_invoice_id'] ?? null,
                $data['stripe_payment_intent_id'] ?? null,
                $data['amount'],
                $data['currency'] ?? 'AUD',
                $data['status'],
                $data['description'] ?? null,
                $data['invoice_url'] ?? null,
                $data['invoice_pdf'] ?? null,
                $data['paid_at'] ?? null,
            ]
        );
    }
}

/**
 * Support Ticket Model
 */
class SupportTicket extends BaseModel
{
    protected static string $table = 'support_tickets';

    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            'SELECT * FROM support_tickets WHERE user_id = ? ORDER BY updated_at DESC',
            [$userId]
        );
    }

    public static function allWithUser(string $status = '', int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = $status ? "WHERE t.status = '$status'" : '';
        return Database::fetchAll(
            "SELECT t.*, u.name as user_name, u.email as user_email
             FROM support_tickets t
             JOIN users u ON u.id = t.user_id
             $where
             ORDER BY t.updated_at DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
    }

    public static function create(int $userId, array $data): int
    {
        return Database::insert(
            'INSERT INTO support_tickets (user_id, ticket_number, subject, status, priority) VALUES (?,?,?,?,?)',
            [
                $userId,
                Helpers::generateTicketNumber(),
                $data['subject'],
                'open',
                $data['priority'] ?? 'medium',
            ]
        );
    }

    public static function getReplies(int $ticketId): array
    {
        return Database::fetchAll(
            'SELECT r.*, u.name as author_name, u.role as author_role
             FROM support_replies r
             JOIN users u ON u.id = r.user_id
             WHERE r.ticket_id = ?
             ORDER BY r.created_at ASC',
            [$ticketId]
        );
    }

    public static function addReply(int $ticketId, int $userId, string $message, bool $isAdmin): void
    {
        Database::insert(
            'INSERT INTO support_replies (ticket_id, user_id, message, is_admin_reply) VALUES (?,?,?,?)',
            [$ticketId, $userId, $message, $isAdmin ? 1 : 0]
        );
        Database::execute(
            "UPDATE support_tickets SET status = ?, updated_at = NOW() WHERE id = ?",
            [$isAdmin ? 'pending' : 'open', $ticketId]
        );
    }
}
