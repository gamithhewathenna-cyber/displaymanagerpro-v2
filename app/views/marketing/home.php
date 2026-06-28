<?php
$c = function($key, $default='') { return htmlspecialchars(ContentController::get('home', $key, $default)); };

// Build hero slides — each slide carries its own display settings
$_hs = [];
for ($_si = 1; $_si <= 6; $_si++) {
    $_sh     = ContentController::get('home', "slide_{$_si}_heading", '');
    $_ss     = ContentController::get('home', "slide_{$_si}_sub", '');
    $_si_img = Settings::get("content_home_slide_{$_si}", '');
    if ($_sh || $_si_img) {
        $_hs[] = [
            'heading'    => $_sh,
            'sub'        => $_ss,
            'img'        => $_si_img,
            'img_mobile' => Settings::get("content_home_slide_{$_si}_mobile", ''),
            'layout'     => ContentController::get('home', "slide_{$_si}_layout",     'left'),
            'bg_color'   => ContentController::get('home', "slide_{$_si}_bg_color",   '#1e1b4b'),
            'text_color' => ContentController::get('home', "slide_{$_si}_text_color", 'light'),
            'overlay'    => ContentController::get('home', "slide_{$_si}_overlay",    '0'),
        ];
    }
}
// Fallback: 3 built-in slides (used when nothing is configured yet)
if (empty($_hs)) {
    $_hs = [
        ['heading' => null,  'sub' => null, 'img' => '', 'img_mobile' => '', 'layout' => 'left', 'bg_color' => '#1e1b4b', 'text_color' => 'light', 'overlay' => '0'],
        ['heading' => 'Manage All Your Channels',   'sub' => 'Create channels, upload images, and every screen updates automatically — no tech skills needed.', 'img' => '', 'img_mobile' => '', 'layout' => 'left', 'bg_color' => '#0a2540', 'text_color' => 'light', 'overlay' => '0'],
        ['heading' => 'Works on Any TV or Device',  'sub' => 'Smart TVs, Fire Stick, Android TV boxes, Chrome browsers — one URL powers them all.',             'img' => '', 'img_mobile' => '', 'layout' => 'left', 'bg_color' => '#052e16', 'text_color' => 'light', 'overlay' => '0'],
    ];
}
$_hsCount = count($_hs);

// Static banner image
$_staticBanner = Settings::get('content_home_banner', '');
?>

