<?php
// Variables from controller: $city (array), $citySlug (string), $allCities (array)
$cityName    = htmlspecialchars($city['name']);
$countryName = htmlspecialchars($city['country']);
$stateLabel  = $city['state'] ? ', ' . $city['state'] : '';
$locationStr = $cityName . $stateLabel . ', ' . $countryName;

// Country peer cities for "Also serving" links
$peers = array_filter($allCities, fn($c, $s) => $c['country'] === $city['country'] && $s !== $citySlug, ARRAY_FILTER_USE_BOTH);

// Country-specific industry phrase
$industryPhrase = match($city['country']) {
    'Australia'      => 'restaurants, cafés, retail stores, hair salons, gyms, medical clinics, and hospitality venues',
    'New Zealand'    => 'cafés, restaurants, retail stores, salons, fitness studios, and hospitality businesses',
    'United Kingdom' => 'pubs, restaurants, retail shops, salons, gyms, healthcare practices, and corporate offices',
    'United States'  => 'restaurants, retail stores, bars, gyms, medical offices, corporate offices, and franchise businesses',
    default          => 'restaurants, retail stores, salons, gyms, and hospitality businesses',
};

// Currency / trial phrasing by country
$currency = match($city['country']) {
    'Australia'      => 'AUD',
    'New Zealand'    => 'NZD',
    'United Kingdom' => 'GBP',
    default          => 'USD',
};
?>
<!-- City-specific JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "DisplayNex",
  "applicationCategory": "BusinessApplication",
  "operatingSystem": "Web",
  "description": "Digital signage software for <?= $cityName ?>, <?= $countryName ?>. Manage TV screens, digital menu boards and promotional displays from a secure cloud dashboard.",
  "areaServed": {
    "@type": "City",
    "name": "<?= $cityName ?>",
    "containedInPlace": { "@type": "Country", "name": "<?= $countryName ?>" }
  },
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD",
    "description": "Free Starter plan, no credit card required"
  }
}
</script>

<!-- HERO -->
<section class="hero-gradient text-white py-24 px-4">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-indigo-300 bg-white/10 px-3 py-1 rounded-full mb-5">
      <?= $city['flag'] ?> <?= htmlspecialchars($locationStr) ?>
    </span>
    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
      Digital Signage Software<br>
      <span style="background:linear-gradient(90deg,#a78bfa,#60a5fa);-webkit-background-clip:text;-webkit-text-fill-color:transparent">for <?= $cityName ?> Businesses</span>
    </h1>
    <p class="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed mb-8">
      DisplayNex helps <?= $cityName ?> businesses manage TV screens, digital menu boards,
      promotional displays, and customer communications from one simple cloud dashboard.
      Update your content from anywhere — no USB drives, no on-site visits.
    </p>
    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="/register" class="inline-block bg-white text-primary-600 font-bold px-8 py-3 rounded-xl hover:bg-gray-50 transition-all shadow-lg text-sm">Start Free Today →</a>
      <a href="/pricing" class="inline-block border border-white/30 text-white font-semibold px-8 py-3 rounded-xl hover:bg-white/10 transition-all text-sm">View Pricing</a>
    </div>
    <p class="mt-4 text-sm text-gray-400">No credit card required · Cancel anytime · Works on any Smart TV</p>
  </div>
</section>

<!-- WHAT LOCAL BUSINESSES GET -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">
    <div class="text-center mb-14">
      <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Built for <?= $cityName ?></span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
        Why <?= $cityName ?> businesses choose DisplayNex
      </h2>
      <p class="text-gray-500 max-w-2xl mx-auto">
        From small independent cafés to multi-location franchises across <?= $countryName ?>,
        our platform makes digital signage simple and affordable.
      </p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
        $benefits = [
          ['☁️', 'Manage from Anywhere',       "Update your " . $cityName . " screens instantly from any device — laptop, tablet, or phone."],
          ['⚡', 'Instant Content Updates',     'Change menus, promotions, pricing, and announcements in seconds. No waiting, no IT.'],
          ['🖥️', 'Works on Any Screen',          'Compatible with Smart TVs, Amazon Fire Stick, Android TV boxes, and Chrome browsers.'],
          ['💰', 'Reduce Printing Costs',        'Stop paying for printed menus and posters. Update everything digitally for a flat monthly fee.'],
          ['📍', 'Multi-Location Management',   'Managing multiple locations in ' . $countryName . '? Control all your screens from one account.'],
          ['🔒', 'Secure & Reliable',            '99.9% uptime. Your content is hosted securely and delivered to your screens automatically.'],
        ];
        foreach ($benefits as [$icon, $title, $desc]):
      ?>
      <div class="bg-gray-50 border border-gray-100 rounded-2xl p-7 hover:border-primary-200 hover:shadow-md transition-all">
        <div class="text-4xl mb-4"><?= $icon ?></div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= $title ?></h3>
        <p class="text-gray-500 text-sm leading-relaxed"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- WHO USES IT IN THIS CITY -->
