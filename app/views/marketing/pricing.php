<section class="py-24 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-10">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-4">Simple, honest pricing</h1>
      <p class="text-xl text-gray-500">Starter plan is 100% free. No credit card required. Cancel anytime.</p>
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
      <?php foreach ($plans as $i => $plan):
        $hasTrial       = (bool)($plan['has_trial'] ?? 1);
        $annualMonthly  = $plan['price_annual'] > 0 ? $plan['price_annual'] / 12 : 0;
        $storageMb      = (int)($plan['max_storage_mb'] ?? 512);
        $storageLabel   = $storageMb >= 1024
                            ? ($storageMb % 1024 === 0 ? intval($storageMb / 1024) : round($storageMb / 1024, 1)) . ' GB'
                            : $storageMb . ' MB';
      ?>
      <div class="bg-white border-2 <?= $i===1 ? 'border-primary-500 shadow-xl shadow-primary-100' : 'border-gray-100' ?> rounded-2xl p-8 relative">
        <?php if ($i === 1): ?>
          <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary-500 text-white text-xs font-bold px-4 py-1.5 rounded-full">MOST POPULAR</div>
        <?php endif; ?>

        <div class="text-sm font-semibold text-gray-500 mb-2"><?= Helpers::e($plan['name']) ?></div>

        <!-- Monthly price (shown by default) -->
        <div class="price-monthly flex items-baseline gap-1 mb-1">
          <?php if ((float)$plan['price_monthly'] <= 0): ?>
          <span class="text-4xl font-extrabold text-green-600">Free</span>
          <?php else: ?>
          <span class="text-4xl font-extrabold text-gray-900">$<?= number_format($plan['price_monthly'], 2) ?></span>
          <span class="text-gray-400 text-sm">USD/mo</span>
          <?php endif; ?>
        </div>
        <!-- Annual price (hidden by default) -->
        <div class="price-annual hidden flex items-baseline gap-1 mb-1">
          <?php if ((float)$plan['price_monthly'] <= 0): ?>
          <span class="text-4xl font-extrabold text-green-600">Free</span>
          <?php else: ?>
          <span class="text-4xl font-extrabold text-gray-900">$<?= number_format($annualMonthly, 2) ?></span>
          <span class="text-gray-400 text-sm">USD/mo</span>
          <?php endif; ?>
        </div>
        <div class="price-annual-note hidden text-xs text-green-600 font-medium mb-1">
          <?= (float)$plan['price_monthly'] <= 0 ? 'No payment required' : '$' . number_format($plan['price_annual'], 2) . ' billed annually' ?>
        </div>

        <div class="text-sm text-gray-400 mb-6">
          Up to <?= (int)$plan['max_screens'] ?> TV screen<?= $plan['max_screens'] > 1 ? 's' : '' ?>
          &nbsp;·&nbsp;<?= $storageLabel ?> storage
        </div>

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
          class="plan-cta block text-center <?= $i===1 ? 'bg-primary-500 hover:bg-primary-600 text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-900' ?> font-semibold py-3 px-6 rounded-xl transition-colors"
          data-slug="<?= Helpers::e($plan['slug']) ?>">
          Start Free Today!
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <p class="text-center text-xs text-gray-400 mt-8">
      All prices in USD. <a href="/contact" class="text-primary-600 hover:text-primary-700">Need custom pricing?</a>
    </p>

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
    btn.style.backgroundColor = isAnnual ? '#ec4899' : '';
    document.querySelectorAll('.price-monthly').forEach(function(el) { el.classList.toggle('hidden', isAnnual); });
    document.querySelectorAll('.price-annual').forEach(function(el) { el.classList.toggle('hidden', !isAnnual); });
    document.querySelectorAll('.price-annual-note').forEach(function(el) { el.classList.toggle('hidden', !isAnnual); });
    document.querySelectorAll('.plan-cta').forEach(function(el) {
      var slug = el.getAttribute('data-slug');
      el.href = '/register?plan=' + slug + (isAnnual ? '&cycle=annual' : '');
    });
  };
})();
</script>

<?php require VIEWS_PATH . '/marketing/_cta.php'; ?>
