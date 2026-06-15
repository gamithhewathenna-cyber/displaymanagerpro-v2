<?php
$isTrialing   = Subscription::isTrialing($sub);
$isActive     = Subscription::isActive($sub);
$trialDaysLeft= Subscription::trialDaysLeft($sub);
?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

  <!-- Left: Subscription details -->
  <div class="xl:col-span-2 space-y-5">

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
        <form method="POST" action="/billing/cancel" onsubmit="return confirm('Cancel your subscription?')">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Cancel subscription</button>
        </form>
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
      <p class="text-sm text-gray-400 mb-5">Select a plan below, then click <strong>Continue to Payment</strong>.</p>

      <form method="POST" action="/billing/subscribe" id="plan-form">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <input type="hidden" name="billing_cycle" value="monthly">
        <input type="hidden" name="plan_id" id="selected-plan-id" value="">

        <div class="space-y-3 mb-5" id="plan-cards">
          <?php foreach ($plans as $plan): ?>
          <?php $isCurrent = ($sub['plan_id'] ?? 0) == $plan['id']; ?>
          <label class="plan-card flex items-center gap-4 border-2 rounded-xl p-4 cursor-pointer transition-all
            <?= $isCurrent ? 'border-primary-400 bg-primary-50' : 'border-gray-100 hover:border-primary-200' ?>"
            data-plan-id="<?= $plan['id'] ?>"
            data-has-paypal="<?= $plan['stripe_price_id_monthly'] ? '1' : '0' ?>">

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
                <?php if (!$plan['stripe_price_id_monthly']): ?>
                <span class="bg-amber-50 text-amber-600 text-xs px-2 py-0.5 rounded-full">PayPal not configured</span>
                <?php endif; ?>
              </div>
              <div class="text-xs text-gray-400 mt-0.5">
                Up to <?= $plan['max_screens'] ?> screen<?= $plan['max_screens']>1?'s':'' ?>
                · <?= $plan['max_slides'] ?? 15 ?> slides per screen
              </div>
            </div>

            <div class="flex items-baseline gap-0.5 flex-shrink-0 text-right">
              <span class="text-xl font-bold text-gray-900">$<?= number_format($plan['price_monthly'],0) ?></span>
              <span class="text-xs text-gray-400 ml-0.5">USD/mo</span>
            </div>
          </label>
          <?php endforeach; ?>
        </div>

        <!-- Subscribe button -->
        <button type="submit" id="continue-btn" disabled
          class="w-full flex items-center justify-center gap-2 bg-[#0070ba] hover:bg-[#005ea6] text-white font-semibold py-3 rounded-xl text-sm transition-colors
                 disabled:opacity-40 disabled:cursor-not-allowed">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H4.25a.641.641 0 0 1-.632-.712l1.562-9.888a.641.641 0 0 1 .632-.566h3.584c1.64 0 2.944.444 3.673 1.285.687.8.876 1.903.565 3.108-.603 2.366-2.445 3.773-5.558 3.773h-.73L7.076 21.337zm4.84-8.93c-.248.97-.96 1.59-2.2 1.59H8.6l.488-3.1h1.116c1.094 0 1.712.38 1.712 1.51zM19.97 12.407H17.144a.641.641 0 0 0-.632.566l-1.562 9.888a.641.641 0 0 0 .632.712h2.826a.641.641 0 0 0 .632-.712l1.562-9.888a.641.641 0 0 0-.632-.566z"/></svg>
          Subscribe with PayPal
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
        <table class="w-full text-sm">
          <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-50">
              <th class="pb-3 text-left font-medium">Date</th>
              <th class="pb-3 text-left font-medium">Description</th>
              <th class="pb-3 text-right font-medium">Amount</th>
              <th class="pb-3 text-right font-medium">Status</th>
              <th class="pb-3 text-right font-medium">Invoice</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach ($payments as $p): ?>
            <tr>
              <td class="py-3 text-gray-600"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              <td class="py-3 text-gray-600"><?= Helpers::e($p['description'] ?? 'Subscription') ?></td>
              <td class="py-3 text-right font-semibold text-gray-900">$<?= number_format($p['amount'],2) ?> <?= $p['currency'] ?></td>
              <td class="py-3 text-right">
                <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full <?= $p['status']==='succeeded'?'bg-green-50 text-green-700':'bg-red-50 text-red-700' ?>">
                  <?= ucfirst($p['status']) ?>
                </span>
              </td>
              <td class="py-3 text-right">
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

  <!-- Plan selection JS -->
  <script>
  (function() {
    const cards  = document.querySelectorAll('.plan-card');
    const planId = document.getElementById('selected-plan-id');
    const btn    = document.getElementById('continue-btn');
    const hint   = document.getElementById('continue-hint');
    if (!cards.length) return;

    cards.forEach(card => {
      card.addEventListener('click', () => {
        const id        = card.dataset.planId;
        const hasStripe = card.dataset.hasPaypal === '1';

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

        planId.value = id;

        if (hasStripe) {
          btn.disabled = false;
          hint.textContent = 'You\'ll be redirected to PayPal to complete payment.';
        } else {
          btn.disabled = true;
          hint.textContent = 'PayPal Plan ID not configured for this plan. Contact the site admin.';
        }
      });
    });
  })();
  </script>

  <!-- Right: Summary -->
  <div class="space-y-5">
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white">
      <div class="text-sm font-medium text-indigo-200 mb-1">All plans include</div>
      <ul class="space-y-2.5 mt-4">
        <?php foreach (['14-Day Free Trial','Unlimited Uploads','Auto Refresh Every 15 Min','Cloud Management','Secure Hosting','Software Updates','Email Support'] as $f): ?>
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
