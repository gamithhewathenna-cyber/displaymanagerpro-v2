<?php
/**
 * ContentController – CMS for marketing pages
 */
class ContentController extends BaseController
{
    private array $pages = ['home', 'features', 'industries', 'faq', 'contact', 'footer', 'branding'];

    public function index(): void
    {
        $this->requireAdmin();
        $this->view('admin/content/index', ['title' => 'Content Manager'], 'admin');
    }

    public function page(string $page): void
    {
        $this->requireAdmin();
        if (!in_array($page, $this->pages)) $this->abort(404);
        $content = $this->getPageContent($page);
        $this->view("admin/content/$page", [
            'title'   => 'Edit ' . ucfirst($page) . ' Page',
            'content' => $content,
        ], 'admin');
    }

    public function savePage(string $page): void
    {
        $this->requireAdmin();
        $this->validateCsrf();
        if (!in_array($page, $this->pages)) $this->abort(404);

        $data = $_POST;
        unset($data['_csrf_token']);

        // Checkbox fields default to 0 if not submitted
        $checkboxFields = [];

        foreach ($data as $key => $value) {
            $settingKey = "content_{$page}_{$key}";
            $val = is_array($value)
                ? json_encode(array_map('trim', $value))
                : trim($value);
            // Save with group prefix using raw DB insert
            Database::execute(
                'INSERT INTO settings (`key`, `value`, `group`) VALUES (?,?,?)
                 ON DUPLICATE KEY UPDATE `value` = ?',
                [$settingKey, $val, 'content', $val]
            );
        }

        ActivityLog::log('admin_content_saved', "Saved content for $page page");
        Session::flash('success', ucfirst($page) . ' page content saved!');
        $this->redirect("/admin/content/$page");
    }

    public function uploadWebsiteLogo(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        if (empty($_FILES['website_logo']) || $_FILES['website_logo']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/branding');
        }

        $file = $_FILES['website_logo'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/content/branding');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg' => true, 'jpeg' => true, 'png' => true, 'webp' => true];

        if (!in_array($file['type'], $allowedMimes) || !isset($extMap[$ext])) {
            Session::flash('error', 'Only JPG, PNG, and WebP files are allowed.');
            $this->redirect('/admin/content/branding');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            Session::flash('error', 'Logo must be under 2MB.');
            $this->redirect('/admin/content/branding');
        }

        // Delete old website logo file
        $oldLogo = Settings::get('website_logo', '');
        if ($oldLogo && str_starts_with($oldLogo, '/uploads/logo/')) {
            $oldPath = PUBLIC_PATH . $oldLogo;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $logoDir = UPLOAD_PATH . '/logo/';
        if (!is_dir($logoDir)) mkdir($logoDir, 0755, true);

        $storedName = 'website_logo_' . time() . '.' . $ext;
        $dest       = $logoDir . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Session::flash('error', 'Failed to save logo. Check directory permissions.');
            $this->redirect('/admin/content/branding');
        }

        Settings::set('website_logo', '/uploads/logo/' . $storedName);
        ActivityLog::log('admin_content_saved', 'Updated website logo');
        Session::flash('success', 'Website logo updated successfully.');
        $this->redirect('/admin/content/branding');
    }

