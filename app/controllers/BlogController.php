<?php
class BlogController extends BaseController
{
    public function index(): void
    {
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $posts = BlogPost::published($page, 9);
        $total = BlogPost::countPublished();
        $pages = (int)ceil($total / 9);

        $this->view('marketing/blog', [
            'title' => 'News & Updates – Digital Signage Tips & Guides',
            'posts' => $posts,
            'page'  => $page,
            'pages' => $pages,
            'total' => $total,
        ], 'marketing');
    }

    public function show(string $slug): void
    {
        $post = BlogPost::findBySlug($slug);
        if (!$post) $this->abort(404);

        $this->view('marketing/blog-post', [
            'title'            => ($post['meta_title'] ?: $post['title']) . ' | News & Updates',
            'meta_description' => $post['meta_description'] ?: $post['excerpt'] ?: '',
            'post'             => $post,
        ], 'marketing');
    }
}
