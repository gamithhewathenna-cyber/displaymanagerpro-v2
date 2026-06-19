<!-- ABOUT HERO -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-indigo-300 bg-white/10 px-3 py-1 rounded-full mb-5">About Display Manager Pro</span>
    <h1 class="text-4xl sm:text-6xl font-extrabold leading-tight mb-6">
      Manage Every Screen<br>
      <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent">From Anywhere</span>
    </h1>
    <p class="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
      At Display Manager Pro, we help businesses update TV screens, digital menu boards, promotions, announcements, and in-store displays from one simple cloud-based dashboard.
    </p>
  </div>
</section>

<!-- WHO WE HELP -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">

      <!-- Text -->
      <div>
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Who We Help</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">Built for businesses of all sizes</h2>
        <p class="text-gray-500 leading-relaxed mb-5">
          Whether you operate a restaurant, café, salon, retail store, clinic, hotel, showroom, or multi-location business, Display Manager Pro makes it easy to keep your content fresh, engaging, and up to date — without USB drives, printing costs, or manual screen updates.
        </p>
        <p class="text-gray-500 leading-relaxed mb-8">
          Our mission is simple: make digital signage affordable, easy to manage, and accessible for businesses of all sizes.
        </p>
        <div class="grid grid-cols-2 gap-2">
          <?php
            $industries = [
              'Restaurants & Cafés','Retail Stores','Salons & Spas','Hotels & Resorts',
              'Medical Clinics','Fitness Centers','Corporate Offices','Supermarkets',
              'Showrooms','Multi-Location Businesses',
            ];
            foreach ($industries as $ind):
          ?>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= htmlspecialchars($ind) ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Image placeholder 1 -->
      <div class="relative">
        <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-primary-50 to-indigo-100 flex items-center justify-center" style="aspect-ratio:4/3;">
          <div class="text-center p-8">
            <svg class="w-16 h-16 text-primary-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-primary-400 font-medium text-sm">Image Placeholder</p>
            <p class="text-primary-300 text-xs mt-1">Replace with your photo</p>
          </div>
        </div>
        <!-- Decorative badge -->
        <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-xl px-5 py-3 border border-gray-100">
          <div class="text-2xl font-extrabold text-gray-900">500+</div>
          <div class="text-xs text-gray-500">Businesses worldwide</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- THE SMARTER WAY -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">

      <!-- Image placeholder 2 -->
      <div class="relative order-2 lg:order-1">
        <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-50 to-violet-100 flex items-center justify-center" style="aspect-ratio:4/3;">
          <div class="text-center p-8">
            <svg class="w-16 h-16 text-indigo-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <p class="text-indigo-400 font-medium text-sm">Image Placeholder</p>
            <p class="text-indigo-300 text-xs mt-1">Replace with your photo</p>
          </div>
        </div>
        <!-- Decorative badge -->
        <div class="absolute -bottom-4 -right-4 bg-white rounded-2xl shadow-xl px-5 py-3 border border-gray-100">
          <div class="text-2xl font-extrabold text-gray-900">14-day</div>
          <div class="text-xs text-gray-500">Free trial, no card needed</div>
        </div>
      </div>

      <!-- Text -->
      <div class="order-1 lg:order-2">
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">The Smarter Way</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">The smarter way to manage digital signage</h2>
        <p class="text-gray-500 leading-relaxed mb-6">
          Traditional screen management can be time-consuming and costly. Businesses often rely on USB drives, manual updates, and staff intervention to change menus, promotions, pricing, and announcements.
        </p>
        <p class="text-gray-500 leading-relaxed mb-6">
          Display Manager Pro eliminates these challenges with a powerful cloud digital signage platform. With just a few clicks, your content is automatically updated across all connected screens.
        </p>
        <ul class="space-y-3">
          <?php
            $points = [
              'Update screens remotely from any device',
              'Manage multiple TV displays from one dashboard',
              'Schedule and organise content effortlessly',
              'Display menus, promotions, announcements & advertising',
              'Keep every location consistent and up to date',
              'Save time and reduce operational costs',
            ];
            foreach ($points as $pt):
          ?>
          <li class="flex items-start gap-3 text-sm text-gray-600">
            <span class="mt-0.5 w-5 h-5 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-3 h-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
            <?= htmlspecialchars($pt) ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-14">
      <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Why Choose Us</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">Why businesses choose Display Manager Pro</h2>
      <p class="text-gray-500 max-w-2xl mx-auto">Enterprise-level digital signage without enterprise-level complexity or pricing.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
        $reasons = [
          ['☁️','Easy Cloud Management','Update digital displays anytime, anywhere through a secure web-based dashboard.'],
          ['🖥️','Multi-Screen Control','Manage multiple TV screens and locations from a single account.'],
          ['⚡','Instant Content Updates','Change menus, promotions, pricing, and announcements in seconds.'],
          ['👆','No Technical Skills Required','Simple, user-friendly software designed for business owners and staff.'],
          ['🔒','Secure & Reliable','Your content is securely hosted and delivered to connected screens automatically.'],
          ['💰','Affordable Digital Signage','Enterprise-level features without enterprise-level pricing.'],
        ];
        foreach ($reasons as [$icon, $title, $desc]):
      ?>
      <div class="bg-gray-50 border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-md transition-all">
        <div class="text-4xl mb-4"><?= $icon ?></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($title) ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= htmlspecialchars($desc) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- OUR VISION -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-3xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-5">Our Vision</span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-6">Every business deserves great digital signage</h2>
    <p class="text-gray-500 leading-relaxed mb-5 text-lg">
      We believe every business should have access to professional digital signage technology without expensive hardware, complicated software, or long-term contracts.
    </p>
    <p class="text-gray-500 leading-relaxed mb-5">
      Our goal is to help businesses communicate more effectively, increase customer engagement, promote products and services, and create better in-store experiences through modern digital display solutions.
    </p>
    <p class="text-gray-500 leading-relaxed">
      As businesses continue to embrace digital transformation, Display Manager Pro is committed to providing an easy-to-use, reliable, and scalable platform that grows with your needs.
    </p>
  </div>
</section>

<!-- CTA -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="text-4xl font-bold mb-4">Start Your Free 14-Day Trial</h2>
    <p class="text-gray-300 mb-4 text-lg">Experience the easiest way to manage digital signage, TV displays, digital menu boards, and promotional screens.</p>
    <p class="text-gray-400 mb-10 max-w-2xl mx-auto">Join businesses worldwide using Display Manager Pro to simplify screen management, improve customer engagement, and keep their content fresh.</p>
    <a href="/register" class="inline-block bg-white text-primary-600 font-bold text-lg px-12 py-4 rounded-xl hover:bg-gray-50 transition-all shadow-lg">
      Start Free Trial Today →
    </a>
  </div>
</section>
