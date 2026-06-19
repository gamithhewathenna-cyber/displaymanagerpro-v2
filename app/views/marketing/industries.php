<?php
$c = function($k,$d=''){return htmlspecialchars(ContentController::get('industries',$k,$d));};
$_img1 = Settings::get('content_industries_image1','');
$_img2 = Settings::get('content_industries_image2','');
?>

<!-- HERO -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-indigo-300 bg-white/10 px-3 py-1 rounded-full mb-5">Digital Signage Platform</span>
    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
      <?= $c('hero_title','Transform Any TV Into A Powerful Digital Sign') ?>
    </h1>
    <p class="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed mb-8">
      <?= $c('hero_body','DisplayManagerPro is a cloud-based digital signage platform that helps businesses manage TV screens, digital menu boards, promotional displays, and customer communications from anywhere.') ?>
    </p>
    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="/register" class="inline-block bg-white text-primary-600 font-bold px-8 py-3 rounded-xl hover:bg-gray-50 transition-all shadow-lg text-sm">Start Free 14-Day Trial →</a>
      <a href="/pricing" class="inline-block border border-white/30 text-white font-semibold px-8 py-3 rounded-xl hover:bg-white/10 transition-all text-sm">View Pricing</a>
    </div>
  </div>
</section>

<!-- SECTION 1 – MULTI-LOCATION -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
      <div>
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Multi-Location Management</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">
          <?= $c('s1_title','Designed For Growing Multi-Location Businesses') ?>
        </h2>
        <p class="text-gray-500 leading-relaxed mb-4"><?= $c('s1_body1','Managing content across multiple locations can be challenging. Traditional printed signage is costly, time-consuming, and difficult to keep updated.') ?></p>
        <p class="text-gray-500 leading-relaxed mb-4"><?= $c('s1_body2','DisplayManagerPro solves this problem by allowing businesses to centrally manage all screens from a secure cloud platform.') ?></p>
        <p class="text-gray-600 font-medium mb-4"><?= $c('s1_intro','Whether you have 1 screen or 100 screens, you can:') ?></p>
        <?php
          $_bullets1 = array_filter(array_map('trim', explode("\n", ContentController::get('industries','s1_bullets',''))));
          if (empty($_bullets1)) $_bullets1 = ['Update content instantly','Schedule promotions and announcements','Maintain consistent branding','Reduce printing costs','Improve customer engagement','Manage all locations from one dashboard'];
        ?>
        <ul class="space-y-2 mb-6">
          <?php foreach ($_bullets1 as $pt): ?>
          <li class="flex items-center gap-3 text-sm text-gray-600">
            <span class="w-5 h-5 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
            <?= htmlspecialchars($pt) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $c('s1_footer','This makes DisplayManagerPro ideal for franchises, hospitality groups, retail chains, healthcare providers, fitness centres, and corporate organisations.') ?></p>
      </div>
      <div class="rounded-2xl overflow-hidden" style="aspect-ratio:4/3;">
        <?php if ($_img1): ?>
          <img src="<?= Helpers::e($_img1) ?>" alt="Multi-location management" class="w-full h-full object-cover">
        <?php else: ?>
          <div class="w-full h-full bg-gradient-to-br from-primary-50 to-indigo-100 flex items-center justify-center">
            <div class="text-center p-8">
              <svg class="w-16 h-16 text-primary-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <p class="text-primary-400 font-medium text-sm">Image Placeholder</p>
              <p class="text-primary-300 text-xs mt-1">Upload via Admin → Content → Industries</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 2 – INCREASE SALES -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
      <div class="rounded-2xl overflow-hidden order-2 lg:order-1" style="aspect-ratio:4/3;">
        <?php if ($_img2): ?>
          <img src="<?= Helpers::e($_img2) ?>" alt="Increase sales with digital displays" class="w-full h-full object-cover">
        <?php else: ?>
          <div class="w-full h-full bg-gradient-to-br from-indigo-50 to-violet-100 flex items-center justify-center">
            <div class="text-center p-8">
              <svg class="w-16 h-16 text-indigo-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <p class="text-indigo-400 font-medium text-sm">Image Placeholder</p>
              <p class="text-indigo-300 text-xs mt-1">Upload via Admin → Content → Industries</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <div class="order-1 lg:order-2">
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Increase Revenue</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">
          <?= $c('s2_title','Increase Sales With Dynamic Digital Displays') ?>
        </h2>
        <p class="text-gray-500 leading-relaxed mb-4"><?= $c('s2_body1','Studies consistently show that digital signage attracts more attention than static printed materials.') ?></p>
        <p class="text-gray-500 leading-relaxed mb-4"><?= $c('s2_body2','Customers naturally look at screens displaying menus, promotions, product information, and announcements. This creates more opportunities to influence purchasing decisions at the point of sale.') ?></p>
        <p class="text-gray-600 font-medium mb-4"><?= $c('s2_intro','Businesses use DisplayManagerPro to:') ?></p>
        <?php
          $_bullets2 = array_filter(array_map('trim', explode("\n", ContentController::get('industries','s2_bullets',''))));
          if (empty($_bullets2)) $_bullets2 = ['Promote high-margin products','Advertise limited-time offers','Highlight new arrivals','Display seasonal campaigns','Cross-sell related products and services','Increase average transaction value'];
        ?>
        <ul class="space-y-2 mb-6">
          <?php foreach ($_bullets2 as $pt): ?>
          <li class="flex items-center gap-3 text-sm text-gray-600">
            <span class="w-5 h-5 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
            <?= htmlspecialchars($pt) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $c('s2_footer','Instead of relying on outdated posters, your business can communicate with customers in real time.') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 3 – WHY ESSENTIAL -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-14">
      <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Why It Matters</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
        <?= $c('s3_title','Why Digital Signage Is Essential For Modern Businesses') ?>
      </h2>
      <p class="text-gray-500 max-w-2xl mx-auto"><?= $c('s3_body1',"Today's customers expect up-to-date information and engaging visual experiences.") ?></p>
      <p class="text-gray-500 max-w-2xl mx-auto mt-2"><?= $c('s3_body2','Digital signage allows businesses to communicate more effectively while creating a professional and modern environment.') ?></p>
    </div>
    <?php
      $s3benefits = [
        ['b1','🎯','Improve Customer Experience','Display menus, services, promotions, and important information clearly and professionally.'],
        ['b2','⏱️','Save Time','Update all screens remotely without manually replacing printed materials.'],
        ['b3','💰','Reduce Costs','Eliminate ongoing printing and distribution expenses.'],
        ['b4','✅','Maintain Brand Consistency','Ensure every location displays the same branding, messaging, and promotions.'],
        ['b5','📈','Scale Easily','Add new locations and screens without changing your workflow.'],
      ];
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($s3benefits as [$key, $defIcon, $defTitle, $defDesc]): ?>
      <div class="bg-gray-50 border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-md transition-all">
        <div class="text-4xl mb-4"><?= ContentController::get('industries',"s3_{$key}_icon", $defIcon) ?></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= $c("s3_{$key}_title", $defTitle) ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $c("s3_{$key}_desc", $defDesc) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SECTION 4 – REGIONS -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-14">
      <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Global Coverage</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
        <?= $c('s4_title','Supporting Businesses Across Australia, New Zealand, United Kingdom & United States') ?>
      </h2>
    </div>
    <?php
      $regions = [
        ['au','🇦🇺','Australia','Sydney, Melbourne, Brisbane, Perth, Adelaide, Gold Coast and regional locations.'],
        ['nz','🇳🇿','New Zealand','Auckland, Wellington, Christchurch, Hamilton, Tauranga and beyond.'],
        ['uk','🇬🇧','United Kingdom','London, Manchester, Birmingham, Liverpool, Leeds, Bristol and nationwide.'],
        ['us','🇺🇸','United States','New York, Los Angeles, Chicago, Houston, Dallas, Miami, Seattle and across all states.'],
      ];
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <?php foreach ($regions as [$rkey, $flag, $defName, $defCities]): ?>
      <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center hover:border-primary-200 hover:shadow-md transition-all">
        <div class="text-4xl mb-3"><?= $flag ?></div>
        <h3 class="text-lg font-bold text-gray-900 mb-3"><?= $c("s4_{$rkey}_title", $defName) ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $c("s4_{$rkey}_cities", $defCities) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <p class="text-center text-gray-500 max-w-2xl mx-auto">
      <?= $c('s4_footer','No matter where your business operates, DisplayManagerPro provides a simple and reliable way to manage your digital displays from anywhere.') ?>
    </p>
  </div>
</section>

<!-- CTA -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="text-4xl font-bold mb-4"><?= $c('cta_title','Ready To Modernise Your Business Displays?') ?></h2>
    <p class="text-gray-300 mb-4 text-lg"><?= $c('cta_body1','Join businesses worldwide using DisplayManagerPro to manage digital menu boards, promotions, announcements, and customer communications from one powerful cloud platform.') ?></p>
    <p class="text-gray-400 mb-10 max-w-2xl mx-auto"><?= $c('cta_body2','Start your free trial today and discover how easy it is to manage every screen from anywhere.') ?></p>
    <a href="/register" class="inline-block bg-white text-primary-600 font-bold text-lg px-12 py-4 rounded-xl hover:bg-gray-50 transition-all shadow-lg">
      <?= $c('cta_button','Start Your 14-Day Free Trial →') ?>
    </a>
  </div>
</section>
