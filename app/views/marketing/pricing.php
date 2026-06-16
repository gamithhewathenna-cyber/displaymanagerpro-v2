<section class="py-24 px-4">
  <div class="max-w-4xl mx-auto text-center mb-12">
    <h1 class="text-5xl font-extrabold text-gray-900 mb-4">Simple, honest pricing</h1>
    <p class="text-xl text-gray-500">Start free for 14 days. No credit card required. Cancel anytime.</p>
  </div>

  <!-- Billing cycle toggle -->
  <div class="flex justify-center mb-10">
    <div class="inline-flex bg-gray-100 rounded-xl p-1 gap-1">
      <button id="btn-monthly" onclick="setBilling('monthly')"
        class="px-5 py-2 rounded-lg text-sm font-semibold transition-all bg-white text-gray-900 shadow-sm">
        Monthly
      </button>
      <button id="btn-annual" onclick="setBilling('annual')"
        class="px-5 py-2 rounded-lg text-sm font-semibold transition-all text-gray-500">
        Annual &nbsp;<span class="text-green-600 font-bold">Save 17%</span>
      </button>
    </div>
  </div>

  <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($plans as $i => $plan):
      $monthlyPrice   = (float)$plan['price_monthly'];
      $annualTotal    = (float)$plan['price_annual'];
      $annualPerMonth = $annualTotal > 0 ? round($annualTotal / 12, 2) : $monthlyPrice;
      $savePct        = ($monthlyPrice > 0 && $annualTotal > 0)
                          ? round((1 - $annualTotal / ($monthlyPrice * 12)) * 100)
                          : 0;
      $storageMb      = (int)($plan['max_storage_mb'] ?? 512);
      $storageLabel   = $storageMb >= 1024
                          ? ($storageMb % 1024 === 0 ? intval($storageMb / 1024) : round($storageMb / 1024, 1)) . ' GB'
                          : $storageMb . ' MB';
      $currency       = Helpers::e($plan['currency'] ?? 'AUD');
      $slug           = Helpers::e($plan['slug']);
    ?>
    <div class="bg-white border-2 <?= $i===1 ? 'border-indigo-500 shadow-xl shadow-indigo-100' : 'border-gray-100' ?> rounded-2xl p-8 relative">
      <?php if ($i === 1): ?>
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-500 text-white text-xs font-bold px-4 py-1.5 rounded-full">MOST POPULAR</div>
      <?php endif; ?>

      <div class="text-sm font-semibold text-gray-400 mb-2"><?= Helpers::e($plan['name']) ?></div>

      <!-- Monthly price display -->
      <div class="price-monthly">
        <div class="text-4xl font-extrabold text-gray-900 mb-1">
          $<?= number_format($monthlyPrice, 0) ?><span class="text-lg font-normal text-gray-400"> <?= $currency ?>/mo</span>
        </div>
        <div class="text-sm text-gray-400 mb-1">Billed monthly</div>
      </div>

      <!-- Annual price display -->
      <div class="price-annual hidden">
        <div class="text-4xl font-extrabold text-gray-900 mb-1">
          $<?= number_format($annualPerMonth, 0) ?><span class="text-lg font-normal text-gray-400"> <?= $currency ?>/mo</span>
        </div>
        <div class="text-sm text-gray-400 mb-1">
          $<?= number_format($annualTotal, 0) ?> billed annually
          <?php if ($savePct > 0): ?>
            &nbsp;·&nbsp;<span class="text-green-600 font-semibold">Save <?= $savePct ?>%</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="text-sm text-gray-400 mt-1 mb-6">
        Up to <?= (int)$plan['max_screens'] ?> TV screen<?= $plan['max_screens'] > 1 ? 's' : '' ?>
        &nbsp;·&nbsp;<?= $storageLabel ?> storage
      </div>

      <?php $feats = json_decode($plan['features'] ?? '[]', true); ?>
      <ul class="space-y-2.5 mb-8">
        <?php foreach ($feats as $f): ?>
        <li class="flex items-center gap-2 text-sm text-gray-600">
          <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          <?= Helpers::e($f) ?>
        </li>
        <?php endforeach; ?>
      </ul>

      <a class="plan-cta block text-center <?= $i===1 ? 'bg-indigo-500 hover:bg-indigo-600 text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-900' ?> font-semibold py-3 rounded-xl transition-colors"
         href="/register?plan=<?= $slug ?>"
         data-slug="<?= $slug ?>">
        Start Free Trial
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-12 text-center text-sm text-gray-400">
    All prices in AUD. <a href="/contact" class="text-indigo-600 hover:text-indigo-700 font-medium">Need custom pricing?</a>
  </div>
</section>

<script>
function setBilling(cycle) {
  var isAnnual = cycle === 'annual';

  document.querySelectorAll('.price-monthly').forEach(function(el) {
    el.classList.toggle('hidden', isAnnual);
  });
  document.querySelectorAll('.price-annual').forEach(function(el) {
    el.classList.toggle('hidden', !isAnnual);
  });

  document.querySelectorAll('.plan-cta').forEach(function(el) {
    var slug = el.getAttribute('data-slug');
    el.href = '/register?plan=' + slug + (isAnnual ? '&cycle=annual' : '');
  });

  var btnMonthly = document.getElementById('btn-monthly');
  var btnAnnual  = document.getElementById('btn-annual');

  if (isAnnual) {
    btnAnnual.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    btnAnnual.classList.remove('text-gray-500');
    btnMonthly.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    btnMonthly.classList.add('text-gray-500');
  } else {
    btnMonthly.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    btnMonthly.classList.remove('text-gray-500');
    btnAnnual.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    btnAnnual.classList.add('text-gray-500');
  }
}
</script>
