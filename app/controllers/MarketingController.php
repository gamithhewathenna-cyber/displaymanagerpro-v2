<?php
/**
 * Marketing Controller – public website pages
 */
class MarketingController extends BaseController
{
    public function home(): void
    {
        $plans = Plan::active();
        $this->view('marketing/home', ['title' => 'Digital Signage Made Simple', 'plans' => $plans], 'marketing');
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
        $title   = 'Digital Signage Software for ' . $name . ' Businesses | DisplayManagerPro';

        $this->view('marketing/city', [
            'title'      => $title,
            'geo_region' => $data['geo'],
            'city'       => $data,
            'citySlug'   => $city,
            'allCities'  => $cities,
        ], 'marketing');
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
