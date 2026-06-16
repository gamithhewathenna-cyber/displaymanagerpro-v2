<?php // plans.php ?>
<div class="flex items-center justify-between mb-5">
  <h2 class="font-semibold text-gray-900">Subscription Plans</h2>
  <a href="/admin/plans/create" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-colors">+ New Plan</a>
</div>
<div class="space-y-4">
  <?php foreach ($plans as $p): ?>
  <div class="bg-white rounded-xl border border-gray-100 p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <div class="font-semibold text-gray-900"><?= Helpers::e($p['name']) ?> <span class="text-xs font-normal text-gray-400 ml-2"><?= $p['is_active']?'Active':'Inactive' ?></span></div>
      <div class="text-sm text-gray-400 mt-1">$<?= number_format($p['price_monthly'],2) ?>/mo · Up to <?= $p['max_screens'] ?> screen<?= $p['max_screens']>1?'s':'' ?> · <?= $p['max_slides'] ?? 15 ?> slides</div>
      <div class="text-xs text-gray-400 mt-0.5">PayPal Plan ID: <span class="font-mono"><?= Helpers::e($p['stripe_price_id_monthly'] ?: '(not set)') ?></span></div>
    </div>
    <div class="flex items-center gap-2">
      <a href="/admin/plans/<?= $p['id'] ?>/edit" class="border border-gray-200 hover:border-primary-300 text-gray-600 text-xs font-medium px-3 py-2 rounded-lg transition-colors flex-shrink-0">Edit</a>
      <form method="POST" action="/admin/plans/<?= $p['id'] ?>/delete" onsubmit="return confirm('Delete this plan? Active subscribers will not be affected.')">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit" class="border border-red-200 hover:bg-red-50 text-red-500 text-xs font-medium px-3 py-2 rounded-lg transition-colors">Delete</button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($plans)): ?>
  <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400 text-sm">
    No plans yet. <a href="/admin/plans/create" class="text-primary-600 font-medium">Create your first plan →</a>
  </div>
  <?php endif; ?>
</div>
