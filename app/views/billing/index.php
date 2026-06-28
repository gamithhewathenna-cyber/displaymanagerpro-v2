<?php
$isTrialing   = Subscription::isTrialing($sub);
$isActive     = Subscription::isActive($sub);
$trialDaysLeft= Subscription::trialDaysLeft($sub);
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <!-- Left: Subscription details -->
  <div class="lg:col-span-2 space-y-5">

    <!-- Current plan -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h2 class="font-semibold text-gray-900 mb-5">Current Plan</h2>
      <?php if ($sub): ?>
      <div class="flex items-start justify-between gap-4 mb-5">
        <div>
          <div class="text-2xl font-bold text-gray-900 mb-1"><?= Helpers::e($sub['plan_name'] ?? 'Unknown') ?> Plan</div>
          <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1 rounded-full <?= $isActive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
              <span class="w-2 h-2 rounded-full <?= $isActive ? 'bg-green-400' : 'bg-red-400' ?>"></span>
              <?= $isTrialing ? "Trial · $trialDaysLeft days left" : ($isActive ? 'Active' : ucfirst($sub['status'])) ?>
            </span>
          </div>
        </div>
        <?php if (!empty($sub['stripe_subscription_id'])): ?>
        <span class="text-xs text-gray-400 font-mono mt-1">ID: <?= Helpers::e(substr($sub['stripe_subscription_id'], 0, 20)) ?>…</span>
        <?php endif; ?>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-gray-50 rounded-xl p-4">
          <div class="text-xs text-gray-400 mb-1">TV Screens</div>
          <div class="font-bold text-gray-900">Up to <?= $sub['max_screens'] ?? 1 ?></div>
        </div>
        <div class="bg-gray-50 rounded-xl p-4">
          <div class="text-xs text-gray-400 mb-1">Billing</div>
          <div class="font-bold text-gray-900"><?= ucfirst($sub['billing_cycle'] ?? 'monthly') ?></div>
        </div>
        <?php if ($sub['trial_ends_at'] && $isTrialing): ?>
        <div class="bg-indigo-50 rounded-xl p-4">
          <div class="text-xs text-indigo-400 mb-1">Trial ends</div>
          <div class="font-bold text-indigo-700"><?= date('M j, Y', strtotime($sub['trial_ends_at'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if ($sub['current_period_end'] && !$isTrialing): ?>
        <div class="bg-gray-50 rounded-xl p-4">
          <div class="text-xs text-gray-400 mb-1">Next renewal</div>
          <div class="font-bold text-gray-900"><?= date('M j, Y', strtotime($sub['current_period_end'])) ?></div>
        </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($sub['stripe_subscription_id']) && in_array($sub['status'], ['active','trialing'])): ?>
      <div class="mt-5 pt-5 border-t border-gray-50">
        <button type="button" onclick="document.getElementById('cancel-confirm').classList.remove('hidden')" id="cancel-trigger"
          class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Cancel subscription</button>

        <div id="cancel-confirm" class="hidden mt-4 bg-red-50 border border-red-100 rounded-xl p-4">
          <div class="flex items-start gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
              <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div>
              <div class="font-semibold text-red-900 text-sm mb-1">Cancel your subscription?</div>
              <div class="text-red-700 text-xs leading-relaxed">Your screens will stop displaying at the end of the current billing period. You can reactivate at any time.</div>
            </div>
          </div>
          <label class="flex items-center gap-2 mb-4 cursor-pointer">
            <input type="checkbox" id="cancel-checkbox" onchange="document.getElementById('cancel-submit').disabled=!this.checked"
              class="w-4 h-4 rounded text-red-500 border-red-300 focus:ring-red-400">
            <span class="text-xs text-red-800 font-medium">I understand my screens will stop working</span>
          </label>
          <div class="flex gap-2">
            <form method="POST" action="/billing/cancel">
              <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
              <button type="submit" id="cancel-submit" disabled
                class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                Yes, cancel subscription
              </button>
            </form>
            <button type="button" onclick="document.getElementById('cancel-confirm').classList.add('hidden');document.getElementById('cancel-checkbox').checked=false;document.getElementById('cancel-submit').disabled=true"
              class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
              Keep subscription
            </button>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php else: ?>
      <div class="text-center py-8 text-gray-400">
        <div class="text-4xl mb-3">💳</div>
        <div class="font-medium text-gray-600 mb-1">No subscription yet</div>
        <div class="text-sm">Choose a plan below to get started.</div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Upgrade plans -->
    <?php if (!$sub || empty($sub['stripe_subscription_id']) || $isTrialing): ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h2 class="font-semibold text-gray-900 mb-1"><?= $isTrialing ? 'Activate your subscription' : 'Choose a plan' ?></h2>
      <p class="text-sm text-gray-400 mb-5">Select a billing cycle and plan, then click <strong>Continue to Payment</strong>.</p>

      <!-- Billing cycle toggle -->
      <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl w-fit mb-5">
        <button type="button" id="cycle-btn-monthly" onclick="setCycle('monthly')"
          class="px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-white text-gray-900 shadow-sm">
          Monthly
        </button>
        <button type="button" id="cycle-btn-annual" onclick="setCycle('annual')"
          class="px-4 py-2 rounded-lg text-sm font-semibold transition-all text-gray-500">
          Annual &nbsp;<span class="text-green-600 font-semibold text-xs">Save ~17%</span>
        </button>
      </div>

      <form method="POST" action="/billing/subscribe" id="plan-form">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <input type="hidden" name="billing_cycle" id="billing-cycle-input" value="monthly">
        <input type="hidden" name="plan_id" id="selected-plan-id" value="">

        <div class="space-y-3 mb-5" id="plan-cards">
          <?php foreach ($plans as $plan):
            $isCurrent      = ($sub['plan_id'] ?? 0) == $plan['id'];
            $annualPerMonth = $plan['price_annual'] > 0 ? round($plan['price_annual'] / 12, 0) : 0;
          ?>
          <label class="plan-card flex items-center gap-4 border-2 rounded-xl p-4 cursor-pointer transition-all
            <?= $isCurrent ? 'border-primary-400 bg-primary-50' : 'border-gray-100 hover:border-primary-200' ?>"
            data-plan-id="<?= $plan['id'] ?>"
            data-has-monthly="<?= $plan['stripe_price_id_monthly'] ? '1' : '0' ?>"
            data-has-annual="<?= $plan['stripe_price_id_annual'] ? '1' : '0' ?>">

            <!-- Radio dot -->
            <div class="w-5 h-5 rounded-full border-2 flex-shrink-0 flex items-center justify-center transition-all
              <?= $isCurrent ? 'border-primary-500 bg-primary-500' : 'border-gray-300' ?>" id="radio-<?= $plan['id'] ?>">
              <div class="w-2 h-2 rounded-full bg-white <?= $isCurrent ? '' : 'hidden' ?>" id="radio-dot-<?= $plan['id'] ?>"></div>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-gray-900"><?= Helpers::e($plan['name']) ?></span>
                <?php if ($isCurrent): ?>
                <span class="bg-primary-100 text-primary-700 text-xs font-semibold px-2 py-0.5 rounded-full">Current</span>
                <?php endif; ?>
                <span class="plan-badge-monthly <?= '' ?> text-xs">
                  <?php if (!$plan['stripe_price_id_monthly']): ?>
                  <span class="bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">PayPal not configured</span>
                  <?php endif; ?>
                </span>
                <span class="plan-badge-annual hidden text-xs">
                  <?php if (!$plan['stripe_price_id_annual']): ?>
                  <span class="bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">Annual PayPal plan not configured</span>
                  <?php else: ?>
                  <span class="bg-green-50 text-green-700 font-semibold px-2 py-0.5 rounded-full">Save ~17%</span>
                  <?php endif; ?>
                </span>
              </div>
              <div class="text-xs text-gray-400 mt-0.5">
                Up to <?= $plan['max_screens'] ?> screen<?= $plan['max_screens']>1?'s':'' ?>
                · <?= $plan['max_slides'] ?? 15 ?> slides per screen
              </div>
            </div>

            <!-- Monthly price -->
            <div class="plan-price-monthly flex flex-col items-end flex-shrink-0">
              <div class="flex items-baseline gap-0.5">
                <span class="text-xl font-bold text-gray-900">$<?= number_format($plan['price_monthly'], 0) ?></span>
                <span class="text-xs text-gray-400 ml-0.5">USD/mo</span>
              </div>
            </div>

            <!-- Annual price (hidden by default) -->
            <div class="plan-price-annual hidden flex flex-col items-end flex-shrink-0">
              <?php if ($plan['price_annual'] > 0): ?>
              <div class="flex items-baseline gap-0.5">
                <span class="text-xl font-bold text-gray-900">$<?= number_format($annualPerMonth, 0) ?></span>
                <span class="text-xs text-gray-400 ml-0.5">USD/mo</span>
              </div>
              <div class="text-xs text-green-600 font-medium">$<?= number_format($plan['price_annual'], 0) ?>/yr total</div>
              <?php else: ?>
              <div class="text-xs text-gray-400">No annual price set</div>
              <?php endif; ?>
            </div>

          </label>
          <?php endforeach; ?>
        </div>

        <!-- Coupon code -->
        <div class="mb-4">
          <label class="block text-xs font-medium text-gray-500 mb-1">Have a coupon code?</label>
          <div class="flex gap-2">
            <input id="billing-coupon-input" name="coupon_code" type="text"
              placeholder="Enter coupon code"
              class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono tracking-wider focus:outline-none focus:ring-2 focus:ring-indigo-400 uppercase"
              oninput="this.value = this.value.toUpperCase(); billingCouponReset()">
            <button type="button" onclick="billingValidateCoupon()"
              class="bg-gray-100 hover:bg-indigo-600 hover:text-white text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap">
              Apply
            </button>
          </div>
          <p id="billing-coupon-msg" class="text-xs mt-1 hidden"></p>
          <input type="hidden" id="billing-coupon-valid" name="coupon_valid" value="0">
        </div>

        <!-- Annual note -->
        <div id="annual-note" class="hidden bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-xs text-green-700 mb-4 flex items-start gap-2">
          <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <span>You're choosing an <strong>annual plan</strong> — a single payment for the full year, billed today via PayPal.</span>
        </div>

        <!-- Subscribe button -->
        <button type="submit" id="continue-btn" disabled
          class="w-full flex items-center justify-center gap-2 bg-[#0070ba] hover:bg-[#005ea6] text-white font-semibold py-3 rounded-xl text-sm transition-colors
                 disabled:opacity-40 disabled:cursor-not-allowed">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H4.25a.641.641 0 0 1-.632-.712l1.562-9.888a.641.641 0 0 1 .632-.566h3.584c1.64 0 2.944.444 3.673 1.285.687.8.876 1.903.565 3.108-.603 2.366-2.445 3.773-5.558 3.773h-.73L7.076 21.337zm4.84-8.93c-.248.97-.96 1.59-2.2 1.59H8.6l.488-3.1h1.116c1.094 0 1.712.38 1.712 1.51zM19.97 12.407H17.144a.641.641 0 0 0-.632.566l-1.562 9.888a.641.641 0 0 0 .632.712h2.826a.641.641 0 0 0 .632-.712l1.562-9.888a.641.641 0 0 0-.632-.566z"/></svg>
          <span id="continue-btn-label">Subscribe with PayPal</span>
        </button>
        <p id="continue-hint" class="text-xs text-center text-gray-400 mt-2">Select a plan above to continue.</p>
      </form>
    </div>
    <?php endif; ?>

    <!-- Payment history -->
    <?php if (!empty($payments)): ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h2 class="font-semibold text-gray-900 mb-5">Payment History</h2>
      <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[420px]">
          <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-50">
              <th class="pb-3 text-left font-medium">Date</th>
              <th class="pb-3 text-left font-medium hidden sm:table-cell">Description</th>
              <th class="pb-3 text-right font-medium">Amount</th>
              <th class="pb-3 text-right font-medium">Status</th>
              <th class="pb-3 text-right font-medium hidden sm:table-cell">Invoice</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach ($payments as $p): ?>
            <tr>
              <td class="py-3 text-gray-600"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              <td class="py-3 text-gray-600 hidden sm:table-cell"><?= Helpers::e($p['description'] ?? 'Subscription') ?></td>
              <td class="py-3 text-right font-semibold text-gray-900">$<?= number_format($p['amount'],2) ?> <?= $p['currency'] ?></td>
              <td class="py-3 text-right">
                <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full <?= $p['status']==='succeeded'?'bg-green-50 text-green-700':'bg-red-50 text-red-700' ?>">
                  <?= ucfirst($p['status']) ?>
                </span>
              </td>
              <td class="py-3 text-right hidden sm:table-cell">
                <?php if ($p['invoice_url']): ?>
                <a href="<?= Helpers::e($p['invoice_url']) ?>" target="_blank" class="text-primary-600 hover:text-primary-700 text-xs font-medium">View</a>
                <?php else: ?>
                <span class="text-gray-300 text-xs">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Plan selection + billing cycle JS -->
  <script>
  (function() {
    const cards       = document.querySelectorAll('.plan-card');
    const planIdInput = document.getElementById('selected-plan-id');
    const cycleInput  = document.getElementById('billing-cycle-input');
    const btn         = document.getElementById('continue-btn');
    const btnLabel    = document.getElementById('continue-btn-label');
    const hint        = document.getElementById('continue-hint');
    const annualNote  = document.getElementById('annual-note');
    if (!cards.length) return;

    let currentCycle    = 'monthly';
    let selectedCard    = null;

    // ── Billing cycle toggle ──────────────────────────────
    window.setCycle = function(cycle) {
      currentCycle = cycle;
      cycleInput.value = cycle;
      const isAnnual = cycle === 'annual';

      // Toggle price blocks
      document.querySelectorAll('.plan-price-monthly').forEach(el => el.classList.toggle('hidden', isAnnual));
      document.querySelectorAll('.plan-price-annual').forEach(el => el.classList.toggle('hidden', !isAnnual));
      document.querySelectorAll('.plan-badge-monthly').forEach(el => el.classList.toggle('hidden', isAnnual));
      document.querySelectorAll('.plan-badge-annual').forEach(el => el.classList.toggle('hidden', !isAnnual));

      // Toggle annual note
      if (annualNote) annualNote.classList.toggle('hidden', !isAnnual);

      // Toggle button styles
      var btnMonthly = document.getElementById('cycle-btn-monthly');
      var btnAnnual  = document.getElementById('cycle-btn-annual');
      if (isAnnual) {
        btnAnnual.classList.add('bg-white','text-gray-900','shadow-sm');
        btnAnnual.classList.remove('text-gray-500');
        btnMonthly.classList.remove('bg-white','text-gray-900','shadow-sm');
        btnMonthly.classList.add('text-gray-500');
      } else {
        btnMonthly.classList.add('bg-white','text-gray-900','shadow-sm');
        btnMonthly.classList.remove('text-gray-500');
        btnAnnual.classList.remove('bg-white','text-gray-900','shadow-sm');
        btnAnnual.classList.add('text-gray-500');
      }

      // Update button label
      if (btnLabel) btnLabel.textContent = isAnnual ? 'Pay Annually with PayPal' : 'Subscribe with PayPal';

      // Re-evaluate button state for selected card
      if (selectedCard) evaluateCard(selectedCard);
    };

    // ── Evaluate whether the selected card+cycle has PayPal configured ──
    function evaluateCard(card) {
      const hasPaypal = currentCycle === 'annual'
        ? card.dataset.hasAnnual === '1'
        : card.dataset.hasMonthly === '1';
      if (hasPaypal) {
        btn.disabled = false;
        hint.textContent = currentCycle === 'annual'
          ? 'One payment for the full year — you\'ll be redirected to PayPal.'
          : 'You\'ll be redirected to PayPal to complete payment.';
      } else {
        btn.disabled = true;
        const which = currentCycle === 'annual' ? 'Annual' : 'Monthly';
        hint.textContent = which + ' PayPal Plan ID not configured for this plan. Contact admin.';
      }
    }

    // ── Plan card click ───────────────────────────────────
    cards.forEach(card => {
      card.addEventListener('click', () => {
        const id = card.dataset.planId;

        // Reset all cards
        cards.forEach(c => {
          c.classList.remove('border-primary-400','bg-primary-50');
          c.classList.add('border-gray-100');
          document.getElementById('radio-' + c.dataset.planId)?.classList.replace('border-primary-500','border-gray-300');
          document.getElementById('radio-' + c.dataset.planId)?.classList.remove('bg-primary-500');
          document.getElementById('radio-dot-' + c.dataset.planId)?.classList.add('hidden');
        });

        // Highlight selected
        card.classList.add('border-primary-400','bg-primary-50');
        card.classList.remove('border-gray-100');
        const radio = document.getElementById('radio-' + id);
        if (radio) { radio.classList.replace('border-gray-300','border-primary-500'); radio.classList.add('bg-primary-500'); }
        const dot = document.getElementById('radio-dot-' + id);
        if (dot) dot.classList.remove('hidden');

        planIdInput.value = id;
        selectedCard = card;
        evaluateCard(card);
      });
    });
  })();

  // ── Coupon AJAX validation (billing page) ──────────────────────────────
  function billingCouponReset() {
    var msg = document.getElementById('billing-coupon-msg');
    msg.classList.add('hidden');
    document.getElementById('billing-coupon-valid').value = '0';
  }

  function billingGetPlanSlug() {
    var card = document.querySelector('.plan-card.border-primary-400');
    return card ? (card.dataset.planSlug || '') : '';
  }

  function billingValidateCoupon() {
    var code = document.getElementById('billing-coupon-input').value.trim();
    var msg  = document.getElementById('billing-coupon-msg');
    if (!code) {
      msg.textContent = 'Please enter a coupon code.';
      msg.className = 'text-xs mt-1 text-red-500';
      return;
    }
    msg.textContent = 'Checking…';
    msg.className = 'text-xs mt-1 text-gray-400';
    msg.classList.remove('hidden');

    var data = new URLSearchParams({ code: code, plan_slug: billingGetPlanSlug() });
    fetch('/coupon/validate', { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.valid) {
          msg.textContent = '✓ ' + res.message;
          msg.className = 'text-xs mt-1 text-green-600 font-medium';
          document.getElementById('billing-coupon-valid').value = '1';
        } else {
          msg.textContent = res.message;
          msg.className = 'text-xs mt-1 text-red-500';
          document.getElementById('billing-coupon-valid').value = '0';
        }
      })
      .catch(function() {
        msg.textContent = 'Could not check coupon. Please try again.';
        msg.className = 'text-xs mt-1 text-red-500';
      });
  }

  var billingCouponEl = document.getElementById('billing-coupon-input');
  if (billingCouponEl) {
    billingCouponEl.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') { e.preventDefault(); billingValidateCoupon(); }
    });
  }
  </script>

  <!-- Right: Summary -->
  <div class="space-y-5">
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white">
      <div class="text-sm font-medium text-indigo-200 mb-1">All plans include</div>
      <ul class="space-y-2.5 mt-4">
        <?php foreach (['Unlimited Uploads','Auto Refresh Every 15 Min','Cloud Management','Secure Hosting','Software Updates','Email Support'] as $f): ?>
        <li class="flex items-center gap-2 text-sm">
          <svg class="w-4 h-4 text-indigo-200 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
          <?= $f ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
      <div class="font-semibold text-gray-900 mb-2 text-sm">Need help?</div>
      <p class="text-xs text-gray-400 mb-4">Questions about your subscription or billing? We're here to help.</p>
      <a href="/support/create" class="block text-center border border-gray-200 hover:border-primary-300 text-gray-600 text-sm font-medium py-2.5 rounded-xl transition-colors">Open Support Ticket</a>
    </div>
  </div>
</div>