<!-- HERO SLIDER — Full-width background image with text overlay -->
<section class="overflow-hidden relative">

  <div id="hs-track" class="relative" style="display:grid;">
    <?php foreach ($_hs as $_hi => $_hsl):
      // Sanitise bg color
      $_bgRaw    = $_hsl['bg_color'] ?? '#1e1b4b';
      $_bgSafe   = preg_match('/^#[0-9a-fA-F]{6}$/', $_bgRaw) ? $_bgRaw : '#1e1b4b';
      $_img      = $_hsl['img'] ?? '';
      $_imgMob   = $_hsl['img_mobile'] ?? '';
      $_layout   = $_hsl['layout'] ?? 'left';
      $_isLight = ($_hsl['text_color'] ?? 'light') === 'light';

      // Text colour classes
      $_tcHead = $_isLight ? 'text-white'     : 'text-gray-900';
      $_tcSub  = $_isLight ? 'text-white/80'  : 'text-gray-600';
      $_tcDesc = $_isLight ? 'text-white/70'  : 'text-gray-500';
      $_badgeBg = $_isLight
        ? 'bg-white/15 border-white/30 text-white'
        : 'bg-primary-50 border-primary-100 text-primary-700';
      $_ctaSec = $_isLight
        ? 'bg-white/15 hover:bg-white/25 border-white/40 text-white'
        : 'bg-transparent border-gray-300 hover:border-primary-400 text-gray-700 hover:text-primary-600';

      // Content alignment classes — mobile always centres at top; desktop follows layout setting
      if ($_layout === 'center') {
          $_wrap   = 'w-full flex flex-col items-center text-center';
          $_prose  = 'max-w-[617px]';
          $_ctaRow = 'flex flex-col sm:flex-row gap-3 justify-center';
      } elseif ($_layout === 'right') {
          $_wrap   = 'w-full flex flex-col items-center text-center sm:ml-auto sm:text-right sm:items-end';
          $_prose  = 'max-w-[521px] lg:max-w-[617px]';
          $_ctaRow = 'flex flex-col sm:flex-row gap-3 justify-center sm:justify-end';
      } else {
          $_wrap   = 'w-full flex flex-col items-center text-center sm:text-left sm:items-start';
          $_prose  = 'max-w-[521px] lg:max-w-[617px]';
          $_ctaRow = 'flex flex-col sm:flex-row gap-3 justify-center sm:justify-start';
      }

      // Overlay: uniform dark tint, opacity controlled per slide (0 = none)
      $_overlayPct = min(80, max(0, (int)($_hsl['overlay'] ?? 0)));
      $_overlayStyle = $_overlayPct > 0
          ? 'background:rgba(0,0,0,' . number_format($_overlayPct / 100, 2) . ')'
          : '';
    ?>
    <?php
      // Per-slide style: swap background image at the sm breakpoint (640px)
      $_desktopBg = $_img    ? 'url(\'' . Helpers::e($_img)    . '\')' : 'none';
      $_mobileBg  = $_imgMob ? 'url(\'' . Helpers::e($_imgMob) . '\')' : $_desktopBg;
    ?>
    <style>
      #hs-bg-<?= $_hi ?> { background-color:<?= $_bgSafe ?>;<?= ($_img || $_imgMob) ? 'background-size:cover;background-position:center;' : '' ?>background-image:<?= $_desktopBg ?>; }
      @media (max-width:639px) { #hs-bg-<?= $_hi ?> { background-image:<?= $_mobileBg ?>; } }
    </style>
    <div class="hs-slide transition-opacity duration-700 <?= $_hi === 0 ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none' ?>" style="grid-area:1/1;">

      <!-- Full-width background panel -->
      <div id="hs-bg-<?= $_hi ?>" class="relative w-full min-h-[320px] sm:min-h-[430px] lg:min-h-[520px]">

        <!-- Overlay: uniform opacity tint (admin-controlled, 0–80%) -->
        <?php if ($_overlayStyle): ?>
        <div class="absolute inset-0 pointer-events-none" style="<?= $_overlayStyle ?>"></div>
        <?php elseif (!$_img && !$_imgMob): ?>
        <div class="absolute inset-0 pointer-events-none opacity-[0.07]" style="background-image:radial-gradient(circle at 20% 50%,#ffffff 0%,transparent 55%),radial-gradient(circle at 80% 20%,#a78bfa 0%,transparent 45%)"></div>
        <?php endif; ?>

        <!-- Slide content -->
        <div class="relative z-10 w-full h-full min-h-[320px] sm:min-h-[430px] lg:min-h-[520px]">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-start sm:items-center min-h-[320px] sm:min-h-[430px] lg:min-h-[520px]">
            <div class="<?= $_wrap ?> py-16 sm:py-20 lg:py-24 w-full">
              <div class="<?= $_prose ?> max-h-[236px] overflow-hidden">

                <!-- Badge (first slide only, or all slides with heading) -->
                <?php if ($_hsl['heading'] === null || $_hi === 0): ?>
                <div class="inline-flex items-center gap-2 <?= $_badgeBg ?> border rounded-full px-4 py-1.5 text-xs sm:text-sm font-medium mb-6">
                  <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse flex-shrink-0"></span>
                  <?= $c('badge_text', '14-day free trial · No credit card required') ?>
                </div>
                <?php endif; ?>

                <!-- Heading -->
                <?php if ($_hsl['heading'] === null): ?>
                  <h1 class="text-3xl sm:text-4xl md:text-5xl xl:text-[3.25rem] font-extrabold leading-tight mb-5 <?= $_tcHead ?>">
                    <?= $c('hero_title_1', 'Update Every Restaurant Screen') ?><br>
                    <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"><?= $c('hero_title_2', 'In Seconds') ?></span>
                  </h1>
                  <p class="text-base sm:text-lg font-medium mb-2 <?= $_tcSub ?>"><?= $c('hero_subtitle', 'No USB Drives. No Complicated Software.') ?></p>
                  <p class="text-sm sm:text-base mb-8 leading-relaxed <?= $_tcDesc ?>"><?= $c('hero_description', 'Manage menus, specials, promotions and announcements across all your TV screens from one simple cloud dashboard.') ?></p>
                <?php elseif ($_hsl['heading'] !== ''): ?>
                  <h1 class="text-3xl sm:text-4xl md:text-5xl xl:text-[3.25rem] font-extrabold leading-tight mb-5 <?= $_tcHead ?>">
                    <?= Helpers::e($_hsl['heading']) ?>
                  </h1>
                  <?php if ($_hsl['sub']): ?>
                  <p class="text-base sm:text-lg mb-8 leading-relaxed <?= $_tcSub ?>"><?= Helpers::e($_hsl['sub']) ?></p>
                  <?php endif; ?>
                <?php endif; ?>

                <!-- CTAs -->
                <div class="<?= $_ctaRow ?>">
                  <a href="/register" class="bg-primary-500 hover:bg-primary-600 text-white font-bold text-sm sm:text-base px-7 py-3.5 rounded-xl transition-all shadow-lg shadow-primary-500/30 hover:-translate-y-0.5 text-center">
                    <?= $c('cta_primary', 'Start Free 14-Day Trial →') ?>
                  </a>
                  <a href="/pricing" class="<?= $_ctaSec ?> border-2 font-semibold text-sm sm:text-base px-7 py-3.5 rounded-xl transition-all text-center">
                    <?= $c('cta_secondary', 'View Pricing') ?>
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <?php endforeach; ?>
  </div>

  <!-- Navigation arrows -->
  <?php if ($_hsCount > 1): ?>
  <button type="button" id="hs-prev" aria-label="Previous slide"
    class="absolute left-3 sm:left-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/20 hover:bg-white/35 border border-white/40 flex items-center justify-center transition-colors backdrop-blur-sm">
    <svg class="w-5 h-5 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
  </button>
  <button type="button" id="hs-next" aria-label="Next slide"
    class="absolute right-3 sm:right-5 top-1/2 -translate-y-1/2 z-20 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/20 hover:bg-white/35 border border-white/40 flex items-center justify-center transition-colors backdrop-blur-sm">
    <svg class="w-5 h-5 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
  </button>
  <!-- Dots -->
  <div class="absolute bottom-6 inset-x-0 flex items-center justify-center z-20" role="tablist" aria-label="Slide navigation">
    <div id="hs-dots" class="flex items-center gap-2">
      <?php for ($_di = 0; $_di < $_hsCount; $_di++): ?>
      <button type="button" role="tab" aria-label="Go to slide <?= $_di + 1 ?>"<?= $_di === 0 ? ' aria-selected="true"' : ' aria-selected="false"' ?>
        class="h-2 rounded-full transition-all duration-300 <?= $_di === 0 ? 'w-8 bg-white' : 'w-2 bg-white/40' ?>"></button>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>

</section>
<script>
(function() {
  var slides  = document.querySelectorAll('#hs-track .hs-slide');
  var dots    = document.querySelectorAll('#hs-dots button');
  var prev    = document.getElementById('hs-prev');
  var next    = document.getElementById('hs-next');
  var current = 0;
  var timer;
  if (slides.length < 2) return;

  function goTo(n) {
    slides[current].classList.remove('opacity-100','z-10','pointer-events-auto');
    slides[current].classList.add('opacity-0','z-0','pointer-events-none');
    dots[current].classList.remove('bg-white','w-8');
    dots[current].classList.add('bg-white/40','w-2');
    dots[current].setAttribute('aria-selected','false');
    current = (n + slides.length) % slides.length;
    slides[current].classList.remove('opacity-0','z-0','pointer-events-none');
    slides[current].classList.add('opacity-100','z-10','pointer-events-auto');
    dots[current].classList.remove('bg-white/40','w-2');
    dots[current].classList.add('bg-white','w-8');
    dots[current].setAttribute('aria-selected','true');
  }

  function startTimer() { timer = setInterval(function(){ goTo(current+1); }, 5000); }
  function resetTimer() { clearInterval(timer); startTimer(); }

  if (prev) prev.addEventListener('click', function(){ goTo(current-1); resetTimer(); });
  if (next) next.addEventListener('click', function(){ goTo(current+1); resetTimer(); });
  dots.forEach(function(dot,i){ dot.addEventListener('click', function(){ goTo(i); resetTimer(); }); });
  startTimer();
})();
</script>

<!-- TRUST / STATS BAR -->
<section class="bg-white border-y border-gray-100 py-10 px-4">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <?php $num = $c("stat_{$i}_num"); $label = $c("stat_{$i}_label"); if (!$num && !$label) continue; ?>
      <div class="text-center group">
        <div class="text-3xl sm:text-4xl font-extrabold text-primary-600 mb-1 tabular-nums"><?= $num ?></div>
        <div class="text-sm text-gray-500 font-medium"><?= $label ?></div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- STATIC BANNER IMAGE -->
<?php if ($_staticBanner): ?>
<section class="overflow-hidden">
  <img src="<?= Helpers::e($_staticBanner) ?>" alt="" class="w-full h-auto block">
</section>
<?php endif; ?>

<!-- FEATURES / SERVICES -->
<section class="py-24 px-4 bg-white">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-14">
      <p class="text-xs font-bold tracking-widest uppercase text-primary-600 mb-3">Our Features</p>
      <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4"><?= $c('features_title', 'Everything you need to manage your screens') ?></h2>
      <p class="text-lg text-gray-500 max-w-2xl mx-auto"><?= $c('features_subtitle') ?></p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      <!-- Card 1: Instant Updates -->
      <div class="bg-white border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-lg transition-all group">
        <div class="w-13 h-13 bg-primary-50 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors" style="width:52px;height:52px;">
          <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-2">Instant Updates</h3>
        <p class="text-gray-500 text-sm leading-relaxed mb-5">Change your menus, specials, or promotions from anywhere — updates appear on screens immediately.</p>
        <a href="/features" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 group-hover:gap-2.5 transition-all">
          Learn more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Card 2: Cloud Dashboard (highlighted) -->
      <div class="bg-primary-500 border border-primary-500 rounded-2xl p-7 shadow-lg shadow-primary-500/20 group">
        <div class="w-13 h-13 bg-white/20 rounded-xl flex items-center justify-center mb-5" style="width:52px;height:52px;">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-white mb-2">Cloud Dashboard</h3>
        <p class="text-primary-100 text-sm leading-relaxed mb-5">Manage all your TV screens from one clean, intuitive dashboard. No software to install.</p>
        <a href="/features" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 hover:text-white group-hover:gap-2.5 transition-all">
          Learn more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Card 3: Auto Refresh -->
      <div class="bg-white border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-lg transition-all group">
        <div class="w-13 h-13 bg-primary-50 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors" style="width:52px;height:52px;">
          <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-2">Auto Refresh</h3>
        <p class="text-gray-500 text-sm leading-relaxed mb-5">Screens automatically reload content on your schedule — every 5, 15, or 60 minutes.</p>
        <a href="/features" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 group-hover:gap-2.5 transition-all">
          Learn more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Card 4: Drag & Drop -->
      <div class="bg-white border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-lg transition-all group">
        <div class="w-13 h-13 bg-primary-50 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors" style="width:52px;height:52px;">
          <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-2">Drag &amp; Drop</h3>
        <p class="text-gray-500 text-sm leading-relaxed mb-5">Upload images, drag to reorder your slides, and your screens update — no tech skills needed.</p>
        <a href="/features" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 group-hover:gap-2.5 transition-all">
          Learn more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Card 5: Secure URLs -->
      <div class="bg-white border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-lg transition-all group">
        <div class="w-13 h-13 bg-primary-50 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors" style="width:52px;height:52px;">
          <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-2">Secure URLs</h3>
        <p class="text-gray-500 text-sm leading-relaxed mb-5">Every channel gets a unique, secure display URL with QR code. Simply open it on any screen.</p>
        <a href="/features" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 group-hover:gap-2.5 transition-all">
          Learn more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Card 6: Works Everywhere -->
      <div class="bg-white border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-lg transition-all group">
        <div class="w-13 h-13 bg-primary-50 rounded-xl flex items-center justify-center mb-5 group-hover:bg-primary-100 transition-colors" style="width:52px;height:52px;">
          <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 18h.01M8 21h8a2 2 0 002-2v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2a2 2 0 002 2zM4 7h16a1 1 0 011 1v7a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-2">Works Everywhere</h3>
        <p class="text-gray-500 text-sm leading-relaxed mb-5">Smart TVs, Fire Stick, Android TV Box, Chrome browser — one channel URL works on all devices.</p>
        <a href="/features" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 group-hover:gap-2.5 transition-all">
          Learn more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="bg-gray-50 py-24 px-4">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-16">
      <p class="text-xs font-bold tracking-widest uppercase text-primary-600 mb-3">Simple Setup</p>
      <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4"><?= $c('hiw_title', 'Up and running in 10 minutes') ?></h2>
    </div>
    <div class="relative">
      <div class="hidden md:block absolute top-8 left-0 right-0 h-px bg-primary-100 mx-32"></div>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="text-center relative">
          <div class="w-16 h-16 bg-primary-500 rounded-2xl flex items-center justify-center text-white text-xl font-bold mx-auto mb-5 relative z-10 shadow-lg shadow-primary-500/30"><?= $i ?></div>
          <h3 class="font-semibold text-gray-900 mb-2 text-sm"><?= $c("step_{$i}_title") ?></h3>
          <p class="text-sm text-gray-500 leading-relaxed"><?= $c("step_{$i}_desc") ?></p>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="py-24 px-4 bg-white">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-10">
      <p class="text-xs font-bold tracking-widest uppercase text-primary-600 mb-3">Pricing Plans</p>
      <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Simple, honest pricing</h2>
      <p class="text-gray-500">No hidden fees. Cancel anytime.</p>
    </div>

    <!-- Billing toggle -->
    <div class="flex items-center justify-center gap-4 mb-12">
      <span class="text-sm font-medium text-gray-700" id="lbl-monthly">Monthly</span>
      <button id="billing-toggle" onclick="switchBilling()"
        class="relative inline-flex h-7 w-14 items-center rounded-full bg-gray-200 transition-colors focus:outline-none"
        aria-label="Toggle billing period">
        <span id="toggle-dot" class="inline-block h-5 w-5 translate-x-1 transform rounded-full bg-white shadow transition-transform"></span>
      </button>
      <span class="text-sm font-medium text-gray-700" id="lbl-annual">
        Annual <span class="ml-1 bg-green-100 text-green-700 text-xs font-semibold px-1.5 py-0.5 rounded-full">Save ~17%</span>
      </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($plans as $i => $plan): ?>
      <?php $hasTrial = (bool)($plan['has_trial'] ?? 1); ?>
      <?php $annualMonthly = $plan['price_annual'] > 0 ? $plan['price_annual'] / 12 : 0; ?>
      <div class="bg-white border-2 <?= $i===1?'border-primary-500 shadow-xl shadow-primary-100':'border-gray-100' ?> rounded-2xl p-8 relative">
        <?php if ($i===1): ?><div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary-500 text-white text-xs font-bold px-4 py-1.5 rounded-full">MOST POPULAR</div><?php endif; ?>
        <div class="text-sm font-semibold text-gray-500 mb-2"><?= Helpers::e($plan['name']) ?></div>

        <!-- Monthly price (shown by default) -->
        <div class="price-monthly flex items-baseline gap-1 mb-1">
          <span class="text-4xl font-extrabold text-gray-900">$<?= number_format($plan['price_monthly'], 2) ?></span>
          <span class="text-gray-400 text-sm">USD/mo</span>
        </div>
        <!-- Annual price (hidden by default) -->
        <div class="price-annual hidden flex items-baseline gap-1 mb-1">
          <span class="text-4xl font-extrabold text-gray-900">$<?= number_format($annualMonthly, 2) ?></span>
          <span class="text-gray-400 text-sm">USD/mo</span>
        </div>
        <div class="price-annual-note hidden text-xs text-green-600 font-medium mb-1">
          $<?= number_format($plan['price_annual'], 2) ?> billed annually
        </div>

        <div class="text-sm text-gray-400 mb-6">Up to <?= $plan['max_screens'] ?> TV screen<?= $plan['max_screens']>1?'s':'' ?></div>
        <?php
          $feats = json_decode($plan['features'] ?? '[]', true);
          if (!$hasTrial) {
              $feats = array_values(array_filter($feats, fn($f) => !preg_match('/14.day|free trial/i', $f)));
          }
        ?>
        <ul class="space-y-3 mb-8">
          <?php if ($hasTrial): ?>
          <li class="flex items-center gap-2.5 text-sm text-green-700 font-medium">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            14-day free trial
          </li>
          <?php endif; ?>
          <?php foreach ($feats as $feat): ?>
          <li class="flex items-center gap-2.5 text-sm text-gray-600">
            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= Helpers::e($feat) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="/register?plan=<?= Helpers::e($plan['slug']) ?>"
          class="block text-center <?= $i===1?'bg-primary-500 hover:bg-primary-600 text-white shadow-md shadow-primary-500/25':'bg-gray-50 hover:bg-gray-100 text-gray-900' ?> font-semibold py-3 px-6 rounded-xl transition-colors">
          <?= $hasTrial ? 'Start Free Trial' : 'Get Started' ?>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <p class="text-center text-xs text-gray-400 mt-8">All prices in USD. <a href="/contact" class="text-primary-600 hover:text-primary-700">Need custom pricing?</a></p>
  </div>
</section>

<script>
(function() {
  var isAnnual = false;
  window.switchBilling = function() {
    isAnnual = !isAnnual;
    var dot = document.getElementById('toggle-dot');
    var btn = document.getElementById('billing-toggle');
    dot.style.transform = isAnnual ? 'translateX(1.75rem)' : 'translateX(0.25rem)';
    btn.style.backgroundColor = isAnnual ? '#6366f1' : '';
    document.querySelectorAll('.price-monthly').forEach(function(el){ el.classList.toggle('hidden', isAnnual); });
    document.querySelectorAll('.price-annual').forEach(function(el){ el.classList.toggle('hidden', !isAnnual); });
    document.querySelectorAll('.price-annual-note').forEach(function(el){ el.classList.toggle('hidden', !isAnnual); });
  };
})();
</script>

<!-- INTERNAL LINK STRIP -->
<section class="bg-gray-50 border-y border-gray-100 py-8 px-4">
  <div class="max-w-5xl mx-auto">
    <p class="text-center text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">Explore DisplayManagerPro</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
      <a href="/features" class="flex flex-col items-center gap-2 bg-white hover:bg-primary-50 border border-gray-100 hover:border-primary-200 rounded-xl p-4 text-center transition-all group shadow-sm hover:shadow-md">
        <span class="text-2xl">⚡</span>
        <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">All Features</span>
      </a>
      <a href="/pricing" class="flex flex-col items-center gap-2 bg-white hover:bg-primary-50 border border-gray-100 hover:border-primary-200 rounded-xl p-4 text-center transition-all group shadow-sm hover:shadow-md">
        <span class="text-2xl">💳</span>
        <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">Pricing Plans</span>
      </a>
      <a href="/industries" class="flex flex-col items-center gap-2 bg-white hover:bg-primary-50 border border-gray-100 hover:border-primary-200 rounded-xl p-4 text-center transition-all group shadow-sm hover:shadow-md">
        <span class="text-2xl">🏢</span>
        <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">Industries We Serve</span>
      </a>
      <a href="/about" class="flex flex-col items-center gap-2 bg-white hover:bg-primary-50 border border-gray-100 hover:border-primary-200 rounded-xl p-4 text-center transition-all group shadow-sm hover:shadow-md">
        <span class="text-2xl">👥</span>
        <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">About Us</span>
      </a>
      <a href="/faq" class="flex flex-col items-center gap-2 bg-white hover:bg-primary-50 border border-gray-100 hover:border-primary-200 rounded-xl p-4 text-center transition-all group shadow-sm hover:shadow-md">
        <span class="text-2xl">❓</span>
        <span class="text-xs font-semibold text-gray-700 group-hover:text-primary-600 transition-colors">FAQ</span>
      </a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="bg-white py-24 px-4">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-14">
      <p class="text-xs font-bold tracking-widest uppercase text-primary-600 mb-3">Customer Reviews</p>
      <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Loved by hospitality businesses</h2>
      <p class="text-gray-500 max-w-2xl mx-auto">Real results from real businesses using DisplayManagerPro every day.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php for ($i = 1; $i <= 6; $i++): ?>
      <?php if ($c("testimonial_{$i}_quote")): ?>
      <div class="bg-gray-50 border border-gray-100 rounded-2xl p-7 flex flex-col hover:shadow-md hover:border-primary-100 transition-all">
        <div class="text-yellow-400 text-base mb-4 tracking-wide">★★★★★</div>
        <p class="text-gray-600 text-sm leading-relaxed mb-6 flex-1"><?= $c("testimonial_{$i}_quote") ?></p>
        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
          <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
            <span class="text-primary-600 font-bold text-sm"><?= mb_substr($c("testimonial_{$i}_name"), 0, 1) ?></span>
          </div>
          <div>
            <div class="font-semibold text-gray-900 text-sm"><?= $c("testimonial_{$i}_name") ?></div>
            <div class="text-gray-400 text-xs"><?= $c("testimonial_{$i}_role") ?></div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <?php endfor; ?>
    </div>
  </div>
</section>

<?php if (!empty($latestPosts)): ?>
<!-- LATEST BLOG POSTS -->
<section class="py-24 px-4 bg-gray-50">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-14">
      <p class="text-xs font-bold tracking-widest uppercase text-primary-600 mb-3">Latest News</p>
      <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">News &amp; Updates</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Tips, guides and product updates for digital signage.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <?php foreach ($latestPosts as $bp): ?>
      <a href="/blog/<?= Helpers::e($bp['slug']) ?>" class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-primary-200 hover:shadow-md transition-all flex flex-col">
        <?php if ($bp['featured_image']): ?>
        <div class="overflow-hidden bg-gray-100" style="aspect-ratio:16/9;">
          <img src="<?= Helpers::e($bp['featured_image']) ?>" alt="<?= Helpers::e($bp['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
        <?php else: ?>
        <div class="bg-gradient-to-br from-primary-50 to-indigo-100 flex items-center justify-center" style="aspect-ratio:16/9;">
          <svg class="w-10 h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        </div>
        <?php endif; ?>
        <div class="p-6 flex flex-col flex-1">
          <?php if ($bp['published_at']): ?>
          <p class="text-xs text-gray-400 mb-2"><?= date('d M Y', strtotime($bp['published_at'])) ?></p>
          <?php endif; ?>
          <h3 class="text-base font-semibold text-gray-900 group-hover:text-primary-600 transition-colors mb-3 leading-snug"><?= Helpers::e($bp['title']) ?></h3>
          <?php if ($bp['excerpt']): ?>
          <p class="text-sm text-gray-500 leading-relaxed flex-1"><?= Helpers::e(mb_strimwidth($bp['excerpt'], 0, 110, '…')) ?></p>
          <?php endif; ?>
          <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-primary-600 group-hover:gap-2 transition-all">Read more <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-10">
      <a href="/blog" class="inline-flex items-center gap-2 border border-gray-200 hover:border-primary-300 text-gray-700 hover:text-primary-600 font-semibold text-sm px-6 py-2.5 rounded-xl transition-all bg-white">
        View all posts
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="hero-gradient text-white py-24 px-4 relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image:radial-gradient(circle at 20% 50%,#6366f1 0%,transparent 50%),radial-gradient(circle at 80% 20%,#8b5cf6 0%,transparent 40%)"></div>
  <div class="max-w-3xl mx-auto text-center relative">
    <p class="text-xs font-bold tracking-widest uppercase text-primary-300 mb-4">Get Started Today</p>
    <h2 class="text-3xl sm:text-4xl font-bold mb-5"><?= $c('cta_title', 'Ready to modernise your screens?') ?></h2>
    <p class="text-gray-300 mb-10 text-lg"><?= $c('cta_subtitle') ?></p>
    <a href="/register" class="inline-block bg-white text-primary-600 font-bold text-base sm:text-lg px-10 py-4 rounded-xl hover:bg-primary-50 transition-all shadow-xl">
      <?= $c('cta_button', 'Get Started Free →') ?>
    </a>
    <p class="mt-6 text-sm text-gray-400">Have questions? <a href="/faq" class="text-white/80 hover:text-white underline underline-offset-2 transition-colors">Read our FAQ</a> or <a href="/contact" class="text-white/80 hover:text-white underline underline-offset-2 transition-colors">contact us</a>.</p>
  </div>
</section>
