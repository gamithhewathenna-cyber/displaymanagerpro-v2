<?php
class BlogPost
{
    public static function published(int $page = 1, int $perPage = 9): array
    {
        $offset = ($page - 1) * $perPage;
        return Database::fetchAll(
            'SELECT * FROM blog_posts WHERE status = ? ORDER BY published_at DESC, id DESC LIMIT ? OFFSET ?',
            ['published', $perPage, $offset]
        );
    }

    public static function allPublished(): array
    {
        return Database::fetchAll(
            'SELECT slug, updated_at, published_at FROM blog_posts WHERE status = ? ORDER BY published_at DESC',
            ['published']
        );
    }

    public static function countPublished(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM blog_posts WHERE status = ?', ['published']);
        return (int)($row['c'] ?? 0);
    }

    public static function allAdmin(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return Database::fetchAll(
            'SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$perPage, $offset]
        );
    }

    public static function countAll(): int
    {
        $row = Database::fetchOne('SELECT COUNT(*) AS c FROM blog_posts');
        return (int)($row['c'] ?? 0);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM blog_posts WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM blog_posts WHERE slug = ? AND status = ?',
            [$slug, 'published']
        );
    }

    public static function create(array $d): int
    {
        return Database::insert(
            'INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, author, status, meta_title, meta_description, published_at)
             VALUES (?,?,?,?,?,?,?,?,?,?)',
            [
                $d['title'], $d['slug'], $d['excerpt'] ?: null, $d['content'] ?: null,
                $d['featured_image'] ?: null, $d['author'], $d['status'],
                $d['meta_title'] ?: null, $d['meta_description'] ?: null,
                $d['status'] === 'published' ? ($d['published_at'] ?? date('Y-m-d H:i:s')) : null,
            ]
        );
    }

    public static function update(int $id, array $d): void
    {
        // Keep original published_at if already set; set it now if newly published
        $pub = $d['published_at'] ?: null;
        if ($d['status'] === 'published' && !$pub) {
            $pub = date('Y-m-d H:i:s');
        }
        if ($d['status'] === 'draft') {
            $pub = null;
        }

        Database::execute(
            'UPDATE blog_posts
             SET title=?, slug=?, excerpt=?, content=?, featured_image=?, author=?,
                 status=?, meta_title=?, meta_description=?, published_at=?, updated_at=NOW()
             WHERE id=?',
            [
                $d['title'], $d['slug'], $d['excerpt'] ?: null, $d['content'] ?: null,
                $d['featured_image'] ?: null, $d['author'], $d['status'],
                $d['meta_title'] ?: null, $d['meta_description'] ?: null,
                $pub, $id,
            ]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM blog_posts WHERE id = ?', [$id]);
    }

    public static function bulkSetStatus(array $ids, string $status): void
    {
        if (!$ids) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        if ($status === 'published') {
            // Preserve an existing published_at (e.g. re-publishing) rather than resetting it
            Database::execute(
                "UPDATE blog_posts SET status='published', published_at = COALESCE(published_at, NOW()), updated_at = NOW() WHERE id IN ($placeholders)",
                $ids
            );
        } else {
            Database::execute(
                "UPDATE blog_posts SET status='draft', published_at = NULL, updated_at = NOW() WHERE id IN ($placeholders)",
                $ids
            );
        }
    }

    public static function makeSlug(string $title, int $excludeId = 0): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $base = $slug ?: 'post';
        $i    = 1;
        while (Database::fetchOne('SELECT id FROM blog_posts WHERE slug = ? AND id != ?', [$slug, $excludeId])) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
