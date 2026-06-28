<?php
/**
 * ContentController – CMS for marketing pages
 */
class ContentController extends BaseController
{
    private array $pages = ['home', 'features', 'industries', 'faq', 'contact', 'pricing', 'footer', 'branding', 'privacy', 'terms', 'refund', 'about'];

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

        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dest)) {
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

    public function uploadMobileWebsiteLogo(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        if (empty($_FILES['mobile_logo']) || $_FILES['mobile_logo']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/branding');
        }

        $file = $_FILES['mobile_logo'];
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

        $old = Settings::get('website_logo_mobile', '');
        if ($old && str_starts_with($old, '/uploads/logo/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $logoDir = UPLOAD_PATH . '/logo/';
        if (!is_dir($logoDir)) mkdir($logoDir, 0755, true);

        $storedName = 'mobile_logo_' . time() . '.' . $ext;
        $dest       = $logoDir . $storedName;

        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dest)) {
            Session::flash('error', 'Failed to save logo. Check directory permissions.');
            $this->redirect('/admin/content/branding');
        }

        Settings::set('website_logo_mobile', '/uploads/logo/' . $storedName);
        ActivityLog::log('admin_content_saved', 'Updated mobile website logo');
        Session::flash('success', 'Mobile logo updated successfully.');
        $this->redirect('/admin/content/branding');
    }

    public function removeMobileWebsiteLogo(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $old = Settings::get('website_logo_mobile', '');
        if ($old && str_starts_with($old, '/uploads/logo/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set('website_logo_mobile', '');
        ActivityLog::log('admin_content_saved', 'Removed mobile website logo');
        Session::flash('success', 'Mobile logo removed.');
        $this->redirect('/admin/content/branding');
    }

    public function uploadHomeSlide(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 6) $this->abort(404);

        if (empty($_FILES['slide_image']) || $_FILES['slide_image']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/home');
        }

        $file = $_FILES['slide_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/content/home');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file['type'] !== 'image/png' || $ext !== 'png') {
            Session::flash('error', 'Only PNG images are allowed for the slider.');
            $this->redirect('/admin/content/home');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'Image must be under 5 MB.');
            $this->redirect('/admin/content/home');
        }

        $old = Settings::get("content_home_slide_{$slot}", '');
        if ($old && str_starts_with($old, '/uploads/slider/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $dir = UPLOAD_PATH . '/slider/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = "home_slide_{$slot}_" . time() . '.png';
        if (!Helpers::moveOptimizedImage($file['tmp_name'], 'image/png', $dir . $name)) {
            Session::flash('error', 'Failed to save image. Check directory permissions.');
            $this->redirect('/admin/content/home');
        }

        Settings::set("content_home_slide_{$slot}", '/uploads/slider/' . $name, 'content');
        ActivityLog::log('admin_content_saved', "Updated homepage slider image slot $slot");
        Session::flash('success', "Slide $slot updated.");
        $this->redirect('/admin/content/home');
    }

    public function removeHomeSlide(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 6) $this->abort(404);

        $old = Settings::get("content_home_slide_{$slot}", '');
        if ($old && str_starts_with($old, '/uploads/slider/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set("content_home_slide_{$slot}", '', 'content');
        ActivityLog::log('admin_content_saved', "Removed homepage slider image slot $slot");
        Session::flash('success', "Slide $slot removed.");
        $this->redirect('/admin/content/home');
    }

    public function uploadHomeSlideMobile(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 6) $this->abort(404);

        if (empty($_FILES['slide_image_mobile']) || $_FILES['slide_image_mobile']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/home');
        }

        $file = $_FILES['slide_image_mobile'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/content/home');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file['type'] !== 'image/png' || $ext !== 'png') {
            Session::flash('error', 'Only PNG images are allowed for the slider.');
            $this->redirect('/admin/content/home');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'Image must be under 5 MB.');
            $this->redirect('/admin/content/home');
        }

        $old = Settings::get("content_home_slide_{$slot}_mobile", '');
        if ($old && str_starts_with($old, '/uploads/slider/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $dir = UPLOAD_PATH . '/slider/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = "home_slide_{$slot}_mobile_" . time() . '.png';
        if (!Helpers::moveOptimizedImage($file['tmp_name'], 'image/png', $dir . $name)) {
            Session::flash('error', 'Failed to save image. Check directory permissions.');
            $this->redirect('/admin/content/home');
        }

        Settings::set("content_home_slide_{$slot}_mobile", '/uploads/slider/' . $name, 'content');
        ActivityLog::log('admin_content_saved', "Updated homepage slider mobile image slot $slot");
        Session::flash('success', "Slide $slot mobile image updated.");
        $this->redirect('/admin/content/home');
    }

    public function removeHomeSlideMobile(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 6) $this->abort(404);

        $old = Settings::get("content_home_slide_{$slot}_mobile", '');
        if ($old && str_starts_with($old, '/uploads/slider/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set("content_home_slide_{$slot}_mobile", '', 'content');
        ActivityLog::log('admin_content_saved', "Removed homepage slider mobile image slot $slot");
        Session::flash('success', "Slide $slot mobile image removed.");
        $this->redirect('/admin/content/home');
    }

    public function uploadFeaturesImage(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        if (empty($_FILES['features_image']) || $_FILES['features_image']['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/home');
        }

        $file = $_FILES['features_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/content/home');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];

        if (!in_array($file['type'], $allowedMimes) || !isset($extMap[$ext])) {
            Session::flash('error', 'Only JPG, PNG, or WebP images are allowed.');
            $this->redirect('/admin/content/home');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'Image must be under 5 MB.');
            $this->redirect('/admin/content/home');
        }

        $old = Settings::get('content_home_features_image', '');
        if ($old && str_starts_with($old, '/uploads/content/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $dir = UPLOAD_PATH . '/content/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = 'home_features_' . time() . '.' . $ext;
        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dir . $name)) {
            Session::flash('error', 'Failed to save image. Check directory permissions.');
            $this->redirect('/admin/content/home');
        }

        Settings::set('content_home_features_image', '/uploads/content/' . $name, 'content');
        ActivityLog::log('admin_content_saved', 'Updated features section image');
        Session::flash('success', 'Features image updated.');
        $this->redirect('/admin/content/home');
    }

    public function removeFeaturesImage(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $old = Settings::get('content_home_features_image', '');
        if ($old && str_starts_with($old, '/uploads/content/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set('content_home_features_image', '', 'content');
        ActivityLog::log('admin_content_saved', 'Removed features section image');
        Session::flash('success', 'Features image removed.');
        $this->redirect('/admin/content/home');
    }

    public function uploadHomeBanner(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $file = $_FILES['banner_image'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No file uploaded.');
            $this->redirect('/admin/content/home');
            return;
        }

        $mime = mime_content_type($file['tmp_name']);
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($mime !== 'image/png' || $ext !== 'png') {
            Session::flash('error', 'Only PNG images are accepted.');
            $this->redirect('/admin/content/home');
            return;
        }
        if ($file['size'] > 10 * 1024 * 1024) {
            Session::flash('error', 'File exceeds 10 MB limit.');
            $this->redirect('/admin/content/home');
            return;
        }

        $old = Settings::get('content_home_banner', '');
        if ($old && str_starts_with($old, '/uploads/banner/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $dir  = PUBLIC_PATH . '/uploads/banner/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $name = 'home_banner_' . time() . '.png';
        if (!Helpers::moveOptimizedImage($file['tmp_name'], 'image/png', $dir . $name)) {
            Session::flash('error', 'Failed to save image.');
            $this->redirect('/admin/content/home');
            return;
        }

        Settings::set('content_home_banner', '/uploads/banner/' . $name, 'content');
        ActivityLog::log('admin_content_saved', 'Updated homepage static banner image');
        Session::flash('success', 'Banner image updated.');
        $this->redirect('/admin/content/home');
    }

    public function removeHomeBanner(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $old = Settings::get('content_home_banner', '');
        if ($old && str_starts_with($old, '/uploads/banner/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set('content_home_banner', '', 'content');
        ActivityLog::log('admin_content_saved', 'Removed homepage static banner image');
        Session::flash('success', 'Banner removed.');
        $this->redirect('/admin/content/home');
    }

    public function uploadAboutImage(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 2) $this->abort(404);

        $file = $_FILES['about_image'] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/about');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/content/about');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg' => true, 'jpeg' => true, 'png' => true, 'webp' => true];

        if (!in_array($file['type'], $allowedMimes) || !isset($extMap[$ext])) {
            Session::flash('error', 'Only JPG, PNG, and WebP images are allowed.');
            $this->redirect('/admin/content/about');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'Image must be under 5 MB.');
            $this->redirect('/admin/content/about');
        }

        $key = "content_about_image{$slot}";
        $old = Settings::get($key, '');
        if ($old && str_starts_with($old, '/uploads/about/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $dir = UPLOAD_PATH . '/about/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = "about_image{$slot}_" . time() . '.' . $ext;
        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dir . $name)) {
            Session::flash('error', 'Failed to save image. Check directory permissions.');
            $this->redirect('/admin/content/about');
        }

        Settings::set($key, '/uploads/about/' . $name, 'content');
        ActivityLog::log('admin_content_saved', "Updated About page image $slot");
        Session::flash('success', "Image $slot updated.");
        $this->redirect('/admin/content/about');
    }

    public function removeAboutImage(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 2) $this->abort(404);

        $key = "content_about_image{$slot}";
        $old = Settings::get($key, '');
        if ($old && str_starts_with($old, '/uploads/about/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set($key, '', 'content');
        ActivityLog::log('admin_content_saved', "Removed About page image $slot");
        Session::flash('success', "Image $slot removed.");
        $this->redirect('/admin/content/about');
    }

    public function uploadIndustriesImage(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 2) $this->abort(404);

        $file = $_FILES['industries_image'] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            Session::flash('error', 'No file selected.');
            $this->redirect('/admin/content/industries');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Upload failed (error code ' . $file['error'] . ').');
            $this->redirect('/admin/content/industries');
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg' => true, 'jpeg' => true, 'png' => true, 'webp' => true];

        if (!in_array($file['type'], $allowedMimes) || !isset($extMap[$ext])) {
            Session::flash('error', 'Only JPG, PNG, and WebP images are allowed.');
            $this->redirect('/admin/content/industries');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            Session::flash('error', 'Image must be under 5 MB.');
            $this->redirect('/admin/content/industries');
        }

        $key = "content_industries_image{$slot}";
        $old = Settings::get($key, '');
        if ($old && str_starts_with($old, '/uploads/industries/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $dir = UPLOAD_PATH . '/industries/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $name = "industries_image{$slot}_" . time() . '.' . $ext;
        if (!Helpers::moveOptimizedImage($file['tmp_name'], $file['type'], $dir . $name)) {
            Session::flash('error', 'Failed to save image. Check directory permissions.');
            $this->redirect('/admin/content/industries');
        }

        Settings::set($key, '/uploads/industries/' . $name, 'content');
        ActivityLog::log('admin_content_saved', "Updated Industries page image $slot");
        Session::flash('success', "Image $slot updated.");
        $this->redirect('/admin/content/industries');
    }

    public function removeIndustriesImage(string $slot): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $slot = (int) $slot;
        if ($slot < 1 || $slot > 2) $this->abort(404);

        $key = "content_industries_image{$slot}";
        $old = Settings::get($key, '');
        if ($old && str_starts_with($old, '/uploads/industries/')) {
            $oldPath = PUBLIC_PATH . $old;
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        Settings::set($key, '', 'content');
        ActivityLog::log('admin_content_saved', "Removed Industries page image $slot");
        Session::flash('success', "Image $slot removed.");
        $this->redirect('/admin/content/industries');
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
                'seo_title'           => '',
                'seo_description'     => '',
                'seo_keyphrase'       => '',
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
                'testimonial_1_quote' => '"Managing our digital menu boards across three restaurant locations is now effortless. We can update promotions instantly from our phone."',
                'testimonial_1_name'  => 'Sarah Mitchell',
                'testimonial_1_role'  => 'Owner, Coastal Kitchen, Sydney NSW',
                'testimonial_2_quote' => '"DisplayManagerPro has saved us hours every week. Updating specials and menu prices across multiple screens takes just minutes."',
                'testimonial_2_name'  => 'James Carter',
                'testimonial_2_role'  => 'Manager, The Urban Plate, Melbourne VIC',
                'testimonial_3_quote' => '"We no longer need USB drives or manual screen updates. Everything is managed from one dashboard, even across multiple restaurants."',
                'testimonial_3_name'  => 'Sarah Johnson',
                'testimonial_3_role'  => 'Operations Manager, Urban Bites Kitchen, Chicago IL',
                'testimonial_4_quote' => '"An excellent solution for digital menu boards. We can instantly update pricing, promotions, and seasonal specials from anywhere."',
                'testimonial_4_name'  => 'David Wilson',
                'testimonial_4_role'  => 'Director, Sunset Burger Co., Miami FL',
                'testimonial_5_quote' => '"Managing screens across our restaurant group has never been easier. Updates are instant and the platform is incredibly user-friendly."',
                'testimonial_5_name'  => 'James Thompson',
                'testimonial_5_role'  => 'Owner, The Oak Kitchen, Manchester UK',
                'testimonial_6_quote' => '"DisplayManagerPro has streamlined our promotional displays and menu updates. The system works perfectly across all our locations."',
                'testimonial_6_name'  => 'Emma Roberts',
                'testimonial_6_role'  => 'Manager, Riverside Dining, Birmingham UK',
                'cta_title'           => 'Ready to modernise your screens?',
                'cta_subtitle'        => 'Start your free 14-day trial. No credit card required. Cancel anytime.',
                'cta_button'          => 'Get Started Free →',
                'slide_1_heading'     => '',
                'slide_1_sub'         => '',
                'slide_1_layout'      => 'left',
                'slide_1_bg_color'    => '#1e1b4b',
                'slide_1_text_color'  => 'light',
                'slide_1_overlay'     => '0',
                'slide_2_heading'     => '',
                'slide_2_sub'         => '',
                'slide_2_layout'      => 'left',
                'slide_2_bg_color'    => '#0a2540',
                'slide_2_text_color'  => 'light',
                'slide_2_overlay'     => '0',
                'slide_3_heading'     => '',
                'slide_3_sub'         => '',
                'slide_3_layout'      => 'left',
                'slide_3_bg_color'    => '#052e16',
                'slide_3_text_color'  => 'light',
                'slide_3_overlay'     => '0',
                'slide_4_heading'     => '',
                'slide_4_sub'         => '',
                'slide_4_layout'      => 'left',
                'slide_4_bg_color'    => '#1e1b4b',
                'slide_4_text_color'  => 'light',
                'slide_4_overlay'     => '0',
                'slide_5_heading'     => '',
                'slide_5_sub'         => '',
                'slide_5_layout'      => 'left',
                'slide_5_bg_color'    => '#1e1b4b',
                'slide_5_text_color'  => 'light',
                'slide_5_overlay'     => '0',
                'slide_6_heading'     => '',
                'slide_6_sub'         => '',
                'slide_6_layout'      => 'left',
                'slide_6_bg_color'    => '#1e1b4b',
                'slide_6_text_color'  => 'light',
                'slide_6_overlay'     => '0',
            ],
            'features' => [
                'seo_title'       => '',
                'seo_description' => '',
                'seo_keyphrase'   => '',
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
                'seo_title'       => '',
                'seo_description' => '',
                'seo_keyphrase'   => '',
                'hero_title'  => 'Transform Any TV Into A Powerful Digital Sign',
                'hero_body'   => 'DisplayManagerPro is a cloud-based digital signage platform that helps businesses manage TV screens, digital menu boards, promotional displays, and customer communications from anywhere. Whether you operate a restaurant in Sydney, a retail store in London, a salon in Auckland, or multiple business locations across the United States, DisplayManagerPro gives you complete control over your content from one simple dashboard.',
                's1_title'   => 'Designed For Growing Multi-Location Businesses',
                's1_body1'   => 'Managing content across multiple locations can be challenging. Traditional printed signage is costly, time-consuming, and difficult to keep updated.',
                's1_body2'   => 'DisplayManagerPro solves this problem by allowing businesses to centrally manage all screens from a secure cloud platform.',
                's1_intro'   => 'Whether you have 1 screen or 100 screens, you can:',
                's1_bullets' => "Update content instantly\nSchedule promotions and announcements\nMaintain consistent branding\nReduce printing costs\nImprove customer engagement\nManage all locations from one dashboard",
                's1_footer'  => 'This makes DisplayManagerPro ideal for franchises, hospitality groups, retail chains, healthcare providers, fitness centres, and corporate organisations.',
                's2_title'   => 'Increase Sales With Dynamic Digital Displays',
                's2_body1'   => 'Studies consistently show that digital signage attracts more attention than static printed materials.',
                's2_body2'   => 'Customers naturally look at screens displaying menus, promotions, product information, and announcements. This creates more opportunities to influence purchasing decisions at the point of sale.',
                's2_intro'   => 'Businesses use DisplayManagerPro to:',
                's2_bullets' => "Promote high-margin products\nAdvertise limited-time offers\nHighlight new arrivals\nDisplay seasonal campaigns\nCross-sell related products and services\nIncrease average transaction value",
                's2_footer'  => 'Instead of relying on outdated posters, your business can communicate with customers in real time.',
                's3_title'   => 'Why Digital Signage Is Essential For Modern Businesses',
                's3_body1'   => "Today's customers expect up-to-date information and engaging visual experiences.",
                's3_body2'   => 'Digital signage allows businesses to communicate more effectively while creating a professional and modern environment.',
                's3_b1_icon' => '🎯', 's3_b1_title' => 'Improve Customer Experience', 's3_b1_desc' => 'Display menus, services, promotions, and important information clearly and professionally.',
                's3_b2_icon' => '⏱️', 's3_b2_title' => 'Save Time',                   's3_b2_desc' => 'Update all screens remotely without manually replacing printed materials.',
                's3_b3_icon' => '💰', 's3_b3_title' => 'Reduce Costs',                's3_b3_desc' => 'Eliminate ongoing printing and distribution expenses.',
                's3_b4_icon' => '✅', 's3_b4_title' => 'Maintain Brand Consistency',  's3_b4_desc' => 'Ensure every location displays the same branding, messaging, and promotions.',
                's3_b5_icon' => '📈', 's3_b5_title' => 'Scale Easily',                's3_b5_desc' => 'Add new locations and screens without changing your workflow.',
                's4_title'    => 'Supporting Businesses Across Australia, New Zealand, United Kingdom & United States',
                's4_au_title' => 'Australia',    's4_au_cities' => 'Sydney, Melbourne, Brisbane, Perth, Adelaide, Gold Coast and regional locations.',
                's4_nz_title' => 'New Zealand',  's4_nz_cities' => 'Auckland, Wellington, Christchurch, Hamilton, Tauranga and beyond.',
                's4_uk_title' => 'United Kingdom','s4_uk_cities' => 'London, Manchester, Birmingham, Liverpool, Leeds, Bristol and nationwide.',
                's4_us_title' => 'United States', 's4_us_cities' => 'New York, Los Angeles, Chicago, Houston, Dallas, Miami, Seattle and across all states.',
                's4_footer'   => 'No matter where your business operates, DisplayManagerPro provides a simple and reliable way to manage your digital displays from anywhere.',
                'cta_title'  => 'Ready To Modernise Your Business Displays?',
                'cta_body1'  => 'Join businesses worldwide using DisplayManagerPro to manage digital menu boards, promotions, announcements, and customer communications from one powerful cloud platform.',
                'cta_body2'  => 'Start your free trial today and discover how easy it is to manage every screen from anywhere.',
                'cta_button' => 'Start Your 14-Day Free Trial →',
            ],
            'faq' => [
                'seo_title'       => '',
                'seo_description' => '',
                'seo_keyphrase'   => '',
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
                'seo_title'       => '',
                'seo_description' => '',
                'seo_keyphrase'   => '',
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
                'col2_links' => "Contact|/contact\nPrivacy Policy|/privacy-policy\nTerms & Conditions|/terms\nRefund Policy|/refund-policy",
                'col3_title' => 'Account',
                'col3_links' => "Sign In|/login\nStart Free Trial|/register",
                'copyright'  => '© ' . date('Y') . ' SignageCloud. All rights reserved.',
            ],
            'pricing' => [
                'seo_title'       => '',
                'seo_description' => '',
                'seo_keyphrase'   => '',
            ],
            'about' => [
                'seo_title'         => '',
                'seo_description'   => '',
                'seo_keyphrase'     => '',
                'hero_subtitle'     => 'At Display Manager Pro, we help businesses update TV screens, digital menu boards, promotions, announcements, and in-store displays from one simple cloud-based dashboard.',
                's1_badge'          => 'Who We Help',
                's1_title'          => 'Built for businesses of all sizes',
                's1_body1'          => 'Whether you operate a restaurant, café, salon, retail store, clinic, hotel, showroom, or multi-location business, Display Manager Pro makes it easy to keep your content fresh, engaging, and up to date — without USB drives, printing costs, or manual screen updates.',
                's1_body2'          => 'Our mission is simple: make digital signage affordable, easy to manage, and accessible for businesses of all sizes.',
                's1_stat_num'       => '500+',
                's1_stat_label'     => 'Businesses worldwide',
                's1_industries'     => "Restaurants & Cafés\nRetail Stores\nSalons & Spas\nHotels & Resorts\nMedical Clinics\nFitness Centers\nCorporate Offices\nSupermarkets\nShowrooms\nMulti-Location Businesses",
                's2_badge'          => 'The Smarter Way',
                's2_title'          => 'The smarter way to manage digital signage',
                's2_body1'          => 'Traditional screen management can be time-consuming and costly. Businesses often rely on USB drives, manual updates, and staff intervention to change menus, promotions, pricing, and announcements.',
                's2_body2'          => 'Display Manager Pro eliminates these challenges with a powerful cloud digital signage platform. With just a few clicks, your content is automatically updated across all connected screens.',
                's2_stat_num'       => '14-day',
                's2_stat_label'     => 'Free trial, no card needed',
                's2_bullets'        => "Update screens remotely from any device\nManage multiple TV displays from one dashboard\nSchedule and organise content effortlessly\nDisplay menus, promotions, announcements & advertising\nKeep every location consistent and up to date\nSave time and reduce operational costs",
                's3_badge'          => 'Why Choose Us',
                's3_title'          => 'Why businesses choose Display Manager Pro',
                's3_subtitle'       => 'Enterprise-level digital signage without enterprise-level complexity or pricing.',
                's3_r1_icon'        => '☁️', 's3_r1_title' => 'Easy Cloud Management',       's3_r1_desc' => 'Update digital displays anytime, anywhere through a secure web-based dashboard.',
                's3_r2_icon'        => '🖥️', 's3_r2_title' => 'Multi-Screen Control',         's3_r2_desc' => 'Manage multiple TV screens and locations from a single account.',
                's3_r3_icon'        => '⚡', 's3_r3_title' => 'Instant Content Updates',      's3_r3_desc' => 'Change menus, promotions, pricing, and announcements in seconds.',
                's3_r4_icon'        => '👆', 's3_r4_title' => 'No Technical Skills Required', 's3_r4_desc' => 'Simple, user-friendly software designed for business owners and staff.',
                's3_r5_icon'        => '🔒', 's3_r5_title' => 'Secure & Reliable',            's3_r5_desc' => 'Your content is securely hosted and delivered to connected screens automatically.',
                's3_r6_icon'        => '💰', 's3_r6_title' => 'Affordable Digital Signage',   's3_r6_desc' => 'Enterprise-level features without enterprise-level pricing.',
                'vision_badge'      => 'Our Vision',
                'vision_title'      => 'Every business deserves great digital signage',
                'vision_body1'      => 'We believe every business should have access to professional digital signage technology without expensive hardware, complicated software, or long-term contracts.',
                'vision_body2'      => 'Our goal is to help businesses communicate more effectively, increase customer engagement, promote products and services, and create better in-store experiences through modern digital display solutions.',
                'vision_body3'      => 'As businesses continue to embrace digital transformation, Display Manager Pro is committed to providing an easy-to-use, reliable, and scalable platform that grows with your needs.',
                'cta_title'         => 'Start Your Free 14-Day Trial',
                'cta_body1'         => 'Experience the easiest way to manage digital signage, TV displays, digital menu boards, and promotional screens.',
                'cta_body2'         => 'Join businesses worldwide using Display Manager Pro to simplify screen management, improve customer engagement, and keep their content fresh.',
                'cta_button'        => 'Start Free Trial Today →',
            ],
            'branding' => [],
            'privacy' => [
                'title'        => 'Privacy Policy',
                'last_updated' => date('F j, Y'),
                'body'         => '<h2>1. Information We Collect</h2><p>We collect information you provide directly to us when you create an account, subscribe to a plan, or contact support. This includes your name, email address, and payment details processed securely via PayPal.</p><h2>2. How We Use Your Information</h2><p>We use the information we collect to provide, maintain, and improve our services, process transactions, send transactional emails, and respond to support requests. We do not sell your personal information to third parties.</p><h2>3. Cookies</h2><p>We use session cookies to keep you logged in. We do not use third-party tracking cookies. Google Analytics may be enabled by the site operator to understand traffic patterns.</p><h2>4. Data Storage & Security</h2><p>Your data is stored on secure servers. We use industry-standard encryption for data in transit (HTTPS) and apply security best practices including CSRF protection and hashed passwords.</p><h2>5. Data Retention</h2><p>We retain your account data for as long as your account is active. If you cancel, your data is retained for 30 days before deletion, after which it is permanently removed.</p><h2>6. Third-Party Services</h2><p>We use PayPal for payment processing. Their use of your information is governed by their own privacy policy. We do not share your data with any other third parties.</p><h2>7. Your Rights</h2><p>You have the right to access, correct, or delete your personal data at any time. To make a request, please contact us via our support ticket system or the contact form.</p><h2>8. Changes to This Policy</h2><p>We may update this Privacy Policy from time to time. We will notify you of any significant changes by email or by posting a notice on our website.</p><h2>9. Contact Us</h2><p>If you have questions about this Privacy Policy, please contact us through our support system.</p>',
            ],
            'terms' => [
                'title'        => 'Terms & Conditions',
                'last_updated' => date('F j, Y'),
                'body'         => '<h2>1. Acceptance of Terms</h2><p>By accessing or using our service, you agree to be bound by these Terms & Conditions. If you do not agree to these terms, please do not use our service.</p><h2>2. Description of Service</h2><p>We provide a cloud-based digital signage management platform that allows you to upload, manage, and display content on TV screens via a unique display URL.</p><h2>3. Account Responsibilities</h2><p>You are responsible for maintaining the confidentiality of your account credentials. You agree to notify us immediately of any unauthorised access to your account. You are responsible for all activity that occurs under your account.</p><h2>4. Acceptable Use</h2><p>You agree not to use the service to upload, store, or display any content that is unlawful, harmful, defamatory, obscene, or that infringes the intellectual property rights of others. We reserve the right to suspend accounts that violate these terms.</p><h2>5. Subscription & Payment</h2><p>Subscription fees are billed in advance on a monthly or annual basis via PayPal. By subscribing, you authorise us to charge your PayPal account at the applicable rate for your plan. All fees are non-refundable except as set out in our Refund Policy.</p><h2>6. Trial Period</h2><p>Where offered, a free trial period is provided to evaluate the service. At the end of the trial, your subscription will begin and your selected payment method will be charged.</p><h2>7. Cancellation</h2><p>You may cancel your subscription at any time from the Billing section of your dashboard. Cancellation takes effect at the end of the current billing period. You will retain access to the service until the period ends.</p><h2>8. Intellectual Property</h2><p>You retain full ownership of all content you upload to the platform. By uploading content, you grant us a limited licence to store and serve that content solely for the purpose of providing the service.</p><h2>9. Limitation of Liability</h2><p>To the maximum extent permitted by law, we shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the service, including loss of data, revenue, or business.</p><h2>10. Changes to Terms</h2><p>We reserve the right to modify these Terms at any time. Continued use of the service after changes are posted constitutes your acceptance of the revised Terms.</p><h2>11. Governing Law</h2><p>These Terms are governed by the laws of Queensland, Australia. Any disputes shall be subject to the exclusive jurisdiction of the courts of Queensland.</p>',
            ],
            'refund' => [
                'title'        => 'Refund Policy',
                'last_updated' => date('F j, Y'),
                'body'         => '<h2>1. Free Trial</h2><p>We offer a free trial period on selected plans with no payment required. This allows you to fully evaluate the service before committing to a subscription. No refund is applicable during the trial period as no payment is taken.</p><h2>2. Subscription Payments</h2><p>All subscription payments are processed in advance for the billing period (monthly or annual). Once a billing period has commenced and payment has been processed, we generally do not offer refunds for that period.</p><h2>3. Cancellation</h2><p>You may cancel your subscription at any time. When you cancel, you retain access to the service until the end of your current paid billing period. No partial refunds are issued for unused time within an active billing period.</p><h2>4. Annual Plans</h2><p>Annual subscriptions are billed as a single upfront payment. If you cancel an annual plan within 14 days of purchase and have not made significant use of the service, you may be eligible for a full refund. Requests must be submitted via our support system within 14 days of the annual charge.</p><h2>5. Exceptional Circumstances</h2><p>We consider refund requests on a case-by-case basis in exceptional circumstances — such as duplicate charges, billing errors, or extended service outages caused by us. To request a refund under exceptional circumstances, please contact us through our support ticket system with details of your request.</p><h2>6. How to Request a Refund</h2><p>All refund requests must be submitted via our support ticket system. Please include your account email address, the date of the charge, and the reason for your request. We aim to respond to all refund requests within 3 business days.</p><h2>7. Processing</h2><p>Approved refunds are processed back to the original PayPal account used for payment. Processing time is typically 3–5 business days after approval.</p><h2>8. Contact</h2><p>If you have questions about this Refund Policy or wish to submit a refund request, please open a support ticket from your dashboard or contact us via our website.</p>',
            ],
            default => [],
        };
    }
}
