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
  $_pixel_code      = Settings::get('meta_pixel_code', '');

  // Canonical: strip query string, use configured app_url as base to avoid http/www drift
  $_app_url_base  = Helpers::appUrl();
  $_canon_path    = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
  $_canonical_url = $_app_url_base . $_canon_path;

  // Per-page meta description override (used by blog posts and other views)
  if (!empty($meta_description)) {
      $_meta_desc = $meta_description;
  }
?>
<title><?= $_full_title ?></title>
<?php $_fav = Settings::get('site_favicon',''); if ($_fav): ?><link rel="icon" href="<?= Helpers::e($_fav) ?>?v=<?= filemtime(PUBLIC_PATH.$_fav) ?>"><?php endif; ?>
<meta name="robots" content="index, follow">
<link rel="canonical" href="<?= Helpers::e($_canonical_url) ?>">
<meta name="description" content="<?= Helpers::e($_meta_desc) ?>">
<?php if ($_seo_keyphrase !== ''): ?>
<meta name="keywords" content="<?= Helpers::e($_seo_keyphrase) ?>">
<?php endif; ?>
<?php if (!empty($geo_region)): ?>
<meta name="geo.region" content="<?= Helpers::e($geo_region) ?>">
<meta name="geo.placename" content="<?= Helpers::e($city['name'] ?? '') ?>">
<?php endif; ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>",
  "url": "<?= Helpers::e(Helpers::appUrl() . '/') ?>",
  "description": "Cloud-based digital signage platform for businesses in Australia, New Zealand, United Kingdom, and United States.",
  "areaServed": ["AU","NZ","GB","US"],
  "sameAs": [
    <?php
      $_fb2 = Settings::get('facebook_url','');
      $_ig2 = Settings::get('instagram_url','');
      $_gb2 = Settings::get('google_business_url','');
      $social = array_filter([$_fb2, $_ig2, $_gb2]);
      echo implode(',', array_map(fn($s) => '"' . addslashes($s) . '"', $social));
    ?>
  ]
}
</script>
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
<?php if ($_pixel_code !== ''): ?>
<script>
(function(){
  function loadPixel(){
    var tmp = document.createElement('div');
    tmp.innerHTML = <?= json_encode($_pixel_code) ?>;
    tmp.querySelectorAll('script').forEach(function(s){
      var n = document.createElement('script');
      if (s.src) { n.src = s.src; n.async = true; } else { n.textContent = s.textContent; }
      document.head.appendChild(n);
    });
  }
  if (document.readyState === 'complete') { loadPixel(); }
  else { window.addEventListener('load', loadPixel); }
})();
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
          primary: { 50:'#fdf2f8',100:'#fce7f3',200:'#fbcfe8',300:'#f9a8d4',400:'#f472b6',500:'#ec4899',600:'#db2777',700:'#be185d',800:'#9d174d',900:'#831843' },
          surface: '#fafafa',
        }
      }
    }
  }
