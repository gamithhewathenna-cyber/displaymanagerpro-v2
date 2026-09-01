<?php
$selectedCycle = in_array($_GET['cycle'] ?? '', ['monthly', 'annual']) ? $_GET['cycle'] : 'monthly';
$selectedPlan  = $_GET['plan'] ?? 'starter';
$old           = Session::getFlash('old') ?? [];

// Determine the initially selected plan so we can set the correct button text
$_activePlan = null;
foreach ($plans as $_p) {
    if ($_p['slug'] === $selectedPlan) { $_activePlan = $_p; break; }
}
if (!$_activePlan) $_activePlan = $plans[0] ?? null;
?>

<h2 class="text-2xl font-bold text-gray-900 mb-1" id="register-heading">Create your account</h2>
<p class="text-gray-500 text-sm mb-6" id="register-subheading">Get started today</p>

<form method="POST" action="/register" class="space-y-4">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <input type="hidden" name="cycle" id="register-cycle" value="<?= Helpers::e($selectedCycle) ?>">

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
    <input type="text" name="name" required value="<?= Helpers::e($old['name'] ?? '') ?>"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="Jane Smith">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
    <input type="email" name="email" required value="<?= Helpers::e($old['email'] ?? '') ?>"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="you@example.com">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
    <input type="password" name="password" required minlength="8"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="At least 8 characters">
  </div>

  <!-- Plan + billing cycle -->
  <div>
    <div class="flex items-center justify-between mb-2">
      <label class="block text-sm font-medium text-gray-700">Choose your plan</label>
      <!-- Billing cycle toggle -->
      <div class="flex items-center gap-0.5 p-0.5 bg-gray-100 rounded-lg text-xs">
        <button type="button" id="reg-cycle-monthly" onclick="regSetCycle('monthly')"
          class="px-3 py-1.5 rounded-md font-semibold transition-all <?= $selectedCycle === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500' ?>">
          Monthly
        </button>
        <button type="button" id="reg-cycle-annual" onclick="regSetCycle('annual')"
          class="px-3 py-1.5 rounded-md font-semibold transition-all <?= $selectedCycle === 'annual' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500' ?>">
          Annual <span class="text-green-600">–17%</span>
        </button>
      </div>
    </div>

    <!-- Annual notice -->
    <div id="reg-annual-notice" class="<?= $selectedCycle === 'annual' ? '' : 'hidden' ?> mb-2 bg-green-50 border border-green-200 text-green-700 text-xs rounded-xl px-3 py-2 flex items-center gap-2">
      <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
      Annual billing — one payment per year, ~17% cheaper than monthly.
    </div>

    <div class="space-y-2">
      <?php foreach ($plans as $plan):
        $isChecked      = ($selectedPlan === $plan['slug']) || ($plan['slug'] === 'starter' && !array_filter($plans, fn($p) => $p['slug'] === $selectedPlan));
        $annualPerMonth = $plan['price_annual'] > 0 ? $plan['price_annual'] / 12 : $plan['price_monthly'];
      ?>
      <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3.5 cursor-pointer hover:border-primary-300 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
        <input type="radio" name="plan" value="<?= Helpers::e($plan['slug']) ?>"
          <?= $isChecked ? 'checked' : '' ?>
          data-has-trial="<?= (int)(bool)($plan['has_trial'] ?? 1) ?>"
          data-slug="<?= Helpers::e($plan['slug']) ?>"
          data-price-monthly="<?= (float)$plan['price_monthly'] ?>"
          data-price-annual="<?= (float)$plan['price_annual'] ?>"
          class="text-primary-500 focus:ring-primary-500">
        <div class="flex-1 flex items-center justify-between gap-2">
          <div>
            <div class="font-semibold text-sm text-gray-900"><?= Helpers::e($plan['name']) ?></div>
            <div class="text-xs text-gray-400">Up to <?= (int)$plan['max_screens'] ?> screen<?= $plan['max_screens'] > 1 ? 's' : '' ?></div>
          </div>
          <div class="text-right flex-shrink-0">
            <div class="reg-price-monthly font-bold text-sm <?= $selectedCycle === 'annual' ? 'hidden' : '' ?> <?= (float)$plan['price_monthly'] <= 0 ? 'text-green-600' : 'text-gray-900' ?>">
              <?= (float)$plan['price_monthly'] <= 0 ? 'Free' : '$' . number_format($plan['price_monthly'], 2) . '/mo' ?>
            </div>
            <div class="reg-price-annual <?= $selectedCycle === 'annual' ? '' : 'hidden' ?>">
              <?php if ((float)$plan['price_monthly'] <= 0): ?>
              <div class="font-bold text-green-600 text-sm">Free</div>
              <?php else: ?>
              <div class="font-bold text-gray-900 text-sm">$<?= number_format($annualPerMonth, 2) ?>/mo</div>
              <div class="text-xs text-green-600 font-medium">$<?= number_format($plan['price_annual'], 2) ?>/yr</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Coupon code -->
  <div class="mt-4">
    <label class="block text-xs font-medium text-gray-600 mb-1">Coupon Code (optional)</label>
    <div class="flex gap-2">
      <input id="reg-coupon-input" name="coupon_code" type="text"
        value="<?= Helpers::e($_POST['coupon_code'] ?? '') ?>"
        placeholder="Enter coupon code"
        class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-indigo-400 uppercase"
        oninput="this.value = this.value.toUpperCase(); regCouponReset()">
      <button type="button" onclick="regValidateCoupon()"
        class="bg-gray-100 hover:bg-indigo-600 hover:text-white text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
        Apply
      </button>
    </div>
    <p id="reg-coupon-msg" class="text-xs mt-1 hidden"></p>
    <input type="hidden" id="reg-coupon-valid" name="coupon_valid" value="0">
  </div>

  <button type="submit" id="register-submit-btn" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors text-sm mt-2">
    Create Account
  </button>
  <p class="text-xs text-gray-400 text-center">By creating an account you agree to our Terms of Service and Privacy Policy.</p>
