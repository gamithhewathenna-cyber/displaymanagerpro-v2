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
      <?php for ($col = 1; $col <= 3; $col++):
        $colTitle = ContentController::get('footer', "col{$col}_title");
        $colLinks = ContentController::get('footer', "col{$col}_links");
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
    <div class="border-t border-gray-800 pt-8 text-xs text-center">
      <?= Helpers::e(ContentController::get('footer', 'copyright', '© ' . date('Y') . ' ' . Settings::get('company_name', APP_NAME) . '. All rights reserved.')) ?>
    </div>
  </div>
</footer>

<script>
  setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000);
</script>
</body>
</html>
