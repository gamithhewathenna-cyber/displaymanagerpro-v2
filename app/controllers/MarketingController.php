<?php
/**
 * Marketing Controller – public website pages
 */
class MarketingController extends BaseController
{
    public function home(): void
    {
        $plans        = Plan::active();
        $latestNews   = BlogArticle::published(3, 0);
        $this->view('marketing/home', [
            'title'      => 'Digital Signage Made Simple',
            'plans'      => $plans,
            'latestNews' => $latestNews,
        ], 'marketing');
    }

    public function features(): void
    {
        $this->view('marketing/features', ['title' => 'Features'], 'marketing');
    }

    public function pricing(): void
    {
        $plans = Plan::active();
        $this->view('marketing/pricing', ['title' => 'Pricing', 'plans' => $plans], 'marketing');
    }

    public function industries(): void
    {
        $this->view('marketing/industries', ['title' => 'Industries'], 'marketing');
    }

    public function faq(): void
    {
        $this->view('marketing/faq', ['title' => 'FAQ'], 'marketing');
    }

    public function privacy(): void
    {
        $content = [
            'title'        => ContentController::get('privacy', 'title', 'Privacy Policy'),
            'last_updated' => ContentController::get('privacy', 'last_updated', ''),
            'body'         => ContentController::get('privacy', 'body', ''),
        ];
        $this->view('marketing/privacy', ['title' => $content['title'], 'content' => $content], 'marketing');
    }

    public function terms(): void
    {
        $content = [
            'title'        => ContentController::get('terms', 'title', 'Terms & Conditions'),
            'last_updated' => ContentController::get('terms', 'last_updated', ''),
            'body'         => ContentController::get('terms', 'body', ''),
        ];
        $this->view('marketing/terms', ['title' => $content['title'], 'content' => $content], 'marketing');
    }

    public function refund(): void
    {
        $content = [
            'title'        => ContentController::get('refund', 'title', 'Refund Policy'),
            'last_updated' => ContentController::get('refund', 'last_updated', ''),
            'body'         => ContentController::get('refund', 'body', ''),
        ];
        $this->view('marketing/refund', ['title' => $content['title'], 'content' => $content], 'marketing');
    }

    public function contact(): void
    {
        $this->view('marketing/contact', ['title' => 'Contact'], 'marketing');
    }

    public function contactSubmit(): void
    {
        $this->validateCsrf();
        $name    = Helpers::sanitize($_POST['name'] ?? '');
        $email   = Helpers::sanitize($_POST['email'] ?? '');
        $message = Helpers::sanitize($_POST['message'] ?? '');

        if (!$name || !Helpers::validateEmail($email) || strlen($message) < 10) {
            Session::flash('error', 'Please fill out all fields correctly.');
            $this->redirect('/contact');
        }

        $adminEmail = Settings::get('smtp_from_email', 'admin@localhost');
        $body = "<p><strong>From:</strong> $name ($email)</p><p><strong>Message:</strong><br>" . nl2br($message) . "</p>";
        Mailer::send($adminEmail, 'Admin', "Website Contact from $name", $body);

        Session::flash('success', 'Message sent! We\'ll be in touch within 1 business day.');
        $this->redirect('/contact');
    }

    // ── News & Updates ─────────────────────────────────────────────────────

    public function news(): void
    {
        $perPage  = 9;
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $offset   = ($page - 1) * $perPage;
        $total    = BlogArticle::countPublished();
        $articles = BlogArticle::published($perPage, $offset);

        $this->view('marketing/news', [
            'title'      => 'News & Updates',
            'articles'   => $articles,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => (int)ceil($total / $perPage),
        ], 'marketing');
    }

    public function newsArticle(string $slug): void
    {
        $article = BlogArticle::findBySlug($slug);
        if (!$article || $article['status'] !== 'published') $this->abort(404);

        Database::execute('UPDATE blog_articles SET view_count = view_count + 1 WHERE id = ?', [$article['id']]);

        $related = BlogArticle::related((int)$article['id'], $article['category'] ?? '', 3);

        $this->view('marketing/news-article', [
            'title'           => $article['seo_title'] ?: $article['title'],
            'metaDescription' => $article['seo_description'] ?: $article['excerpt'] ?: '',
            'ogImage'         => $article['featured_image_url'] ?: '',
            'article'         => $article,
            'related'         => $related,
        ], 'marketing');
    }

    // ── XML Sitemap ────────────────────────────────────────────────────────

    public function sitemap(): void
    {
        $baseUrl  = rtrim(Helpers::baseUrl(), '/');
        $articles = BlogArticle::published();

        header('Content-Type: application/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $staticPages = ['/', '/features', '/pricing', '/industries', '/faq', '/contact', '/news'];
        foreach ($staticPages as $path) {
            echo "  <url>\n";
            echo "    <loc>" . Helpers::e($baseUrl . $path) . "</loc>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>" . ($path === '/' ? '1.0' : '0.8') . "</priority>\n";
            echo "  </url>\n";
        }

        foreach ($articles as $a) {
            $lastmod = $a['updated_at'] ?? $a['published_at'];
            echo "  <url>\n";
            echo "    <loc>" . Helpers::e($baseUrl . '/news/' . $a['slug']) . "</loc>\n";
            if ($lastmod) {
                echo "    <lastmod>" . date('Y-m-d', strtotime($lastmod)) . "</lastmod>\n";
            }
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }

        echo "</urlset>\n";
        exit;
    }
}
