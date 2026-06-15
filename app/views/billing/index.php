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
        <?php if ($sub['stripe_subscription_id']): ?>
        <a href="/billing/portal" class="border border-gray-200 hover:border-gray-300 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl transition-colors flex-shrink-0">
          Manage Billing →
        </a>
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

      <?php if ($sub['stripe_subscription_id'] && ($sub['status'] === 'active' || $sub['status'] === 'trialing')): ?>
      <div class="mt-5 pt-5 border-t border-gray-50">
        <form method="POST" action="/billing/cancel" onsubmit="return confirm('Cancel your subscription? You\'ll keep access until the end of your billing period.')">
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
    <?php if (!$sub || !$sub['stripe_subscription_id'] || $isTrialing): ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h2 class="font-semibold text-gray-900 mb-5"><?= $isTrialing ? 'Activate your subscription' : 'Choose a plan' ?></h2>
      <div class="space-y-3">
        <?php foreach ($plans as $plan): ?>
        <?php $isCurrent = ($sub['plan_id'] ?? 0) == $plan['id']; ?>
        <div class="border-2 <?= $isCurrent ? 'border-primary-400 bg-primary-50' : 'border-gray-100 hover:border-primary-200' ?> rounded-xl p-5 transition-all">
          <div class="flex items-center justify-between gap-4">
            <div>
              <div class="flex items-center gap-2">
                <div class="font-semibold text-gray-900"><?= Helpers::e($plan['name']) ?></div>
                <?php if ($isCurrent): ?>
                <span class="bg-primary-100 text-primary-700 text-xs font-semibold px-2 py-0.5 rounded-full">Current</span>
                <?php endif; ?>
              </div>
              <div class="text-sm text-gray-400 mt-0.5">Up to <?= $plan['max_screens'] ?> screen<?= $plan['max_screens']>1?'s':'' ?></div>
            </div>
            <div class="flex items-baseline gap-1 flex-shrink-0">
              <span class="text-2xl font-bold text-gray-900">$<?= number_format($plan['price_monthly'],0) ?></span>
              <span class="text-sm text-gray-400">AUD/mo</span>
            </div>
          </div>
          <?php if (!$isCurrent && $plan['stripe_price_id_monthly']): ?>
          <form method="POST" action="/billing/checkout" class="mt-4">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
            <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
            <input type="hidden" name="billing_cycle" value="monthly">
            <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
              <?= $isTrialing ? 'Activate' : 'Switch to' ?> <?= Helpers::e($plan['name']) ?>
            </button>
          </form>
          <?php elseif (!$plan['stripe_price_id_monthly']): ?>
          <div class="mt-4 text-xs text-center text-gray-400">Stripe price not configured – contact admin</div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
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
