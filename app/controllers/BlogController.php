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

    public function sitemap(): void
    {
        $appUrl = rtrim(Settings::get('app_url', Helpers::baseUrl()), '/');
        $posts  = BlogPost::allPublished();

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        echo "  <url>\n";
        echo '    <loc>' . htmlspecialchars($appUrl . '/blog') . "</loc>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        echo "  </url>\n";

        foreach ($posts as $post) {
            $lastmod = date('Y-m-d', strtotime($post['updated_at'] ?: $post['published_at']));
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($appUrl . '/blog/' . $post['slug']) . "</loc>\n";
            echo '    <lastmod>' . $lastmod . "</lastmod>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }
}
