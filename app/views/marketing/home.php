<?php
$c = function($key, $default='') { return htmlspecialchars(ContentController::get('home', $key, $default)); };
?>
<!-- HERO -->
<?php
  $_sliderImgs = [];
  for ($_si = 1; $_si <= 6; $_si++) {
      $_img = Settings::get("content_home_slide_{$_si}", '');
      if ($_img) $_sliderImgs[] = $_img;
  }
  $_slideCount = !empty($_sliderImgs) ? count($_sliderImgs) : 4;
?>
<section class="hero-gradient text-white py-16 md:py-24 px-4 overflow-hidden relative">
  <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 15% 50%, #6366f1 0%, transparent 50%), radial-gradient(circle at 85% 20%, #8b5cf6 0%, transparent 40%)"></div>
  <div class="max-w-6xl mx-auto relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 xl:gap-16 items-center">

      <!-- Left: Text -->
      <div class="order-2 lg:order-1">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-sm mb-7">
          <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
          <?= $c('badge_text', '14-day free trial · No credit card required') ?>
        </div>
        <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold leading-tight mb-5">
          <?= $c('hero_title_1', 'Update Every Restaurant Screen') ?><br>
          <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= $c('hero_title_2', 'In Seconds') ?></span>
        </h1>
        <p class="text-xl text-gray-300 mb-3 font-light"><?= $c('hero_subtitle', 'No USB Drives. No Complicated Software.') ?></p>
        <p class="text-base text-gray-400 mb-9 max-w-lg"><?= $c('hero_description', 'Manage menus, specials, promotions and announcements across all your TV screens from one simple cloud dashboard.') ?></p>
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="/register" class="bg-primary-500 hover:bg-primary-600 text-white font-bold text-lg px-9 py-4 rounded-xl transition-all shadow-lg shadow-primary-500/30 hover:-translate-y-0.5 text-center">
            <?= $c('cta_primary', 'Start Free 14-Day Trial →') ?>
          </a>
          <a href="/pricing" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-lg px-9 py-4 rounded-xl transition-all text-center">
            <?= $c('cta_secondary', 'View Pricing') ?>
          </a>
        </div>
      </div>

      <!-- Right: Image carousel -->
      <div class="order-1 lg:order-2 relative">
        <div class="absolute -inset-3 bg-gradient-to-r from-violet-600/25 to-blue-600/25 rounded-3xl blur-2xl pointer-events-none"></div>
        <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-white/10" style="background:#111827;">
          <!-- Browser chrome bar -->
          <div class="flex items-center gap-2 px-4 py-3 border-b border-white/10" style="background:rgba(31,41,55,0.9);">
            <div class="flex gap-1.5">
              <div class="w-3 h-3 rounded-full bg-red-400/80"></div>
              <div class="w-3 h-3 rounded-full bg-yellow-400/80"></div>
              <div class="w-3 h-3 rounded-full bg-green-400/80"></div>
            </div>
            <div class="flex-1 mx-3 bg-white/10 rounded-md px-3 py-1 text-xs text-gray-400 truncate">dashboard.signagecloud.com</div>
          </div>
          <!-- Slides container -->
          <div id="hero-slides" class="relative" style="aspect-ratio:16/10;">
            <?php if (!empty($_sliderImgs)): ?>
              <?php foreach ($_sliderImgs as $_idx => $_src): ?>
              <div class="slide absolute inset-0 transition-opacity duration-700 <?= $_idx === 0 ? 'opacity-100' : 'opacity-0' ?>">
                <img src="<?= Helpers::e($_src) ?>" alt="" class="w-full h-full object-cover">
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <!-- Placeholder: Dashboard -->
              <div class="slide absolute inset-0 transition-opacity duration-700 opacity-100 p-5" style="background:linear-gradient(135deg,#1e1b4b,#2e1065);">
                <div class="text-[10px] text-indigo-400/70 mb-3 font-bold tracking-widest uppercase">Dashboard Overview</div>
                <div class="grid grid-cols-3 gap-2 mb-3">
                  <div class="bg-white/10 rounded-xl p-3"><div class="text-[10px] text-gray-400 mb-1">Active Screens</div><div class="text-xl font-bold text-white">3</div><div class="text-[10px] text-green-400">● All live</div></div>
                  <div class="bg-white/10 rounded-xl p-3"><div class="text-[10px] text-gray-400 mb-1">Total Slides</div><div class="text-xl font-bold text-white">24</div><div class="text-[10px] text-gray-400">Across channels</div></div>
                  <div class="bg-white/10 rounded-xl p-3"><div class="text-[10px] text-gray-400 mb-1">Auto Refresh</div><div class="text-xl font-bold text-white">15m</div><div class="text-[10px] text-blue-400">Enabled</div></div>
                </div>
                <div class="bg-white/5 rounded-xl p-3">
                  <div class="flex items-center justify-between mb-2"><span class="text-xs text-gray-300 font-medium">My Channels</span><span class="text-[10px] text-indigo-400">View all →</span></div>
                  <div class="space-y-1.5">
                    <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-1.5"><div class="w-1.5 h-1.5 rounded-full bg-green-400"></div><span class="text-xs text-gray-300 flex-1">Main Menu Board</span><span class="text-[10px] text-gray-500">8 slides</span></div>
                    <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-1.5"><div class="w-1.5 h-1.5 rounded-full bg-green-400"></div><span class="text-xs text-gray-300 flex-1">Daily Specials</span><span class="text-[10px] text-gray-500">3 slides</span></div>
                    <div class="flex items-center gap-2 bg-white/5 rounded-lg px-3 py-1.5"><div class="w-1.5 h-1.5 rounded-full bg-yellow-400"></div><span class="text-xs text-gray-300 flex-1">Promotions</span><span class="text-[10px] text-gray-500">5 slides</span></div>
                  </div>
                </div>
              </div>
              <!-- Placeholder: Channel Editor -->
              <div class="slide absolute inset-0 transition-opacity duration-700 opacity-0 p-5" style="background:linear-gradient(135deg,#0c1a2e,#0a2540);">
                <div class="text-[10px] text-blue-400/70 mb-3 font-bold tracking-widest uppercase">Channel Editor</div>
                <div class="bg-white/5 rounded-xl p-4 mb-3">
                  <div class="flex items-center justify-between mb-3"><span class="text-sm font-semibold text-white">Main Menu Board</span><span class="text-[10px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">Live</span></div>
                  <div class="flex gap-2">
                    <div class="flex-1 bg-gradient-to-br from-indigo-600/50 to-purple-600/50 rounded-lg flex items-center justify-center text-[10px] text-gray-300 border border-white/10" style="aspect-ratio:16/9;">Slide 1</div>
                    <div class="flex-1 bg-gradient-to-br from-blue-600/50 to-cyan-600/50 rounded-lg flex items-center justify-center text-[10px] text-gray-300 border border-white/10" style="aspect-ratio:16/9;">Slide 2</div>
                    <div class="flex-1 bg-gradient-to-br from-emerald-600/50 to-teal-600/50 rounded-lg flex items-center justify-center text-[10px] text-gray-300 border border-white/10" style="aspect-ratio:16/9;">Slide 3</div>
                    <div class="flex-1 border-2 border-dashed border-white/20 rounded-lg flex items-center justify-center text-gray-500 text-xl" style="aspect-ratio:16/9;">+</div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <div class="bg-white/5 rounded-xl p-3"><div class="text-[10px] text-gray-400 mb-1">Slide Duration</div><div class="text-sm font-semibold text-white">8 seconds</div></div>
                  <div class="bg-white/5 rounded-xl p-3"><div class="text-[10px] text-gray-400 mb-1">Transition</div><div class="text-sm font-semibold text-white">Fade</div></div>
                </div>
              </div>
              <!-- Placeholder: Media Library -->
              <div class="slide absolute inset-0 transition-opacity duration-700 opacity-0 p-5" style="background:linear-gradient(135deg,#1a0a2e,#2d0a3e);">
                <div class="text-[10px] text-purple-400/70 mb-3 font-bold tracking-widest uppercase">Media Library</div>
                <div class="grid grid-cols-3 gap-2 mb-3">
                  <?php foreach (['from-indigo-600/40 to-purple-600/40','from-blue-600/40 to-cyan-600/40','from-emerald-600/40 to-teal-600/40','from-amber-600/40 to-orange-600/40','from-pink-600/40 to-rose-600/40','from-violet-600/40 to-indigo-600/40'] as $_pc): ?>
                  <div class="bg-gradient-to-br <?= $_pc ?> rounded-lg border border-white/10 flex items-center justify-center" style="aspect-ratio:16/9;">
                    <svg class="w-5 h-5 text-white/30" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                  </div>
                  <?php endforeach; ?>
                </div>
                <div class="flex items-center justify-between bg-white/5 rounded-xl px-4 py-2.5">
                  <span class="text-xs text-gray-400">6 images · 18.4 MB used</span>
                  <span class="text-[10px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded-full font-semibold">+ Upload</span>
                </div>
              </div>
              <!-- Placeholder: Live Display -->
              <div class="slide absolute inset-0 transition-opacity duration-700 opacity-0 p-5" style="background:linear-gradient(135deg,#052e16,#064e3b);">
                <div class="text-[10px] text-emerald-400/70 mb-3 font-bold tracking-widest uppercase">Live TV Display</div>
                <div class="bg-black/40 rounded-xl border border-white/10 overflow-hidden mb-3" style="aspect-ratio:16/9;">
                  <div class="w-full h-full bg-gradient-to-br from-indigo-800/60 to-violet-800/60 flex flex-col items-center justify-center gap-2">
                    <div class="text-white/20 text-4xl">📺</div>
                    <div class="text-white text-sm font-semibold">Today's Specials</div>
                    <div class="text-white/40 text-xs">Auto-refreshes every 15 min</div>
                  </div>
                </div>
                <div class="flex items-center gap-2 bg-white/5 rounded-xl px-4 py-2.5">
                  <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                  <span class="text-xs text-gray-300 flex-1">Display is live</span>
                  <span class="text-[10px] text-gray-500">Open on any Smart TV</span>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <!-- Carousel controls -->
          <div class="absolute bottom-3 inset-x-0 flex items-center justify-center gap-3 z-20">
            <button id="hero-prev" class="w-7 h-7 rounded-full bg-black/50 hover:bg-black/80 border border-white/20 flex items-center justify-center transition-colors">
              <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div id="hero-dots" class="flex items-center gap-1.5">
              <?php for ($_di = 0; $_di < $_slideCount; $_di++): ?>
              <button class="h-1.5 rounded-full transition-all duration-300 <?= $_di === 0 ? 'w-5 bg-white' : 'w-1.5 bg-white/40' ?>"></button>
              <?php endfor; ?>
            </div>
            <button id="hero-next" class="w-7 h-7 rounded-full bg-black/50 hover:bg-black/80 border border-white/20 flex items-center justify-center transition-colors">
              <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<script>
