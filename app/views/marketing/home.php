<?php
$c = function($key, $default='') { return htmlspecialchars(ContentController::get('home', $key, $default)); };
?>
<!-- HERO -->
<section class="hero-gradient text-white py-28 px-4 overflow-hidden relative">
  <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 20% 50%, #6366f1 0%, transparent 50%), radial-gradient(circle at 80% 20%, #8b5cf6 0%, transparent 40%)"></div>
  <div class="max-w-6xl mx-auto relative z-10">
    <div class="text-center max-w-4xl mx-auto">
      <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-sm mb-8">
        <span class="w-2 h-2 bg-green-400 rounded-full"></span>
        <?= $c('badge_text', '14-day free trial · No credit card required') ?>
      </div>
      <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">
        <?= $c('hero_title_1', 'Update Every Restaurant Screen') ?><br>
        <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= $c('hero_title_2', 'In Seconds') ?></span>
      </h1>
      <p class="text-xl md:text-2xl text-gray-300 mb-4 font-light"><?= $c('hero_subtitle', 'No USB Drives. No Complicated Software.') ?></p>
      <p class="text-base text-gray-400 mb-12 max-w-xl mx-auto"><?= $c('hero_description', 'Manage menus, specials, promotions and announcements across all your TV screens from one simple cloud dashboard.') ?></p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/register" class="bg-primary-500 hover:bg-primary-600 text-white font-bold text-lg px-10 py-4 rounded-xl transition-all shadow-lg shadow-primary-500/30 hover:-translate-y-0.5">
          <?= $c('cta_primary', 'Start Free 14-Day Trial →') ?>
        </a>
        <a href="/pricing" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-lg px-10 py-4 rounded-xl transition-all">
          <?= $c('cta_secondary', 'View Pricing') ?>
        </a>
      </div>
    </div>
    <div class="mt-20 float">
      <div class="bg-white/5 border border-white/10 rounded-2xl p-4 max-w-3xl mx-auto backdrop-blur">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-3 h-3 rounded-full bg-red-400"></div>
          <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
          <div class="w-3 h-3 rounded-full bg-green-400"></div>
          <div class="flex-1 bg-white/10 rounded px-3 py-1 text-xs text-gray-400 ml-2">dashboard.signagecloud.com</div>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-4">
          <div class="bg-white/5 rounded-lg p-3"><div class="text-xs text-gray-400 mb-1">Active Screens</div><div class="text-2xl font-bold text-white">3</div><div class="text-xs text-green-400">● All live</div></div>
          <div class="bg-white/5 rounded-lg p-3"><div class="text-xs text-gray-400 mb-1">Slides Uploaded</div><div class="text-2xl font-bold text-white">24</div><div class="text-xs text-gray-400">Across all channels</div></div>
          <div class="bg-white/5 rounded-lg p-3"><div class="text-xs text-gray-400 mb-1">Auto Refresh</div><div class="text-2xl font-bold text-white">15m</div><div class="text-xs text-blue-400">Enabled</div></div>
        </div>
        <div class="bg-white/5 rounded-lg p-3">
          <div class="flex items-center justify-between mb-2"><div class="text-xs text-gray-400">Main Menu Board</div><div class="w-2 h-2 rounded-full bg-green-400"></div></div>
          <div class="flex gap-2">
            <div class="flex-1 h-12 bg-gradient-to-r from-purple-600/40 to-blue-600/40 rounded-md flex items-center justify-center text-xs text-gray-300">Slide 1</div>
            <div class="flex-1 h-12 bg-gradient-to-r from-blue-600/40 to-green-600/40 rounded-md flex items-center justify-center text-xs text-gray-300">Slide 2</div>
            <div class="flex-1 h-12 bg-gradient-to-r from-green-600/40 to-yellow-600/40 rounded-md flex items-center justify-center text-xs text-gray-300">Slide 3</div>
            <div class="w-12 h-12 border-2 border-dashed border-white/20 rounded-md flex items-center justify-center text-gray-500 text-lg">+</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

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
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">Simple, honest pricing</h2>
      <p class="text-gray-500">All plans include a 14-day free trial. No credit card required to start.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($plans as $i => $plan): ?>
      <div class="bg-white border-2 <?= $i===1?'border-primary-500 shadow-xl shadow-primary-100':'border-gray-100' ?> rounded-2xl p-8 relative">
        <?php if ($i===1): ?><div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary-500 text-white text-xs font-bold px-4 py-1.5 rounded-full">MOST POPULAR</div><?php endif; ?>
        <div class="text-sm font-semibold text-gray-500 mb-2"><?= Helpers::e($plan['name']) ?></div>
        <div class="flex items-baseline gap-1 mb-1"><span class="text-4xl font-extrabold text-gray-900">$<?= number_format($plan['price_monthly'],0) ?></span><span class="text-gray-400 text-sm">AUD/mo</span></div>
        <div class="text-sm text-gray-400 mb-6">Up to <?= $plan['max_screens'] ?> TV screen<?= $plan['max_screens']>1?'s':'' ?></div>
        <?php $feats = json_decode($plan['features']??'[]',true); ?>
        <ul class="space-y-3 mb-8">
          <?php foreach ($feats as $feat): ?>
          <li class="flex items-center gap-2.5 text-sm text-gray-600">
            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= Helpers::e($feat) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="/register?plan=<?= Helpers::e($plan['slug']) ?>" class="block text-center <?= $i===1?'bg-primary-500 hover:bg-primary-600 text-white':'bg-gray-50 hover:bg-gray-100 text-gray-900' ?> font-semibold py-3 px-6 rounded-xl transition-colors">Start Free Trial</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

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
