<?php
/**
 * Marketing Controller – public website pages
 */
class MarketingController extends BaseController
{
    public function home(): void
    {
        $plans        = Plan::active();
        $latestPosts  = BlogPost::published(1, 3);
        $this->view('marketing/home', ['title' => 'Digital Signage Made Simple', 'plans' => $plans, 'latestPosts' => $latestPosts], 'marketing');
    }

    public function about(): void
    {
        $this->view('marketing/about', ['title' => 'About Us'], 'marketing');
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

    public function cityPage(string $city): void
    {
        $cities = self::getCities();
        $city   = strtolower(trim($city));
        if (!array_key_exists($city, $cities)) $this->abort(404);

        $data    = $cities[$city];
        $name    = $data['name'];
        $country = $data['country'];
        $title   = 'Digital Signage Software for ' . $name . ' Businesses';

        $this->view('marketing/city', [
            'title'      => $title,
            'geo_region' => $data['geo'],
            'city'       => $data,
            'citySlug'   => $city,
            'allCities'  => $cities,
        ], 'marketing');
    }

    public function geoSitemap(): void
    {
        $appUrl = Helpers::appUrl();

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach (self::getCities() as $citySlug => $city) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($appUrl . '/digital-signage/' . $citySlug) . "</loc>\n";
            echo "    <changefreq>monthly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }

    private static function getCities(): array
    {
        return [
            'sydney'       => ['name' => 'Sydney',       'country' => 'Australia',      'state' => 'NSW', 'geo' => 'AU-NSW', 'flag' => '🇦🇺'],
            'melbourne'    => ['name' => 'Melbourne',    'country' => 'Australia',      'state' => 'VIC', 'geo' => 'AU-VIC', 'flag' => '🇦🇺'],
            'brisbane'     => ['name' => 'Brisbane',     'country' => 'Australia',      'state' => 'QLD', 'geo' => 'AU-QLD', 'flag' => '🇦🇺'],
            'perth'        => ['name' => 'Perth',        'country' => 'Australia',      'state' => 'WA',  'geo' => 'AU-WA',  'flag' => '🇦🇺'],
            'adelaide'     => ['name' => 'Adelaide',     'country' => 'Australia',      'state' => 'SA',  'geo' => 'AU-SA',  'flag' => '🇦🇺'],
            'gold-coast'   => ['name' => 'Gold Coast',   'country' => 'Australia',      'state' => 'QLD', 'geo' => 'AU-QLD', 'flag' => '🇦🇺'],
            'auckland'     => ['name' => 'Auckland',     'country' => 'New Zealand',    'state' => '',    'geo' => 'NZ',     'flag' => '🇳🇿'],
            'wellington'   => ['name' => 'Wellington',   'country' => 'New Zealand',    'state' => '',    'geo' => 'NZ',     'flag' => '🇳🇿'],
            'christchurch' => ['name' => 'Christchurch', 'country' => 'New Zealand',    'state' => '',    'geo' => 'NZ',     'flag' => '🇳🇿'],
            'london'       => ['name' => 'London',       'country' => 'United Kingdom', 'state' => '',    'geo' => 'GB-ENG', 'flag' => '🇬🇧'],
            'manchester'   => ['name' => 'Manchester',   'country' => 'United Kingdom', 'state' => '',    'geo' => 'GB-ENG', 'flag' => '🇬🇧'],
            'birmingham'   => ['name' => 'Birmingham',   'country' => 'United Kingdom', 'state' => '',    'geo' => 'GB-ENG', 'flag' => '🇬🇧'],
            'new-york'     => ['name' => 'New York',     'country' => 'United States',  'state' => 'NY',  'geo' => 'US-NY',  'flag' => '🇺🇸'],
            'los-angeles'  => ['name' => 'Los Angeles',  'country' => 'United States',  'state' => 'CA',  'geo' => 'US-CA',  'flag' => '🇺🇸'],
            'chicago'      => ['name' => 'Chicago',      'country' => 'United States',  'state' => 'IL',  'geo' => 'US-IL',  'flag' => '🇺🇸'],
        ];
    }

    public function sitemap(): void
    {
        $appUrl = Helpers::appUrl();

        $staticUrls = [
            ['loc' => $appUrl . '/',               'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => $appUrl . '/features',        'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => $appUrl . '/pricing',         'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => $appUrl . '/industries',      'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => $appUrl . '/about',           'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => $appUrl . '/faq',             'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => $appUrl . '/blog',            'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => $appUrl . '/contact',         'priority' => '0.6', 'changefreq' => 'yearly'],
            ['loc' => $appUrl . '/privacy-policy',  'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => $appUrl . '/terms',           'priority' => '0.3', 'changefreq' => 'yearly'],
            ['loc' => $appUrl . '/refund-policy',   'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach (array_keys(self::getCities()) as $citySlug) {
            $staticUrls[] = ['loc' => $appUrl . '/digital-signage/' . $citySlug, 'priority' => '0.8', 'changefreq' => 'monthly'];
        }

        $posts = BlogPost::allPublished();
        $blogUrls = [];
        foreach ($posts as $post) {
            $blogUrls[] = [
                'loc'        => $appUrl . '/blog/' . $post['slug'],
                'lastmod'    => date('Y-m-d', strtotime($post['updated_at'] ?: $post['published_at'])),
                'priority'   => '0.7',
                'changefreq' => 'monthly',
            ];
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach (array_merge($staticUrls, $blogUrls) as $url) {
            echo "  <url>\n";
            echo '    <loc>' . htmlspecialchars($url['loc']) . "</loc>\n";
            if (!empty($url['lastmod']))    echo '    <lastmod>'   . $url['lastmod']    . "</lastmod>\n";
            if (!empty($url['changefreq'])) echo '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            if (!empty($url['priority']))   echo '    <priority>'  . $url['priority']   . "</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
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

}