(function() {
  var slides  = document.querySelectorAll('#hero-slides .slide');
  var dots    = document.querySelectorAll('#hero-dots button');
  var current = 0;
  var timer;
  if (slides.length < 2) return;

  function goTo(n) {
    slides[current].classList.remove('opacity-100');
    slides[current].classList.add('opacity-0');
    dots[current].classList.remove('bg-white', 'w-5');
    dots[current].classList.add('bg-white/40', 'w-1.5');
    current = (n + slides.length) % slides.length;
    slides[current].classList.remove('opacity-0');
    slides[current].classList.add('opacity-100');
    dots[current].classList.remove('bg-white/40', 'w-1.5');
    dots[current].classList.add('bg-white', 'w-5');
  }

  function startTimer() { timer = setInterval(function() { goTo(current + 1); }, 4500); }
  function resetTimer() { clearInterval(timer); startTimer(); }

  document.getElementById('hero-prev').addEventListener('click', function() { goTo(current - 1); resetTimer(); });
  document.getElementById('hero-next').addEventListener('click', function() { goTo(current + 1); resetTimer(); });
  dots.forEach(function(dot, i) { dot.addEventListener('click', function() { goTo(i); resetTimer(); }); });
  startTimer();
})();
</script>

<!-- TRUST BAR -->
<section class="bg-gray-50 border-y border-gray-100 py-8 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div>
        <div class="text-2xl font-bold text-gray-900"><?= $c("stat_{$i}_num") ?></div>
        <div class="text-sm text-gray-500"><?= $c("stat_{$i}_label") ?></div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="py-24 px-4">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold text-gray-900 mb-4"><?= $c('features_title', 'Everything you need to manage your screens') ?></h2>
      <p class="text-lg text-gray-500 max-w-2xl mx-auto"><?= $c('features_subtitle') ?></p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <?php
        $features = [
          ['📺','Instant Updates','Change your menus, specials, or promotions from anywhere.'],
          ['☁️','Cloud Dashboard','Manage everything from one clean dashboard.'],
          ['🔄','Auto Refresh','Screens automatically reload content every 15 minutes.'],
          ['🖼️','Drag & Drop','Upload images, drag to reorder, done.'],
          ['🔒','Secure URLs','Every screen gets a unique secure URL.'],
          ['📱','Works Everywhere','Smart TVs, Fire Stick, Android TV Box, Chrome browser.'],
        ];
        foreach ($features as [$icon, $title, $desc]):
      ?>
      <div class="bg-white border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-md transition-all">
        <div class="text-4xl mb-4"><?= $icon ?></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= $title ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="bg-gray-50 py-24 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold text-gray-900 mb-4"><?= $c('hiw_title', 'Up and running in 10 minutes') ?></h2>
    </div>
    <div class="relative">
      <div class="hidden md:block absolute top-8 left-0 right-0 h-px bg-gray-200 mx-32"></div>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="text-center relative">
          <div class="w-16 h-16 bg-primary-500 rounded-2xl flex items-center justify-center text-white text-xl font-bold mx-auto mb-4 relative z-10"><?= $i ?></div>
          <h3 class="font-semibold text-gray-900 mb-2"><?= $c("step_{$i}_title") ?></h3>
          <p class="text-sm text-gray-500"><?= $c("step_{$i}_desc") ?></p>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="py-24 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">Simple, honest pricing</h2>
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
          class="block text-center <?= $i===1?'bg-primary-500 hover:bg-primary-600 text-white':'bg-gray-50 hover:bg-gray-100 text-gray-900' ?> font-semibold py-3 px-6 rounded-xl transition-colors">
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

<!-- TESTIMONIALS -->
<section class="bg-gray-50 py-24 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-16"><h2 class="text-4xl font-bold text-gray-900 mb-4">Loved by hospitality businesses</h2></div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php for ($i = 1; $i <= 3; $i++): ?>
      <div class="bg-white border border-gray-100 rounded-2xl p-7">
        <div class="text-yellow-400 text-lg mb-4">★★★★★</div>
        <p class="text-gray-700 text-sm leading-relaxed mb-5"><?= $c("testimonial_{$i}_quote") ?></p>
        <div>
          <div class="font-semibold text-gray-900 text-sm"><?= $c("testimonial_{$i}_name") ?></div>
          <div class="text-gray-400 text-xs"><?= $c("testimonial_{$i}_role") ?></div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="text-4xl font-bold mb-4"><?= $c('cta_title', 'Ready to modernise your screens?') ?></h2>
    <p class="text-gray-300 mb-10 text-lg"><?= $c('cta_subtitle') ?></p>
    <a href="/register" class="inline-block bg-white text-primary-600 font-bold text-lg px-12 py-4 rounded-xl hover:bg-gray-50 transition-all shadow-lg">
      <?= $c('cta_button', 'Get Started Free →') ?>
    </a>
  </div>
</section>
