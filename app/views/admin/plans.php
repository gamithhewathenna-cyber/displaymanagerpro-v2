<?php // plans.php ?>
<div class="space-y-4">
  <?php foreach ($plans as $p): ?>
  <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center justify-between gap-4">
    <div>
      <div class="font-semibold text-gray-900"><?= Helpers::e($p['name']) ?> <span class="text-xs font-normal text-gray-400 ml-2"><?= $p['is_active']?'Active':'Inactive' ?></span></div>
      <div class="text-sm text-gray-400 mt-1">$<?= number_format($p['price_monthly'],2) ?>/mo · Up to <?= $p['max_screens'] ?> screen<?= $p['max_screens']>1?'s':'' ?></div>
      <div class="text-xs text-gray-400 mt-0.5">Stripe monthly ID: <span class="font-mono"><?= Helpers::e($p['stripe_price_id_monthly'] ?: '(not set)') ?></span></div>
    </div>
    <a href="/admin/plans/<?= $p['id'] ?>/edit" class="border border-gray-200 hover:border-primary-300 text-gray-600 text-xs font-medium px-3 py-2 rounded-lg transition-colors flex-shrink-0">Edit Plan</a>
  </div>
  <?php endforeach; ?>
</div>
