<?php
/**
 * SignageCloud – Front Controller
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/app.php';

$lockFile = ROOT_PATH . '/install/install.lock';
$uriPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isInstallRoute = (strpos($uriPath, '/install') === 0);

if (!file_exists($lockFile) && !$isInstallRoute) {
    header('Location: /install');
    exit;
}

require_once APP_PATH . '/helpers/Database.php';
require_once APP_PATH . '/helpers/Core.php';
require_once APP_PATH . '/helpers/Helpers.php';
require_once APP_PATH . '/helpers/Services.php';
require_once APP_PATH . '/helpers/StorageService.php';
require_once APP_PATH . '/models/UserSettings.php';
require_once APP_PATH . '/models/Models.php';
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/AppControllers.php';
require_once APP_PATH . '/controllers/BillingDisplayControllers.php';
require_once APP_PATH . '/controllers/AdminController.php';
require_once APP_PATH . '/controllers/MarketingController.php';
require_once APP_PATH . '/controllers/ContentController.php';
require_once APP_PATH . '/controllers/InstallerController.php';

Session::start();

// ── Maintenance Mode ──────────────────────────────────────────────────────────
$_skipMaint = in_array($uriPath, ['/login', '/logout'])
    || strpos($uriPath, '/admin')   === 0
    || strpos($uriPath, '/display') === 0
    || $uriPath === '/billing/webhook'
    || $isInstallRoute;

if (!$_skipMaint && file_exists($lockFile)) {
    try {
        if (Settings::get('maintenance_mode', '0') === '1') {
            $userSession = Session::get('user');
            $isAdmin     = isset($userSession['role']) && $userSession['role'] === 'admin';
            if (!$isAdmin) {
                http_response_code(503);
                $siteName           = Settings::get('company_name', 'SignageCloud');
                $maintenanceMessage = Settings::get('maintenance_message', 'We are currently performing scheduled maintenance. We\'ll be back shortly.');
                require VIEWS_PATH . '/maintenance.php';
                exit;
            }
        }
    } catch (Exception $e) { /* DB not ready, skip */ }
}

// ── Router ────────────────────────────────────────────────────────────────────
$router = new Router();
$uri    = $uriPath;
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$router->get('/install',        'InstallerController', 'index');
$router->post('/install',       'InstallerController', 'process');
$router->get('/install/{step}', 'InstallerController', 'step');

$router->get('/',           'MarketingController', 'home');
$router->get('/features',   'MarketingController', 'features');
$router->get('/pricing',    'MarketingController', 'pricing');
$router->get('/industries', 'MarketingController', 'industries');
$router->get('/faq',        'MarketingController', 'faq');
$router->get('/contact',    'MarketingController', 'contact');
$router->post('/contact',   'MarketingController', 'contactSubmit');

$router->get('/login',                  'AuthController', 'showLogin');
$router->post('/login',                 'AuthController', 'login');
$router->get('/register',               'AuthController', 'showRegister');
$router->post('/register',              'AuthController', 'register');
$router->get('/logout',                 'AuthController', 'logout');
$router->get('/verify-email/{token}',   'AuthController', 'verifyEmail');
$router->get('/forgot-password',        'AuthController', 'showForgotPassword');
$router->post('/forgot-password',       'AuthController', 'forgotPassword');
$router->get('/reset-password/{token}', 'AuthController', 'showResetPassword');
$router->post('/reset-password',        'AuthController', 'resetPassword');

$router->get('/dashboard', 'DashboardController', 'index');
$router->get('/profile',   'DashboardController', 'profile');
$router->post('/profile',  'DashboardController', 'updateProfile');

$router->get('/channels',                           'ChannelController', 'index');
$router->get('/channels/create',                    'ChannelController', 'create');
$router->post('/channels',                          'ChannelController', 'store');
$router->get('/channels/{id}',                      'ChannelController', 'show');
$router->get('/channels/{id}/edit',                 'ChannelController', 'edit');
$router->post('/channels/{id}',                     'ChannelController', 'update');
$router->post('/channels/{id}/delete',              'ChannelController', 'delete');
$router->post('/channels/{id}/slides',              'ChannelController', 'addSlide');
$router->post('/channels/{id}/slides/{sid}/delete', 'ChannelController', 'removeSlide');
$router->post('/channels/{id}/reorder',             'ChannelController', 'reorderSlides');

$router->get('/media',              'MediaController', 'index');
$router->post('/media/upload',      'MediaController', 'upload');
$router->post('/media/{id}/delete', 'MediaController', 'delete');

$router->get('/billing',           'BillingController', 'index');
$router->post('/billing/checkout', 'BillingController', 'checkout');
$router->get('/billing/success',   'BillingController', 'checkoutSuccess');
$router->get('/billing/portal',    'BillingController', 'portal');
$router->post('/billing/cancel',   'BillingController', 'cancel');
$router->post('/billing/webhook',  'BillingController', 'webhook');

$router->get('/support',             'SupportController', 'index');
$router->get('/support/create',      'SupportController', 'create');
$router->post('/support',            'SupportController', 'store');
$router->get('/support/{id}',        'SupportController', 'show');
$router->post('/support/{id}/reply', 'SupportController', 'reply');

$router->get('/display/{slug}',      'DisplayController', 'show');
$router->get('/display/{slug}/data', 'DisplayController', 'data');

$router->get('/admin',                                'AdminController', 'dashboard');
$router->get('/admin/customers',                      'AdminController', 'customers');
$router->get('/admin/customers/{id}',                 'AdminController', 'viewCustomer');
$router->post('/admin/customers/{id}/suspend',        'AdminController', 'suspendCustomer');
$router->post('/admin/customers/{id}/activate',       'AdminController', 'activateCustomer');
$router->post('/admin/customers/{id}/delete',         'AdminController', 'deleteCustomer');
$router->post('/admin/customers/{id}/reset-password', 'AdminController', 'resetCustomerPassword');
$router->get('/admin/plans',                          'AdminController', 'plans');
$router->get('/admin/plans/{id}/edit',                'AdminController', 'editPlan');
$router->post('/admin/plans/{id}',                    'AdminController', 'updatePlan');
$router->get('/admin/tickets',                        'AdminController', 'tickets');
$router->get('/admin/tickets/{id}',                   'SupportController', 'show');
$router->post('/admin/tickets/{id}/reply',            'SupportController', 'reply');
$router->post('/admin/tickets/{id}/close',            'AdminController', 'closeTicket');
$router->post('/admin/tickets/{id}/reopen',           'AdminController', 'reopenTicket');
$router->get('/admin/revenue',                        'AdminController', 'revenue');
$router->get('/admin/settings',                       'AdminController', 'settings');
$router->post('/admin/settings',                      'AdminController', 'saveSettings');
$router->post('/admin/settings/logo',                 'AdminController', 'uploadLogo');
$router->post('/admin/settings/logo/remove',          'AdminController', 'removeLogo');
$router->post('/admin/settings/test-email',           'AdminController', 'testEmail');

// ─ Content Manager (Admin CMS)
$router->get('/admin/content',                          'ContentController', 'index');
$router->post('/admin/content/branding/logo',           'ContentController', 'uploadWebsiteLogo');
$router->post('/admin/content/branding/logo/remove',    'ContentController', 'removeWebsiteLogo');
$router->get('/admin/content/{page}',                   'ContentController', 'page');
$router->post('/admin/content/{page}/save',             'ContentController', 'savePage');

$router->dispatch($uri, $method);
