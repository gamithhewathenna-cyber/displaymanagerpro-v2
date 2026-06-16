<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
  $_seo_page        = basename($content_view ?? '');
  $_seo_title       = ContentController::get($_seo_page, 'seo_title', '');
  $_seo_description = ContentController::get($_seo_page, 'seo_description', '');
  $_seo_keyphrase   = ContentController::get($_seo_page, 'seo_keyphrase', '');
  $_company         = Settings::get('company_name', APP_NAME);
  $_page_title      = $title ?? APP_NAME;
  $_full_title      = $_seo_title !== ''
    ? Helpers::e($_seo_title)
    : Helpers::e($_page_title) . ' – ' . Helpers::e($_company);
  $_meta_desc       = $_seo_description !== ''
    ? $_seo_description
    : 'Cloud digital signage management for restaurants, cafes, and retail. Manage all your TV screens from one dashboard.';
  $_ga_id           = Settings::get('ga_measurement_id', '');
  $_gsc_code        = Settings::get('gsc_verification', '');
?>
<title><?= $_full_title ?></title>
<?php $_fav = Settings::get('site_favicon',''); if ($_fav): ?><link rel="icon" href="<?= Helpers::e($_fav) ?>?v=<?= filemtime(PUBLIC_PATH.$_fav) ?>"><?php endif; ?>
<meta name="description" content="<?= Helpers::e($_meta_desc) ?>">
<?php if ($_seo_keyphrase !== ''): ?>
<meta name="keywords" content="<?= Helpers::e($_seo_keyphrase) ?>">
<?php endif; ?>
<?php if ($_gsc_code !== ''): ?>
<meta name="google-site-verification" content="<?= Helpers::e($_gsc_code) ?>">
<?php endif; ?>
<?php if ($_ga_id !== ''): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= Helpers::e($_ga_id) ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?= Helpers::e($_ga_id) ?>');
</script>
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans: ['Poppins', 'sans-serif'] },
        colors: {
          primary: { 50:'#f0f0ff',100:'#e0e0ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca' },
          surface: '#fafafa',
        }
      }
    }
  }
