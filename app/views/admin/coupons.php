<?php
/* Admin – Coupon Codes list */
?>
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-900">Coupon Codes</h1>
    <p class="text-sm text-gray-500 mt-0.5">Create and manage discount coupons for your plans.</p>
  </div>
  <a href="/admin/coupons/create"
     class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    New Coupon
  </a>
</div>

<?php if (empty($coupons)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
  <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
  </div>
  <h3 class="font-semibold text-gray-900 mb-1">No coupons yet</h3>
  <p class="text-sm text-gray-500 mb-5">Create your first discount coupon to share on social media or in email campaigns.</p>
  <a href="/admin/coupons/create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">Create Coupon</a>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
  <table class="w-full text-sm">
    <thead>
      <tr class="border-b border-gray-100">
        <th class="text-left font-semibold text-gray-500 px-5 py-3">Code</th>
        <th class="text-left font-semibold text-gray-500 px-4 py-3">Discount</th>
        <th class="text-left font-semibold text-gray-500 px-4 py-3">Applies to</th>
        <th class="text-left font-semibold text-gray-500 px-4 py-3">Uses</th>
        <th class="text-left font-semibold text-gray-500 px-4 py-3">Expires</th>
        <th class="text-left font-semibold text-gray-500 px-4 py-3">Status</th>
        <th class="px-4 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($coupons as $c): ?>
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-5 py-3 font-mono font-semibold text-indigo-700 tracking-wider"><?= Helpers::e($c['code']) ?></td>
        <td class="px-4 py-3 font-semibold text-gray-900">
          <?php if ($c['discount_type'] === 'percentage'): ?>
            <?= (int)$c['discount_value'] ?>% off
          <?php else: ?>
            $<?= number_format((float)$c['discount_value'], 2) ?> off
          <?php endif; ?>
        </td>
        <td class="px-4 py-3 text-gray-600">
          <?= $c['applies_to'] === 'all' ? '<span class="text-gray-400">All plans</span>' : Helpers::e($c['applies_to']) ?>
        </td>
        <td class="px-4 py-3 text-gray-600">
          <?= (int)$c['used_count'] ?>
          <?php if ($c['max_uses'] !== null): ?>
            <span class="text-gray-400">/ <?= (int)$c['max_uses'] ?></span>
          <?php endif; ?>
        </td>
        <td class="px-4 py-3 text-gray-600">
          <?= $c['expires_at'] ? date('M j, Y', strtotime($c['expires_at'])) : '<span class="text-gray-400">Never</span>' ?>
        </td>
        <td class="px-4 py-3">
          <?php if ($c['is_active']): ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full bg-green-50 text-green-700">
              <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>Active
            </span>
          <?php else: ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
              <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive
            </span>
          <?php endif; ?>
        </td>
        <td class="px-4 py-3">
          <div class="flex items-center gap-2 justify-end">
            <a href="/admin/coupons/<?= $c['id'] ?>/edit"
               class="text-gray-400 hover:text-indigo-600 transition-colors" title="Edit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            <form method="POST" action="/admin/coupons/<?= $c['id'] ?>/toggle" class="inline">
              <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
              <button type="submit" class="text-gray-400 hover:text-yellow-600 transition-colors" title="<?= $c['is_active'] ? 'Deactivate' : 'Activate' ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
              </button>
            </form>
            <form method="POST" action="/admin/coupons/<?= $c['id'] ?>/delete" class="inline"
                  onsubmit="return confirm('Delete coupon <?= Helpers::e($c['code']) ?>?')">
              <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
              <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