    public function removeWebsiteLogo(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $oldLogo = Settings::get('website_logo', '');
        if ($oldLogo && str_starts_with($oldLogo, '/uploads/logo/')) {
            $oldPath = PUBLIC_PATH . $oldLogo;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set('website_logo', '');
        ActivityLog::log('admin_content_saved', 'Removed website logo');
        Session::flash('success', 'Website logo removed.');
        $this->redirect('/admin/content/branding');
    }

    public static function get(string $page, string $key, string $default = ''): string
    {
        $val = Settings::get("content_{$page}_{$key}", null);
        return ($val !== null && $val !== '') ? $val : $default;
    }

    public static function getJson(string $page, string $key, array $default = []): array
    {
        $val = Settings::get("content_{$page}_{$key}", '');
        if (!$val) return $default;
        $decoded = json_decode($val, true);
        return is_array($decoded) ? $decoded : $default;
    }

    private function getPageContent(string $page): array
    {
        $defaults = $this->getDefaults($page);
        $content  = [];
        foreach ($defaults as $key => $default) {
            $val = Settings::get("content_{$page}_{$key}", null);
            $content[$key] = ($val !== null && $val !== '') ? $val : $default;
        }
        return $content;
    }

    private function getDefaults(string $page): array
    {
        return match($page) {
            'home' => [
                'badge_text'          => '14-day free trial · No credit card required',
                'hero_title_1'        => 'Update Every Restaurant Screen',
                'hero_title_2'        => 'In Seconds',
                'hero_subtitle'       => 'No USB Drives. No Complicated Software.',
                'hero_description'    => 'Manage menus, specials, promotions and announcements across all your TV screens from one simple cloud dashboard.',
                'cta_primary'         => 'Start Free 14-Day Trial →',
                'cta_secondary'       => 'View Pricing',
                'stat_1_num'          => '500+',
                'stat_1_label'        => 'Businesses',
                'stat_2_num'          => '10,000+',
                'stat_2_label'        => 'Screens managed',
                'stat_3_num'          => '99.9%',
                'stat_3_label'        => 'Uptime',
                'stat_4_num'          => '14-day',
                'stat_4_label'        => 'Free trial',
                'features_title'      => 'Everything you need to manage your screens',
                'features_subtitle'   => 'No technical knowledge required. If you can use email, you can manage your digital signage.',
                'hiw_title'           => 'Up and running in 10 minutes',
                'step_1_title'        => 'Create account',
                'step_1_desc'         => 'Sign up and choose your plan. 14-day free trial included.',
                'step_2_title'        => 'Create a channel',
                'step_2_desc'         => 'Give it a name and choose landscape or portrait.',
                'step_3_title'        => 'Upload images',
                'step_3_desc'         => 'Drag and drop your menu boards, promos, or specials.',
                'step_4_title'        => 'Display on TV',
                'step_4_desc'         => 'Open the secure URL on any Smart TV. Done.',
                'testimonial_1_quote' => '"We update our daily specials in 30 seconds now."',
                'testimonial_1_name'  => 'Sarah M.',
                'testimonial_1_role'  => 'Cafe Owner, Brisbane',
                'testimonial_2_quote' => '"Had 3 screens running in 20 minutes. Incredibly easy."',
                'testimonial_2_name'  => 'James K.',
                'testimonial_2_role'  => 'Restaurant Manager, Sydney',
                'testimonial_3_quote' => '"Finally ditched the TV running PowerPoint from a laptop."',
                'testimonial_3_name'  => 'Lisa T.',
                'testimonial_3_role'  => 'Bar Owner, Melbourne',
                'cta_title'           => 'Ready to modernise your screens?',
                'cta_subtitle'        => 'Start your free 14-day trial. No credit card required. Cancel anytime.',
                'cta_button'          => 'Get Started Free →',
            ],
            'features' => [
                'title'      => 'Everything your screens need',
                'subtitle'   => 'Built for busy restaurants and small businesses.',
                'f1_icon'    => '📺', 'f1_title' => 'Multi-Screen Management',  'f1_desc' => 'Control every TV screen from one dashboard.',
                'f2_icon'    => '☁️', 'f2_title' => 'Cloud Storage',             'f2_desc' => 'Your images are stored securely in the cloud.',
                'f3_icon'    => '🔄', 'f3_title' => 'Auto Refresh',              'f3_desc' => 'Screens automatically reload content at your chosen interval.',
                'f4_icon'    => '🖼️', 'f4_title' => 'Drag & Drop Slides',       'f4_desc' => 'Upload images and drag to set the playback order.',
                'f5_icon'    => '🎬', 'f5_title' => 'Smooth Transitions',        'f5_desc' => 'Choose from Fade, Slide, Zoom, and Crossfade.',
                'f6_icon'    => '📱', 'f6_title' => 'Universal Compatibility',   'f6_desc' => 'Works on Smart TVs, Android TV Boxes, Amazon Fire Sticks.',
                'f7_icon'    => '🔒', 'f7_title' => 'Secure Display URLs',       'f7_desc' => 'Each channel gets a unique, secure URL.',
                'f8_icon'    => '📷', 'f8_title' => 'QR Code Generator',         'f8_desc' => 'Every channel generates a QR code for instant screen setup.',
                'f9_icon'    => '⚡', 'f9_title' => 'Instant Updates',           'f9_desc' => 'Changes appear within the next auto-refresh cycle.',
                'f10_icon'   => '🛡️', 'f10_title'=> 'Enterprise Security',       'f10_desc'=> 'CSRF protection, XSS prevention, bcrypt password hashing.',
                'f11_icon'   => '📊', 'f11_title'=> 'Usage Dashboard',           'f11_desc'=> 'See your storage, active screens, and subscription status.',
                'f12_icon'   => '🎧', 'f12_title'=> 'Email Support',             'f12_desc'=> 'Get help via our built-in support ticket system.',
                'cta_title'  => 'Ready to get started?',
                'cta_subtitle' => '14-day free trial. No credit card required.',
                'cta_button' => 'Start Free Trial →',
            ],
            'industries' => [
                'title'    => 'Built for your industry',
                'subtitle' => 'From busy restaurants to boutique retail.',
                'i1_icon'  => '🍕', 'i1_name' => 'Restaurants & Cafes',    'i1_desc' => 'Display your full menu, daily specials, and combo deals.',
                'i2_icon'  => '🍺', 'i2_name' => 'Bars & Pubs',            'i2_desc' => 'Show your tap list, happy hour deals, and event nights.',
                'i3_icon'  => '🏨', 'i3_name' => 'Hotels & Accommodation',  'i3_desc' => 'Welcome guests with branded displays in lobbies and lifts.',
                'i4_icon'  => '💅', 'i4_name' => 'Retail & Beauty',        'i4_desc' => 'Showcase new arrivals, promotions, and loyalty program benefits.',
                'i5_icon'  => '🏋️', 'i5_name' => 'Gyms & Fitness',         'i5_desc' => 'Display class timetables, PT promotions, and member achievements.',
                'i6_icon'  => '🏥', 'i6_name' => 'Healthcare & Clinics',   'i6_desc' => 'Display wait times, health tips, and service information.',
                'cta_title'  => 'Ready to modernise your screens?',
                'cta_button' => 'Start Your 14-Day Free Trial',
            ],
            'faq' => [
                'title'        => 'Frequently Asked Questions',
                'subtitle'     => 'Everything you need to know.',
                'q1'  => 'Do I need a credit card to start?',
                'a1'  => 'No. Your 14-day free trial starts immediately with no credit card required.',
                'q2'  => 'What devices can I use to display my channel?',
                'a2'  => 'Any device with a modern web browser — Smart TVs, Android TV boxes, Amazon Fire Sticks, Raspberry Pi, and tablets.',
                'q3'  => 'How do I set up my first screen?',
                'a3'  => 'Sign up → Create a channel → Upload images → Copy the display URL → Open on your TV. Under 5 minutes.',
                'q4'  => 'What happens after my trial ends?',
                'a4'  => 'Your screens pause until you activate a paid plan. Your data is preserved for 30 days.',
                'q5'  => 'Can I switch plans?',
                'a5'  => 'Yes, anytime via the Billing page. Upgrades take effect immediately.',
                'q6'  => 'Can I cancel anytime?',
                'a6'  => 'Absolutely. Cancel from the Billing page with no early termination fees.',
                'q7'  => 'What image formats are supported?',
                'a7'  => 'JPEG, PNG, and WebP. Maximum file size is 500KB per image.',
                'q8'  => 'How quickly do changes appear on screen?',
                'a8'  => 'Screens auto-refresh at your chosen interval (5–60 minutes).',
                'q9'  => 'What are the server requirements?',
                'a9'  => 'PHP 8.2+, MySQL 8.0+, Apache or LiteSpeed with mod_rewrite.',
                'q10' => 'Do screens work without internet?',
                'a10' => 'No — SignageCloud is cloud-based and requires an internet connection.',
                'still_title'   => 'Still have questions?',
                'still_subtitle'=> 'We usually reply within a few hours during business days.',
                'still_button'  => 'Contact Us',
            ],
            'contact' => [
                'title'       => 'Get in touch',
                'subtitle'    => 'Have a question, need help setting up, or want to discuss a custom plan?',
                'email_label' => 'Email Support',
                'email_desc'  => 'We typically reply within 1 business day.',
                'hours_label' => 'Business Hours',
                'hours_value' => 'Monday – Friday, 9am – 5pm AEST',
                'form_title'  => 'Send us a message',
                'cta_title'   => 'Not a customer yet?',
                'cta_desc'    => 'Start your free 14-day trial. No credit card required.',
                'cta_button'  => 'Start Free Trial →',
            ],
            'footer' => [
                'tagline'    => 'Cloud-based digital signage for restaurants, cafes, and retail.',
                'col1_title' => 'Product',
                'col1_links' => "Features|/features\nPricing|/pricing\nIndustries|/industries\nFAQ|/faq",
                'col2_title' => 'Company',
                'col2_links' => "Contact|/contact",
                'col3_title' => 'Account',
                'col3_links' => "Sign In|/login\nStart Free Trial|/register",
                'copyright'  => '© ' . date('Y') . ' SignageCloud. All rights reserved.',
            ],
            'branding' => [],
            default => [],
        };
    }
}