</script>
<style>
  body { font-family: 'Poppins', sans-serif; }
  .gradient-text { background: linear-gradient(135deg, #6366f1, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
  .hero-gradient { background: linear-gradient(135deg, #0f0f1a 0%, #1a1a3e 50%, #0f0f1a 100%); }
  @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  .float { animation: float 4s ease-in-out infinite; }
</style>
</head>
<body class="bg-white text-gray-900">

<!-- Nav -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-20">
    <?php $__logo = Settings::get('website_logo', '') ?: Settings::get('company_logo', ''); ?>
    <a href="/" class="flex items-center gap-2.5 font-bold text-2xl min-w-0">
      <?php if ($__logo): ?>
        <img src="<?= Helpers::e($__logo) ?>" alt="<?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>" class="max-h-12 max-w-[210px] object-contain">
      <?php else: ?>
        <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>
      <?php endif; ?>
    </a>
    <div class="hidden md:flex items-center gap-8 text-base font-medium text-gray-600">
      <a href="/features" class="hover:text-primary-600 transition-colors">Features</a>
      <a href="/pricing" class="hover:text-primary-600 transition-colors">Pricing</a>
      <a href="/industries" class="hover:text-primary-600 transition-colors">Industries</a>
      <a href="/faq" class="hover:text-primary-600 transition-colors">FAQ</a>
    </div>
    <div class="flex items-center gap-3">
      <a href="/login" class="text-base font-medium text-gray-700 hover:text-primary-600 transition-colors">Sign In</a>
      <a href="/register" class="bg-primary-500 hover:bg-primary-600 text-white text-base font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-sm">Start Free Trial</a>
    </div>
  </div>
</nav>

<main class="pt-20">
  <?php if ($msg = Session::getFlash('success')): ?>
    <div class="fixed top-20 right-4 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-medium max-w-sm" id="flash-msg">
      <?= Helpers::e($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($msg = Session::getFlash('error')): ?>
    <div class="fixed top-20 right-4 z-50 bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-medium max-w-sm" id="flash-msg">
      <?= Helpers::e($msg) ?>
    </div>
  <?php endif; ?>

  <?php require VIEWS_PATH . '/' . $content_view . '.php'; ?>
</main>

<!-- Footer (CMS-driven) -->
<footer class="bg-gray-900 text-gray-400 py-16 mt-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 mb-12">
      <div class="col-span-2 md:col-span-1">
        <div class="flex items-center gap-2 text-white font-bold text-lg mb-4">
          <?php if ($__logo): ?>
            <div class="bg-white rounded-md px-2 py-1 inline-block">
              <img src="<?= Helpers::e($__logo) ?>" alt="<?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>" class="max-h-7 max-w-[140px] object-contain block">
            </div>
          <?php else: ?>
            <div class="w-7 h-7 bg-primary-500 rounded-md flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>
          <?php endif; ?>
        </div>
        <p class="text-sm leading-relaxed"><?= Helpers::e(ContentController::get('footer', 'tagline', 'Cloud digital signage for restaurants, cafes, retail, and hospitality.')) ?></p>
      </div>
      <?php
        $_footerDefaults = [
          1 => ['title' => 'Product',  'links' => "Features|/features\nPricing|/pricing\nIndustries|/industries\nFAQ|/faq"],
          2 => ['title' => 'Company',  'links' => "Contact|/contact\nPrivacy Policy|/privacy-policy\nTerms & Conditions|/terms\nRefund Policy|/refund-policy"],
          3 => ['title' => 'Account',  'links' => "Sign In|/login\nStart Free Trial|/register"],
        ];
      ?>
      <?php for ($col = 1; $col <= 3; $col++):
        $colTitle = ContentController::get('footer', "col{$col}_title", $_footerDefaults[$col]['title']);
        $colLinks = ContentController::get('footer', "col{$col}_links", $_footerDefaults[$col]['links']);
        if (!$colTitle && !$colLinks) continue;
        $links = array_filter(explode("\n", $colLinks));
      ?>
      <div>
        <h4 class="text-white font-semibold text-sm mb-4"><?= Helpers::e($colTitle) ?></h4>
        <ul class="space-y-2 text-sm">
          <?php foreach ($links as $link):
            $parts = explode('|', trim($link), 2);
            if (count($parts) < 2) continue;
            [$label, $url] = $parts;
          ?>
          <li><a href="<?= Helpers::e(trim($url)) ?>" class="hover:text-white transition-colors"><?= Helpers::e(trim($label)) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endfor; ?>
    </div>
    <div class="border-t border-gray-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-5">
      <div class="text-xs text-gray-500 text-center sm:text-left">
        <?= Helpers::e(ContentController::get('footer', 'copyright', '© ' . date('Y') . ' ' . Settings::get('company_name', APP_NAME) . '. All rights reserved.')) ?>
      </div>

      <!-- Payment badges -->
      <div class="flex items-center gap-2">

        <!-- PayPal -->
        <div class="bg-white rounded-md px-2.5 flex items-center justify-center" style="height:28px;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 20" height="14" width="56" aria-label="PayPal">
            <path fill="#003087" d="M9.5 2h5.8c2.4 0 4 1.5 3.6 3.9-.6 3.9-3.3 5.3-6.6 5.3H10l-1.1 6.3H5.6L9.5 2zm2.4 7c1.7 0 3-.8 3.3-2.5.2-1.3-.5-2-1.9-2h-2l-.9 4.5h1.5z"/>
            <path fill="#009cde" d="M21 2h5.8c2.4 0 4 1.5 3.6 3.9-.6 3.9-3.3 5.3-6.6 5.3h-2.3L20.4 17.5h-3.3L21 2zm2.4 7c1.7 0 3-.8 3.3-2.5.2-1.3-.5-2-1.9-2h-2l-.9 4.5h1.5z"/>
            <path fill="#003087" d="M34.5 12.5c.1-.7.6-1.1 1.3-1.1.6 0 1 .4.9 1.1H34.5zm4.3 1.8c-.5 1-1.6 1.6-2.9 1.6-2.1 0-3.2-1.4-2.8-3.5.4-2.1 2.1-3.5 4.2-3.5 2 0 3.1 1.4 2.7 3.5l-.1.5h-5.3c-.1.9.4 1.4 1.2 1.4.5 0 .9-.2 1.1-.6l1.9.6z"/>
            <path fill="#003087" d="M40.2 9h2.2l-.2 1c.4-.7 1.1-1.1 1.9-1.1.2 0 .4 0 .5.1l-.4 2c-.2-.1-.4-.1-.6-.1-1 0-1.7.7-1.9 1.8l-.5 3h-2.2L40.2 9z"/>
            <path fill="#003087" d="M45.1 9h2.2l-1.5 8.5h-2.2L45.1 9zm.5-2.8c0-.7.5-1.2 1.2-1.2s1.1.5 1 1.2c-.1.7-.6 1.2-1.3 1.2-.6 0-1-.5-.9-1.2z"/>
            <path fill="#003087" d="M48.4 9h2.1l.5 4.5 2.2-4.5h2.3l-3.9 7.5c-.7 1.3-1.5 2-2.8 2-.5 0-.9-.1-1.2-.2l.4-1.7c.2.1.4.1.6.1.5 0 .8-.2 1.1-.7l.2-.4L48.4 9z"/>
            <path fill="#009cde" d="M60.9 11.6c.2-1-.3-1.6-1.1-1.6-1 0-1.7.8-1.9 1.9-.2 1 .3 1.6 1.1 1.6 1 0 1.7-.8 1.9-1.9zm-5.4 6h-2.2l2.1-11.5h2.1l-.2 1.1c.6-.8 1.4-1.3 2.5-1.3 1.9 0 3 1.4 2.5 3.6-.4 2.1-2 3.5-3.8 3.5-1 0-1.7-.5-2-1.2l-.9 4.8h-.1z"/>
            <path fill="#009cde" d="M66.4 12.5c.1-.7.6-1.1 1.3-1.1.6 0 1 .4.9 1.1h-2.2zm4.3 1.8c-.5 1-1.6 1.6-2.9 1.6-2.1 0-3.2-1.4-2.8-3.5.4-2.1 2.1-3.5 4.2-3.5 2 0 3.1 1.4 2.7 3.5l-.1.5h-5.3c-.1.9.4 1.4 1.2 1.4.5 0 .9-.2 1.1-.6l1.9.6z"/>
            <path fill="#009cde" d="M72 9h2.1l-.2 1c.4-.7 1.1-1.1 1.9-1.1.2 0 .4 0 .5.1l-.4 2c-.2-.1-.4-.1-.6-.1-1 0-1.7.7-1.9 1.8l-.5 3H70.7L72 9z"/>
          </svg>
        </div>

        <!-- Visa -->
        <div class="rounded-md flex items-center justify-center px-2.5" style="height:28px;background:#1A1F71;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 16" height="11" width="34" aria-label="Visa">
            <text x="25" y="12.5" text-anchor="middle" font-family="Arial,Helvetica,sans-serif" font-weight="900" font-size="13" fill="white" letter-spacing="1.5">VISA</text>
          </svg>
        </div>

        <!-- Mastercard -->
        <div class="bg-white rounded-md px-1.5 flex items-center justify-center" style="height:28px;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 24" height="18" width="28" aria-label="Mastercard">
            <circle cx="14" cy="12" r="10" fill="#EB001B"/>
            <circle cx="24" cy="12" r="10" fill="#F79E1B"/>
            <path d="M19 4.27a10 10 0 010 15.46A10 10 0 0119 4.27z" fill="#FF5F00"/>
          </svg>
        </div>

      </div>
    </div>
  </div>
</footer>

<script>
  setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000);
</script>
</body>
</html>
