<?php
class ContentSchedule extends BaseModel
{
    protected static string $table = 'content_schedules';

    public static function forChannel(int $channelId): array
    {
        return Database::fetchAll(
            'SELECT * FROM content_schedules WHERE channel_id = ? ORDER BY starts_at DESC',
            [$channelId]
        );
    }

    /**
     * Returns the highest-priority schedule that is active right now.
     */
    public static function activeForChannel(int $channelId): ?array
    {
        $now = date('Y-m-d H:i:s');
        return Database::fetchOne(
            'SELECT * FROM content_schedules
             WHERE channel_id = ? AND is_active = 1
               AND starts_at <= ? AND (ends_at IS NULL OR ends_at > ?)
             ORDER BY priority DESC, starts_at DESC
             LIMIT 1',
            [$channelId, $now, $now]
        );
    }

    public static function create(int $channelId, array $d): int
    {
        return Database::insert(
            'INSERT INTO content_schedules (channel_id, name, starts_at, ends_at, media_ids, priority, is_active)
             VALUES (?,?,?,?,?,?,1)',
            [
                $channelId,
                $d['name'],
                $d['starts_at'],
                $d['ends_at'] ?? null,
                json_encode(array_values($d['media_ids'])),
                (int)($d['priority'] ?? 0),
            ]
        );
    }

    public static function update(int $id, array $d): void
    {
        Database::execute(
            'UPDATE content_schedules
             SET name=?, starts_at=?, ends_at=?, media_ids=?, priority=?, updated_at=NOW()
             WHERE id=?',
            [
                $d['name'],
                $d['starts_at'],
                $d['ends_at'] ?? null,
                json_encode(array_values($d['media_ids'])),
                (int)($d['priority'] ?? 0),
                $id,
            ]
        );
    }

    public static function belongsToChannel(int $id, int $channelId): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM content_schedules WHERE id = ? AND channel_id = ?',
            [$id, $channelId]
        );
    }

    public static function deleteForChannel(int $channelId): void
    {
        Database::execute('DELETE FROM content_schedules WHERE channel_id = ?', [$channelId]);
    }
}
