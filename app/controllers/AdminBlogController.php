<?php
class AdminBlogController extends BaseController
{
    // ── List ─────────────────────────────────────────────────────────────────

    public function index(): void
    {
        $this->requireAdmin();
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $posts = BlogPost::allAdmin($page, 20);
        $total = BlogPost::countAll();

        $this->view('admin/blog/index', [
            'title' => 'News & Updates',
            'posts' => $posts,
            'page'  => $page,
            'pages' => (int)ceil($total / 20),
            'total' => $total,
        ], 'admin');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->requireAdmin();
        $this->view('admin/blog/form', ['title' => 'New Article', 'post' => null], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        [$data, $error] = $this->parseForm();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect('/admin/blog/create');
        }

        // Always run through makeSlug() — not just when blank — since the form's JS
        // auto-fills the slug from the title, so it's almost never actually empty here.
        // Without this, a genuine collision (e.g. resubmission, similar titles) throws a
        // raw duplicate-key SQL error instead of auto-resolving with a -1, -2, ... suffix.
        $data['slug'] = BlogPost::makeSlug($data['slug'] ?: $data['title']);

        $imagePath = '';
        if (!empty($_FILES['featured_image']['name'])) {
            $imagePath = $this->uploadImage() ?? '';
        }
        $data['featured_image'] = $imagePath;

        try {
            $id = BlogPost::create($data);
        } catch (Exception $e) {
            error_log('Blog post create error: ' . $e->getMessage());
            Session::flash('error', 'Could not save the article: ' . $e->getMessage());
            $this->redirect('/admin/blog/create');
        }

        ActivityLog::log('blog_post_created', 'Created post: ' . $data['title']);
        Session::flash('success', 'Post created.');
        $this->redirect('/admin/blog/' . $id . '/edit');
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function edit(string $id): void
    {
        $this->requireAdmin();
        $post = BlogPost::findById((int)$id);
        if (!$post) $this->abort(404);

        $this->view('admin/blog/form', ['title' => 'Edit Article', 'post' => $post], 'admin');
    }

    public function update(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $post = BlogPost::findById((int)$id);
        if (!$post) $this->abort(404);

        [$data, $error] = $this->parseForm();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect('/admin/blog/' . $id . '/edit');
        }

        // Same as store() — always dedupe, since the slug field is rarely actually blank.
        $data['slug'] = BlogPost::makeSlug($data['slug'] ?: $data['title'], (int)$id);

        // Image handling
        $imagePath = $post['featured_image'] ?? '';
        if (($_POST['remove_image'] ?? '') === '1') {
            $this->deleteImageFile($imagePath);
            $imagePath = '';
        } elseif (!empty($_FILES['featured_image']['name'])) {
            $this->deleteImageFile($imagePath);
            $imagePath = $this->uploadImage() ?? $imagePath;
        }
        $data['featured_image'] = $imagePath;
        $data['published_at']   = $post['published_at'];

        try {
            BlogPost::update((int)$id, $data);
        } catch (Exception $e) {
            error_log('Blog post update error: ' . $e->getMessage());
            Session::flash('error', 'Could not save the article: ' . $e->getMessage());
            $this->redirect('/admin/blog/' . $id . '/edit');
        }

        ActivityLog::log('blog_post_updated', 'Updated post: ' . $data['title']);
        Session::flash('success', 'Post saved.');
        $this->redirect('/admin/blog/' . $id . '/edit');
    }

    // ── Bulk actions ─────────────────────────────────────────────────────────

    public function bulkStatus(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $ids    = array_values(array_unique(array_filter(array_map('intval', $_POST['ids'] ?? []))));
        $status = in_array($_POST['status'] ?? '', ['published', 'draft'], true) ? $_POST['status'] : null;

        if (!$ids || !$status) {
            Session::flash('error', 'Select at least one article first.');
            $this->redirect('/admin/blog');
        }

        try {
            BlogPost::bulkSetStatus($ids, $status);
        } catch (Exception $e) {
            error_log('Blog bulk status error: ' . $e->getMessage());
            Session::flash('error', 'Could not update the selected articles: ' . $e->getMessage());
            $this->redirect('/admin/blog');
        }

        ActivityLog::log(
            'blog_bulk_' . $status,
            count($ids) . ' post(s) set to ' . $status . ': ' . implode(',', $ids)
        );
        Session::flash('success', count($ids) . ' article' . (count($ids) === 1 ? '' : 's') . ' ' . ($status === 'published' ? 'published' : 'unpublished') . '.');
        $this->redirect('/admin/blog');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $post = BlogPost::findById((int)$id);
        if (!$post) $this->abort(404);

        $this->deleteImageFile($post['featured_image'] ?? '');
        BlogPost::delete((int)$id);
        ActivityLog::log('blog_post_deleted', 'Deleted post: ' . $post['title']);
        Session::flash('success', 'Post deleted.');
        $this->redirect('/admin/blog');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function parseForm(): array
    {
        $title   = Helpers::sanitize($_POST['title'] ?? '');
        if (!$title) return [[], 'Title is required.'];

        return [[
            'title'            => $title,
            'slug'             => Helpers::sanitize($_POST['slug'] ?? ''),
            'excerpt'          => trim($_POST['excerpt'] ?? ''),
            'content'          => trim($_POST['content'] ?? ''),
            'author'           => Helpers::sanitize($_POST['author'] ?? 'Admin'),
            'status'           => in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft',
            'meta_title'       => Helpers::sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => Helpers::sanitize($_POST['meta_description'] ?? ''),
        ], null];
    }

    private function uploadImage(): ?string
    {
        $file = $_FILES['featured_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $ext          = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts  = ['jpg','jpeg','png','webp'];
        $allowedMimes = ['image/jpeg','image/png','image/webp'];

        if (!in_array($ext, $allowedExts) || !in_array($file['type'], $allowedMimes)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $dir  = UPLOAD_PATH . '/blog/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = 'blog_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dir . $name)) {
            return '/uploads/blog/' . $name;
        }
        return null;
    }

    private function deleteImageFile(string $path): void
    {
        if ($path && str_starts_with($path, '/uploads/blog/')) {
            $file = PUBLIC_PATH . $path;
            if (file_exists($file)) @unlink($file);
        }
    }
}