<section class="py-20 px-4 bg-gray-50">
  <div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
      <div>
        <span class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4">Industries We Serve in <?= $cityName ?></span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">
          Trusted by <?= $cityName ?> businesses across every sector
        </h2>
        <p class="text-gray-500 leading-relaxed mb-6">
          DisplayNex is used by <?= $industryPhrase ?> in <?= $cityName ?>.
          Whether you operate a single location or manage screens across <?= $countryName ?>,
          our platform scales with your business.
        </p>
        <div class="grid grid-cols-2 gap-2 mb-8">
          <?php
            $industries = ['Restaurants & Cafés','Retail Stores','Hair Salons & Spas','Hotels & Hospitality','Gyms & Fitness Centres','Medical Clinics','Corporate Offices','Franchise Businesses'];
            foreach ($industries as $ind):
          ?>
          <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= htmlspecialchars($ind) ?>
          </div>
          <?php endforeach; ?>
        </div>
        <a href="/industries" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
          See all industries we serve →
        </a>
      </div>
      <div class="bg-gradient-to-br from-primary-50 to-indigo-100 rounded-3xl p-10 flex items-center justify-center" style="min-height:320px;">
        <div class="text-center">
          <div class="text-6xl mb-4"><?= $city['flag'] ?></div>
          <div class="text-2xl font-extrabold text-gray-800 mb-1"><?= $cityName ?></div>
          <div class="text-gray-500 text-sm"><?= $countryName ?></div>
          <div class="mt-4 text-xs text-gray-400">Serving businesses across <?= $countryName ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-14">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">Up and running in 10 minutes</h2>
      <p class="text-gray-500">No technical knowledge required. If you can send an email, you can manage your screens.</p>
    </div>
    <div class="relative">
      <div class="hidden md:block absolute top-8 left-0 right-0 h-px bg-gray-200 mx-32"></div>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <?php
          $steps = [
            ['Create account',   'Sign up and choose your plan — Starter is free, no credit card required.'],
            ['Create a channel', 'Give it a name and choose landscape or portrait orientation.'],
            ['Upload content',   'Drag and drop your menus, promotions, or announcements.'],
            ['Display on TV',    'Open the secure URL on any Smart TV or media player. Done.'],
          ];
          foreach ($steps as $i => [$stepTitle, $stepDesc]):
        ?>
        <div class="text-center relative">
          <div class="w-16 h-16 bg-primary-500 rounded-2xl flex items-center justify-center text-white text-xl font-bold mx-auto mb-4 relative z-10"><?= $i + 1 ?></div>
          <h3 class="font-semibold text-gray-900 mb-2"><?= $stepTitle ?></h3>
          <p class="text-sm text-gray-500"><?= $stepDesc ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($peers)): ?>
<!-- OTHER CITIES IN SAME COUNTRY -->
<section class="py-16 px-4 bg-gray-50 border-y border-gray-100">
  <div class="max-w-5xl mx-auto">
    <p class="text-center text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">
      Also serving <?= $countryName ?>
    </p>
    <div class="flex flex-wrap items-center justify-center gap-3">
      <?php foreach ($peers as $peerSlug => $peerData): ?>
      <a href="/digital-signage/<?= htmlspecialchars($peerSlug) ?>"
         class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-primary-300 rounded-full px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-all">
        <?= $peerData['flag'] ?> Digital Signage <?= htmlspecialchars($peerData['name']) ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>


<?php require VIEWS_PATH . '/marketing/_cta.php'; ?>