</script>
<style>
  body { font-family: 'Poppins', sans-serif; }
  .gradient-text { background: linear-gradient(135deg, #C218C8 0%, #F218A0 35%, #FF4A6E 65%, #FF7A1A 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
  .hero-gradient { background: linear-gradient(135deg, #0f0f1a 0%, #1a1a3e 50%, #0f0f1a 100%); }
  .hero-light { background: linear-gradient(135deg, #fdf2f8 0%, #ffffff 60%, #fce7f3 100%); }
  @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  .float { animation: float 4s ease-in-out infinite; }
  #mobile-menu { transition: max-height 0.3s ease, opacity 0.3s ease; max-height: 0; opacity: 0; overflow: hidden; }
  #mobile-menu.open { max-height: 400px; opacity: 1; }

  /* Scroll-reveal for page sections */
  .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
  .reveal.is-visible { opacity: 1; transform: translateY(0); }

  /* Fade lazy-loaded images in once they've actually loaded, instead of popping in */
  img[loading="lazy"] { opacity: 0; transition: opacity .5s ease; }
  img[loading="lazy"].img-loaded { opacity: 1; }

  @media (prefers-reduced-motion: reduce) {
    .reveal { opacity: 1; transform: none; transition: none; }
    img[loading="lazy"] { opacity: 1; transition: none; }
  }
</style>
<noscript><style>.reveal, img[loading="lazy"] { opacity: 1 !important; transform: none !important; }</style></noscript>
</head>
<body class="bg-white text-gray-900">

<?php
  // Social and contact vars defined once — reused in top bar and footer
  $_fb            = Settings::get('facebook_url', '');
  $_ig            = Settings::get('instagram_url', '');
  $_gb            = Settings::get('google_business_url', '');
  $_contact_email = Settings::get('smtp_from_email', '');
?>

<!-- TOP INFO BAR -->
<?php if ($_contact_email || $_fb || $_ig || $_gb): ?>
<div class="bg-primary-600 text-white text-xs">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2.5 flex items-center justify-between gap-4">
    <!-- Left: contact email -->
    <div class="flex items-center gap-5">
      <?php if ($_contact_email): ?>
      <a href="mailto:<?= Helpers::e($_contact_email) ?>" class="flex items-center gap-1.5 hover:text-primary-100 transition-colors">
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <span class="hidden sm:inline"><?= Helpers::e($_contact_email) ?></span>
      </a>
      <?php endif; ?>
    </div>
    <!-- Right: social icons -->
    <?php if ($_fb || $_ig || $_gb): ?>
    <div class="flex items-center gap-3">
      <span class="text-white/60 hidden sm:inline text-[11px] font-medium tracking-wide">Follow Us:</span>
      <?php if ($_fb): ?>
      <a href="<?= Helpers::e($_fb) ?>" target="_blank" rel="noopener" aria-label="Facebook" class="hover:text-primary-200 transition-colors">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
      </a>
      <?php endif; ?>
      <?php if ($_ig): ?>
      <a href="<?= Helpers::e($_ig) ?>" target="_blank" rel="noopener" aria-label="Instagram" class="hover:text-primary-200 transition-colors">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
      </a>
      <?php endif; ?>
      <?php if ($_gb): ?>
      <a href="<?= Helpers::e($_gb) ?>" target="_blank" rel="noopener" aria-label="Google Business" class="hover:text-primary-200 transition-colors">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- Nav -->
<nav class="sticky top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-20">
      <?php
        $__logo       = Settings::get('website_logo', '') ?: Settings::get('company_logo', '');
        $__mobileLogo = Settings::get('website_logo_mobile', '') ?: $__logo;
        $__altText    = Helpers::e(Settings::get('company_name', APP_NAME));
      ?>
      <!-- Logo -->
      <a href="/" class="flex items-center gap-2.5 font-bold text-xl min-w-0 flex-shrink-0">
        <?php if ($__logo || $__mobileLogo): ?>
          <?php if ($__mobileLogo && $__mobileLogo !== $__logo): ?>
            <img src="<?= Helpers::e($__mobileLogo) ?>" alt="<?= $__altText ?>" class="block md:hidden max-h-11 max-w-[150px] object-contain">
            <img src="<?= Helpers::e($__logo) ?>"       alt="<?= $__altText ?>" class="hidden md:block max-h-11 max-w-[200px] object-contain">
          <?php else: ?>
            <img src="<?= Helpers::e($__logo) ?>" alt="<?= $__altText ?>" class="max-h-11 max-w-[200px] object-contain">
          <?php endif; ?>
        <?php else: ?>
          <div class="w-9 h-9 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <span class="text-gray-900"><?= Helpers::e(Settings::get('company_name', APP_NAME)) ?></span>
        <?php endif; ?>
      </a>

      <!-- Desktop nav links -->
      <div class="hidden lg:flex items-center gap-7 text-sm font-medium text-gray-600">
        <a href="/about"      class="hover:text-primary-600 transition-colors py-1 border-b-2 border-transparent hover:border-primary-500">About</a>
        <a href="/features"   class="hover:text-primary-600 transition-colors py-1 border-b-2 border-transparent hover:border-primary-500">Features</a>
        <a href="/pricing"    class="hover:text-primary-600 transition-colors py-1 border-b-2 border-transparent hover:border-primary-500">Pricing</a>
        <a href="/industries" class="hover:text-primary-600 transition-colors py-1 border-b-2 border-transparent hover:border-primary-500">Industries</a>
        <a href="/faq"        class="hover:text-primary-600 transition-colors py-1 border-b-2 border-transparent hover:border-primary-500">FAQ</a>
        <a href="/blog"       class="hover:text-primary-600 transition-colors py-1 border-b-2 border-transparent hover:border-primary-500">News &amp; Updates</a>
      </div>

      <!-- Desktop CTA buttons + mobile hamburger -->
      <div class="flex items-center gap-3">
        <a href="/login" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors">Sign In</a>
        <a href="/register" class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors shadow-sm hidden sm:block">Start Free Today!</a>
        <!-- Mobile hamburger -->
        <button id="mobile-nav-toggle" aria-label="Toggle menu" class="lg:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 rounded-lg hover:bg-gray-50 transition-colors">
          <span class="w-5 h-0.5 bg-gray-700 rounded transition-all" id="ham-line-1"></span>
          <span class="w-5 h-0.5 bg-gray-700 rounded transition-all" id="ham-line-2"></span>
          <span class="w-5 h-0.5 bg-gray-700 rounded transition-all" id="ham-line-3"></span>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu">
      <div class="pb-5 pt-1 border-t border-gray-100 space-y-1">
        <a href="/about"      class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">About</a>
        <a href="/features"   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">Features</a>
        <a href="/pricing"    class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">Pricing</a>
        <a href="/industries" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">Industries</a>
        <a href="/faq"        class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">FAQ</a>
        <a href="/blog"       class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">News &amp; Updates</a>
        <div class="pt-3 grid grid-cols-2 gap-2">
          <a href="/login"    class="text-center border border-gray-200 text-gray-700 font-semibold text-sm py-2.5 rounded-lg hover:border-primary-300 hover:text-primary-600 transition-colors">Sign In</a>
          <a href="/register" class="text-center bg-primary-500 hover:bg-primary-600 text-white font-semibold text-sm py-2.5 rounded-lg transition-colors">Start Free Today!</a>
        </div>
      </div>
    </div>
  </div>
</nav>

<main>
  <?php if ($msg = Session::getFlash('success')): ?>
    <div class="fixed top-24 right-4 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-medium max-w-sm" id="flash-msg">
      <?= Helpers::e($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($msg = Session::getFlash('error')): ?>
    <div class="fixed top-24 right-4 z-50 bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-medium max-w-sm" id="flash-msg">
      <?= Helpers::e($msg) ?>
    </div>
  <?php endif; ?>

  <?php require VIEWS_PATH . '/' . $content_view . '.php'; ?>
</main>

<!-- Footer (CMS-driven) -->
<footer class="bg-gray-900 text-gray-400 pt-16 pb-8">
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
          3 => ['title' => 'Account',  'links' => "Sign In|/login\nStart Free Today!|/register"],
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

    <!-- Locations row -->
    <div class="border-t border-gray-800 pt-8 pb-6">
      <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-4">Locations We Serve</p>
      <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs text-gray-500">
        <a href="/digital-signage/sydney"       class="hover:text-gray-300 transition-colors">Digital Signage Sydney</a>
        <a href="/digital-signage/melbourne"    class="hover:text-gray-300 transition-colors">Digital Signage Melbourne</a>
        <a href="/digital-signage/brisbane"     class="hover:text-gray-300 transition-colors">Digital Signage Brisbane</a>
        <a href="/digital-signage/perth"        class="hover:text-gray-300 transition-colors">Digital Signage Perth</a>
        <a href="/digital-signage/adelaide"     class="hover:text-gray-300 transition-colors">Digital Signage Adelaide</a>
        <a href="/digital-signage/gold-coast"   class="hover:text-gray-300 transition-colors">Digital Signage Gold Coast</a>
        <a href="/digital-signage/auckland"     class="hover:text-gray-300 transition-colors">Digital Signage Auckland</a>
        <a href="/digital-signage/wellington"   class="hover:text-gray-300 transition-colors">Digital Signage Wellington</a>
        <a href="/digital-signage/london"       class="hover:text-gray-300 transition-colors">Digital Signage London</a>
        <a href="/digital-signage/manchester"   class="hover:text-gray-300 transition-colors">Digital Signage Manchester</a>
        <a href="/digital-signage/new-york"     class="hover:text-gray-300 transition-colors">Digital Signage New York</a>
        <a href="/digital-signage/los-angeles"  class="hover:text-gray-300 transition-colors">Digital Signage Los Angeles</a>
        <a href="/digital-signage/chicago"      class="hover:text-gray-300 transition-colors">Digital Signage Chicago</a>
      </div>
    </div>

    <!-- Policy links + social icons row -->
    <div class="border-t border-gray-800 pt-6 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex flex-wrap justify-center sm:justify-start gap-x-5 gap-y-1">
        <a href="/privacy-policy" class="text-xs text-gray-500 hover:text-gray-300 transition-colors">Privacy Policy</a>
        <a href="/terms"          class="text-xs text-gray-500 hover:text-gray-300 transition-colors">Terms &amp; Conditions</a>
        <a href="/refund-policy"  class="text-xs text-gray-500 hover:text-gray-300 transition-colors">Refund Policy</a>
      </div>
      <?php if ($_fb || $_ig || $_gb): ?>
      <div class="flex items-center gap-3">
        <?php if ($_fb): ?>
        <a href="<?= Helpers::e($_fb) ?>" target="_blank" rel="noopener" aria-label="Facebook"
           class="w-8 h-8 rounded-full bg-gray-800 hover:bg-[#1877F2] flex items-center justify-center transition-colors">
          <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
        </a>
        <?php endif; ?>
        <?php if ($_ig): ?>
        <a href="<?= Helpers::e($_ig) ?>" target="_blank" rel="noopener" aria-label="Instagram"
           class="w-8 h-8 rounded-full bg-gray-800 hover:bg-pink-600 flex items-center justify-center transition-colors">
          <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        <?php endif; ?>
        <?php if ($_gb): ?>
        <a href="<?= Helpers::e($_gb) ?>" target="_blank" rel="noopener" aria-label="Google Business"
           class="w-8 h-8 rounded-full bg-gray-800 hover:bg-white flex items-center justify-center transition-colors">
          <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
        </a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-5">
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
  // Flash message auto-dismiss
  setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000);

  // Mobile menu toggle
  (function() {
    var btn   = document.getElementById('mobile-nav-toggle');
    var menu  = document.getElementById('mobile-menu');
    var line1 = document.getElementById('ham-line-1');
    var line3 = document.getElementById('ham-line-3');
    if (!btn || !menu) return;
    btn.addEventListener('click', function() {
      var open = menu.classList.toggle('open');
      line1.style.transform = open ? 'translateY(8px) rotate(45deg)' : '';
      line3.style.transform = open ? 'translateY(-8px) rotate(-45deg)' : '';
      document.getElementById('ham-line-2').style.opacity = open ? '0' : '1';
    });
  })();

  // Reveal page sections as they scroll into view (skip the first — usually the hero, shown immediately)
  (function() {
    var sections = Array.prototype.slice.call(document.querySelectorAll('main > section')).slice(1);
    if (!sections.length) return;

    if (!('IntersectionObserver' in window)) {
      sections.forEach(function(el) { el.classList.add('reveal', 'is-visible'); });
      return;
    }

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    sections.forEach(function(el) {
      el.classList.add('reveal');
      observer.observe(el);
    });
  })();

  // Fade lazy-loaded images in once they've actually finished loading
  (function() {
    var imgs = document.querySelectorAll('img[loading="lazy"]');
    imgs.forEach(function(img) {
      if (img.complete && img.naturalWidth > 0) {
        img.classList.add('img-loaded');
      } else {
        img.addEventListener('load', function() { img.classList.add('img-loaded'); });
        img.addEventListener('error', function() { img.classList.add('img-loaded'); });
      }
    });
  })();
</script>
</body>
</html>
