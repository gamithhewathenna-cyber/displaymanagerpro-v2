<?php $c = function($k,$d=''){return htmlspecialchars(ContentController::get('about',$k,$d));}; ?>
<?php
  $_img1 = Settings::get('content_about_image1','');
  $_img2 = Settings::get('content_about_image2','');
?>

<!-- ABOUT HERO -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-indigo-300 bg-white/10 px-3 py-1 rounded-full mb-5">About Display Manager Pro</span>
    <h1 class="text-4xl sm:text-6xl font-extrabold leading-tight mb-6">
      Manage Every Screen<br>
      <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent">From Anywhere</span>
    </h1>
    <p class="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
      <?= $c('hero_subtitle','At Display Manager Pro, we help businesses update TV screens, digital menu boards, promotions, announcements, and in-store displays from one simple cloud-based dashboard.') ?>
    </p>
  </div>
</section>

<!-- SECTION 1 – WHO WE HELP -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">

      <div>
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4"><?= $c('s1_badge','Who We Help') ?></span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight"><?= $c('s1_title','Built for businesses of all sizes') ?></h2>
        <p class="text-gray-500 leading-relaxed mb-5"><?= $c('s1_body1') ?></p>
        <p class="text-gray-500 leading-relaxed mb-8"><?= $c('s1_body2') ?></p>
        <?php
          $_industries = array_filter(explode("\n", ContentController::get('about','s1_industries','')));
          if (empty($_industries)) $_industries = ['Restaurants & Cafés','Retail Stores','Salons & Spas','Hotels & Resorts','Medical Clinics','Fitness Centers','Corporate Offices','Supermarkets','Showrooms','Multi-Location Businesses'];
        ?>
        <div class="grid grid-cols-2 gap-2">
          <?php foreach ($_industries as $ind): ?>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= htmlspecialchars(trim($ind)) ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Image 1 -->
      <div class="relative">
        <div class="rounded-2xl overflow-hidden" style="aspect-ratio:4/3;">
          <?php if ($_img1): ?>
            <img src="<?= Helpers::e($_img1) ?>" alt="<?= $c('s1_title') ?>" class="w-full h-full object-cover">
          <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-primary-50 to-indigo-100 flex items-center justify-center">
              <div class="text-center p-8">
                <svg class="w-16 h-16 text-primary-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-primary-400 font-medium text-sm">Image Placeholder</p>
                <p class="text-primary-300 text-xs mt-1">Upload via Admin → Content → About</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-xl px-5 py-3 border border-gray-100">
          <div class="text-2xl font-extrabold text-gray-900"><?= $c('s1_stat_num','500+') ?></div>
          <div class="text-xs text-gray-500"><?= $c('s1_stat_label','Businesses worldwide') ?></div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- SECTION 2 – THE SMARTER WAY -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">

      <!-- Image 2 -->
      <div class="relative order-2 lg:order-1">
        <div class="rounded-2xl overflow-hidden" style="aspect-ratio:4/3;">
          <?php if ($_img2): ?>
            <img src="<?= Helpers::e($_img2) ?>" alt="<?= $c('s2_title') ?>" class="w-full h-full object-cover">
          <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-indigo-50 to-violet-100 flex items-center justify-center">
              <div class="text-center p-8">
                <svg class="w-16 h-16 text-indigo-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <p class="text-indigo-400 font-medium text-sm">Image Placeholder</p>
                <p class="text-indigo-300 text-xs mt-1">Upload via Admin → Content → About</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="absolute -bottom-4 -right-4 bg-white rounded-2xl shadow-xl px-5 py-3 border border-gray-100">
          <div class="text-2xl font-extrabold text-gray-900"><?= $c('s2_stat_num','14-day') ?></div>
          <div class="text-xs text-gray-500"><?= $c('s2_stat_label','Free trial, no card needed') ?></div>
        </div>
      </div>

      <div class="order-1 lg:order-2">
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4"><?= $c('s2_badge','The Smarter Way') ?></span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight"><?= $c('s2_title','The smarter way to manage digital signage') ?></h2>
        <p class="text-gray-500 leading-relaxed mb-5"><?= $c('s2_body1') ?></p>
        <p class="text-gray-500 leading-relaxed mb-6"><?= $c('s2_body2') ?></p>
        <?php
          $_bullets = array_filter(explode("\n", ContentController::get('about','s2_bullets','')));
          if (empty($_bullets)) $_bullets = ['Update screens remotely from any device','Manage multiple TV displays from one dashboard','Schedule and organise content effortlessly','Display menus, promotions, announcements & advertising','Keep every location consistent and up to date','Save time and reduce operational costs'];
        ?>
        <ul class="space-y-3">
          <?php foreach ($_bullets as $pt): ?>
          <li class="flex items-start gap-3 text-sm text-gray-600">
            <span class="mt-0.5 w-5 h-5 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
            <?= htmlspecialchars(trim($pt)) ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- SECTION 3 – WHY CHOOSE US -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-14">
      <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4"><?= $c('s3_badge','Why Choose Us') ?></span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4"><?= $c('s3_title','Why businesses choose Display Manager Pro') ?></h2>
      <p class="text-gray-500 max-w-2xl mx-auto"><?= $c('s3_subtitle') ?></p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php for ($r = 1; $r <= 6; $r++): ?>
      <div class="bg-gray-50 border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-md transition-all">
        <div class="text-4xl mb-4"><?= ContentController::get('about',"s3_r{$r}_icon",'') ?></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= $c("s3_r{$r}_title") ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $c("s3_r{$r}_desc") ?></p>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- OUR VISION -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-3xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-5"><?= $c('vision_badge','Our Vision') ?></span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-6"><?= $c('vision_title','Every business deserves great digital signage') ?></h2>
    <p class="text-gray-500 leading-relaxed mb-5 text-lg"><?= $c('vision_body1') ?></p>
    <p class="text-gray-500 leading-relaxed mb-5"><?= $c('vision_body2') ?></p>
    <p class="text-gray-500 leading-relaxed"><?= $c('vision_body3') ?></p>
  </div>
</section>

<!-- RELATED PAGES -->
<section class="bg-white border-y border-gray-100 py-10 px-4">
  <div class="max-w-5xl mx-auto">
    <p class="text-center text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">Learn More</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <a href="/features" class="bg-gray-50 border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-3 transition-all group hover:shadow-md">
        <span class="text-2xl flex-shrink-0">⚡</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Platform Features</div>
          <div class="text-xs text-gray-400 mt-0.5">See what we offer</div>
        </div>
      </a>
      <a href="/industries" class="bg-gray-50 border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-3 transition-all group hover:shadow-md">
        <span class="text-2xl flex-shrink-0">🏢</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Industries We Serve</div>
          <div class="text-xs text-gray-400 mt-0.5">Who we help</div>
        </div>
      </a>
      <a href="/pricing" class="bg-gray-50 border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-3 transition-all group hover:shadow-md">
        <span class="text-2xl flex-shrink-0">💳</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Pricing & Plans</div>
          <div class="text-xs text-gray-400 mt-0.5">Transparent pricing</div>
        </div>
      </a>
      <a href="/contact" class="bg-gray-50 border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-3 transition-all group hover:shadow-md">
        <span class="text-2xl flex-shrink-0">📬</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Get In Touch</div>
          <div class="text-xs text-gray-400 mt-0.5">We'd love to hear from you</div>
        </div>
      </a>
    </div>
  </div>
</section>

<?php require VIEWS_PATH . '/marketing/_cta.php'; ?>