</form>

<p class="text-center text-sm text-gray-500 mt-5">
  Already have an account?
  <a href="/login" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
</p>

<script>
// Plan selection no longer changes the heading/button copy — every plan starts
// the same way, with no trial framing. It does still reset any coupon discount
// preview, since that amount was computed against the previously-selected plan.
document.querySelectorAll('input[name="plan"]').forEach(function(radio) {
  radio.addEventListener('change', function() {
    if (typeof regCouponReset === 'function') regCouponReset();
  });
});

function regSetCycle(cycle) {
  var isAnnual = cycle === 'annual';
  document.getElementById('register-cycle').value = cycle;

  // The price basis changed — any previously-shown discount amount is now stale
  if (typeof regCouponReset === 'function') regCouponReset();

  // Price blocks
  document.querySelectorAll('.reg-price-monthly').forEach(function(el) { el.classList.toggle('hidden', isAnnual); });
  document.querySelectorAll('.reg-price-annual').forEach(function(el) { el.classList.toggle('hidden', !isAnnual); });

  // Annual notice
  var notice = document.getElementById('reg-annual-notice');
  if (notice) notice.classList.toggle('hidden', !isAnnual);

  // Toggle button styles
  var btnM = document.getElementById('reg-cycle-monthly');
  var btnA = document.getElementById('reg-cycle-annual');
  if (isAnnual) {
    btnA.classList.add('bg-white','text-gray-900','shadow-sm'); btnA.classList.remove('text-gray-500');
    btnM.classList.remove('bg-white','text-gray-900','shadow-sm'); btnM.classList.add('text-gray-500');
  } else {
    btnM.classList.add('bg-white','text-gray-900','shadow-sm'); btnM.classList.remove('text-gray-500');
    btnA.classList.remove('bg-white','text-gray-900','shadow-sm'); btnA.classList.add('text-gray-500');
  }
}

// ── Coupon AJAX validation ──────────────────────────────────────────────────
function regCouponReset() {
  var msg = document.getElementById('reg-coupon-msg');
  msg.classList.add('hidden');
  document.getElementById('reg-coupon-valid').value = '0';
}

function regGetPlanSlug() {
  var radio = document.querySelector('input[name="plan"]:checked');
  return radio ? radio.dataset.slug || '' : '';
}

function regGetPlanPrice() {
  var radio = document.querySelector('input[name="plan"]:checked');
  if (!radio) return 0;
  var cycle = document.getElementById('register-cycle').value;
  var price = cycle === 'annual' ? radio.dataset.priceAnnual : radio.dataset.priceMonthly;
  return parseFloat(price) || 0;
}

function regGetEmail() {
  var el = document.getElementById('email');
  return el ? el.value.trim() : '';
}

function regValidateCoupon() {
  var code = document.getElementById('reg-coupon-input').value.trim();
  var msg  = document.getElementById('reg-coupon-msg');
  if (!code) {
    msg.textContent = 'Please enter a coupon code.';
    msg.className = 'text-xs mt-1 text-red-500';
    return;
  }
  var price = regGetPlanPrice();
  if (price <= 0) {
    msg.textContent = 'This plan is free — no coupon needed.';
    msg.className = 'text-xs mt-1 text-gray-400';
    msg.classList.remove('hidden');
    return;
  }
  msg.textContent = 'Checking…';
  msg.className = 'text-xs mt-1 text-gray-400';
  msg.classList.remove('hidden');

  var data = new URLSearchParams({
    code: code,
    plan_slug: regGetPlanSlug(),
    email: regGetEmail(),
    price: price,
  });

  fetch('/coupon/validate', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.valid) {
        var extra = (res.discount_amount != null && res.final_price != null)
          ? ' You save $' + res.discount_amount.toFixed(2) + ' — new total: $' + res.final_price.toFixed(2) + '.'
          : '';
        msg.textContent = '✓ ' + res.message + extra;
        msg.className = 'text-xs mt-1 text-green-600 font-medium';
        document.getElementById('reg-coupon-valid').value = '1';
      } else {
        msg.textContent = res.message;
        msg.className = 'text-xs mt-1 text-red-500';
        document.getElementById('reg-coupon-valid').value = '0';
      }
    })
    .catch(function() {
      msg.textContent = 'Could not check coupon. Please try again.';
      msg.className = 'text-xs mt-1 text-red-500';
    });
}

// Allow pressing Enter in the coupon field to trigger Apply
document.getElementById('reg-coupon-input').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') { e.preventDefault(); regValidateCoupon(); }
});
</script>
