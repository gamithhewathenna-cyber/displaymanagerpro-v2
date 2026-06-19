<?php
/**
 * Blog Controller – admin management for News & Updates articles
 */
class BlogController extends BaseController
{
    // ── Admin: list ────────────────────────────────────────────────────────

    public function index(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset  = ($page - 1) * $perPage;

        $articles = Database::fetchAll(
            'SELECT b.*, u.name as author_display
             FROM blog_articles b
             JOIN users u ON u.id = b.user_id
             ORDER BY b.created_at DESC
             LIMIT ? OFFSET ?',
            [$perPage, $offset]
        );
        $total      = (int)(Database::fetchOne('SELECT COUNT(*) as c FROM blog_articles')['c'] ?? 0);
        $totalPages = (int)ceil($total / $perPage);

        $this->view('admin/blog/index', [
            'title'      => 'News & Updates',
            'articles'   => $articles,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => $totalPages,
        ], 'admin');
    }

    // ── Admin: create ──────────────────────────────────────────────────────

    public function create(): void
    {
        $this->requireAdmin();
        $this->view('admin/blog/create', ['title' => 'New Article'], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $user = $this->currentUser();

        $title      = Helpers::sanitize($_POST['title'] ?? '');
        $slug       = trim(Helpers::sanitize($_POST['slug'] ?? ''));
        $excerpt    = Helpers::sanitize($_POST['excerpt'] ?? '');
        $body       = $_POST['body'] ?? '';
        $category   = Helpers::sanitize($_POST['category'] ?? '');
        $authorName = Helpers::sanitize($_POST['author_name'] ?? '') ?: $user['name'];
        $status     = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';
        $publishedAt = !empty($_POST['published_at'])
            ? date('Y-m-d H:i:s', strtotime($_POST['published_at']))
            : null;
        $seoTitle   = Helpers::sanitize($_POST['seo_title'] ?? '');
        $seoDesc    = Helpers::sanitize($_POST['seo_description'] ?? '');
        $focusKw    = Helpers::sanitize($_POST['focus_keyword'] ?? '');

        if (!$title) {
            Session::flash('error', 'Title is required.');
            $this->redirect('/admin/blog/create');
        }

        $slug = $slug ? BlogArticle::makeSlug($slug) : BlogArticle::makeSlug($title);
        $slug = BlogArticle::uniqueSlug($slug);

        if ($status === 'published' && !$publishedAt) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $featuredImageUrl = null;
        if (!empty($_FILES['featured_image']['name'])) {
            try {
                $media            = StorageService::store($_FILES['featured_image'], $user['id']);
                Media::create($user['id'], $media);
                $featuredImageUrl = $media['public_url'];
            } catch (Exception $e) {
                Session::flash('error', 'Image upload failed: ' . $e->getMessage());
                $this->redirect('/admin/blog/create');
            }
        }

        Database::insert(
            'INSERT INTO blog_articles
             (user_id, title, slug, excerpt, body, featured_image_url, category, author_name,
              status, published_at, seo_title, seo_description, focus_keyword)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [$user['id'], $title, $slug, $excerpt, $body, $featuredImageUrl, $category,
             $authorName, $status, $publishedAt, $seoTitle, $seoDesc, $focusKw]
        );

        ActivityLog::log('blog_article_created', "Created article: $title");
        Session::flash('success', 'Article created successfully.');
        $this->redirect('/admin/blog');
    }

    // ── Admin: edit ────────────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requireAdmin();
        $article = BlogArticle::find((int)$id);
        if (!$article) $this->abort(404);

        $this->view('admin/blog/edit', [
            'title'   => 'Edit Article',
            'article' => $article,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $article = BlogArticle::find((int)$id);
        if (!$article) $this->abort(404);

        $title      = Helpers::sanitize($_POST['title'] ?? '');
        $slug       = trim(Helpers::sanitize($_POST['slug'] ?? ''));
        $excerpt    = Helpers::sanitize($_POST['excerpt'] ?? '');
        $body       = $_POST['body'] ?? '';
        $category   = Helpers::sanitize($_POST['category'] ?? '');
        $authorName = Helpers::sanitize($_POST['author_name'] ?? '') ?: $user['name'];
        $status     = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';
        $publishedAt = !empty($_POST['published_at'])
            ? date('Y-m-d H:i:s', strtotime($_POST['published_at']))
            : $article['published_at'];
        $seoTitle   = Helpers::sanitize($_POST['seo_title'] ?? '');
        $seoDesc    = Helpers::sanitize($_POST['seo_description'] ?? '');
        $focusKw    = Helpers::sanitize($_POST['focus_keyword'] ?? '');

        if (!$title) {
            Session::flash('error', 'Title is required.');
            $this->redirect("/admin/blog/{$id}/edit");
        }

        // Rebuild slug only if the admin changed it from the stored value
        $newSlug = $slug ?: BlogArticle::makeSlug($title);
        if ($newSlug !== $article['slug']) {
            $newSlug = BlogArticle::makeSlug($newSlug);
            $newSlug = BlogArticle::uniqueSlug($newSlug, (int)$id);
        }

        if ($status === 'published' && !$publishedAt) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $featuredImageUrl = $article['featured_image_url'];
        if (!empty($_FILES['featured_image']['name'])) {
            try {
                $media            = StorageService::store($_FILES['featured_image'], $user['id']);
                Media::create($user['id'], $media);
                $featuredImageUrl = $media['public_url'];
            } catch (Exception $e) {
                Session::flash('error', 'Image upload failed: ' . $e->getMessage());
                $this->redirect("/admin/blog/{$id}/edit");
            }
        }

        Database::execute(
            'UPDATE blog_articles SET
             title=?, slug=?, excerpt=?, body=?, featured_image_url=?, category=?,
             author_name=?, status=?, published_at=?, seo_title=?, seo_description=?,
             focus_keyword=?, updated_at=NOW()
             WHERE id=?',
            [$title, $newSlug, $excerpt, $body, $featuredImageUrl, $category,
             $authorName, $status, $publishedAt, $seoTitle, $seoDesc, $focusKw, (int)$id]
        );

        ActivityLog::log('blog_article_updated', "Updated article: $title");
        Session::flash('success', 'Article updated successfully.');
        $this->redirect('/admin/blog');
    }

    // ── Admin: delete ──────────────────────────────────────────────────────

    public function deleteArticle(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $article = BlogArticle::find((int)$id);
        if (!$article) $this->abort(404);

        Database::execute('DELETE FROM blog_articles WHERE id = ?', [(int)$id]);
        ActivityLog::log('blog_article_deleted', "Deleted article: {$article['title']}");
        Session::flash('success', 'Article deleted.');
        $this->redirect('/admin/blog');
    }
}
