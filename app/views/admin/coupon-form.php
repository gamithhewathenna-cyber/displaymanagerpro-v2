<?php
/* Admin – Create / Edit Coupon */
$isEdit  = !empty($coupon);
$postUrl = $isEdit ? "/admin/coupons/{$coupon['id']}" : '/admin/coupons';

// Pre-selected plan slugs
$appliesRaw  = $coupon['applies_to'] ?? 'all';
$isAllPlans  = ($appliesRaw === 'all');
$selectedSlugs = $isAllPlans ? [] : array_map('trim', explode(',', $appliesRaw));

// Format expires_at for datetime-local input
$expiresVal = '';
if (!empty($coupon['expires_at'])) {
    $expiresVal = date('Y-m-d\TH:i', strtotime($coupon['expires_at']));
}
?>

<div class="max-w-2xl">
  <div class="flex items-center gap-3 mb-6">
    <a href="/admin/coupons" class="text-gray-400 hover:text-gray-600 transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <h1 class="text-xl font-bold text-gray-900"><?= $isEdit ? 'Edit Coupon' : 'New Coupon' ?></h1>
  </div>

  <form method="POST" action="<?= $postUrl ?>" class="space-y-5">
    <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

    <!-- Code -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
      <h2 class="font-semibold text-gray-900">Coupon Code</h2>

      <?php if ($isEdit): ?>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Code</label>
        <div class="font-mono font-bold text-xl text-indigo-700 tracking-widest px-4 py-3 bg-indigo-50 rounded-xl"><?= Helpers::e($coupon['code']) ?></div>
      </div>
      <?php else: ?>
      <div>
        <label for="code" class="block text-xs font-medium text-gray-500 mb-1">Code <span class="text-red-400">*</span></label>
        <input id="code" name="code" type="text" required
          value="<?= Helpers::e($_POST['code'] ?? '') ?>"
          placeholder="e.g. LAUNCH50"
          pattern="[A-Za-z0-9\-_]+"
          title="Letters, numbers, hyphens and underscores only"
          oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9\-_]/g,'')"
          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <p class="text-xs text-gray-400 mt-1">Letters, numbers, hyphens and underscores only. Will be saved uppercase.</p>
      </div>
      <?php endif; ?>

      <div>
        <label for="description" class="block text-xs font-medium text-gray-500 mb-1">Description (internal note)</label>
        <input id="description" name="description" type="text"
          value="<?= Helpers::e($coupon['description'] ?? $_POST['description'] ?? '') ?>"
          placeholder="e.g. Launch promotion — 50% off first 10 customers"
          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
      </div>
    </div>

    <!-- Discount -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
      <h2 class="font-semibold text-gray-900">Discount</h2>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Type <span class="text-red-400">*</span></label>
          <select name="discount_type" id="discount_type" required
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
            onchange="updateDiscountLabel()">
            <option value="percentage" <?= ($coupon['discount_type'] ?? 'percentage') === 'percentage' ? 'selected' : '' ?>>Percentage (%)</option>
            <option value="fixed"      <?= ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed amount ($)</option>
          </select>
        </div>
        <div>
          <label for="discount_value" class="block text-xs font-medium text-gray-500 mb-1">
            Value <span class="text-red-400">*</span>
            <span id="discount_unit" class="font-normal text-gray-400">(e.g. 50 for 50%)</span>
          </label>
          <input id="discount_value" name="discount_value" type="number" min="0.01" step="0.01" required
            value="<?= Helpers::e($coupon['discount_value'] ?? $_POST['discount_value'] ?? '') ?>"
            placeholder="50"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
      </div>
    </div>

    <!-- Plans -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-3">
      <h2 class="font-semibold text-gray-900">Applies To</h2>
      <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio" name="applies_to[]" value="all" id="applies_all"
          <?= $isAllPlans ? 'checked' : '' ?>
          class="text-indigo-600 focus:ring-indigo-400"
          onchange="togglePlanSelect()">
        <span class="text-sm text-gray-700">All plans</span>
      </label>
      <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio" name="_applies_mode" id="applies_specific"
          <?= !$isAllPlans ? 'checked' : '' ?>
          class="text-indigo-600 focus:ring-indigo-400"
          onchange="togglePlanSelect()">
        <span class="text-sm text-gray-700">Specific plans</span>
      </label>

      <div id="plan-select" class="<?= $isAllPlans ? 'hidden' : '' ?> pl-6 space-y-2">
        <?php foreach ($plans as $plan): ?>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="applies_to[]" value="<?= Helpers::e($plan['slug']) ?>"
            <?= in_array($plan['slug'], $selectedSlugs) ? 'checked' : '' ?>
            class="text-indigo-600 focus:ring-indigo-400">
          <span class="text-sm text-gray-700"><?= Helpers::e($plan['name']) ?></span>
        </label>
        <?php endforeach; ?>
        <?php if (empty($plans)): ?>
        <p class="text-xs text-gray-400">No active plans found.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Limits -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
      <h2 class="font-semibold text-gray-900">Limits &amp; Expiry</h2>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="max_uses" class="block text-xs font-medium text-gray-500 mb-1">Max total uses</label>
          <input id="max_uses" name="max_uses" type="number" min="1" step="1"
            value="<?= Helpers::e($coupon['max_uses'] ?? $_POST['max_uses'] ?? '') ?>"
            placeholder="Leave blank for unlimited"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
          <p class="text-xs text-gray-400 mt-1">Leave blank for unlimited uses.</p>
        </div>
        <div>
          <label for="expires_at" class="block text-xs font-medium text-gray-500 mb-1">Expiry date &amp; time</label>
          <input id="expires_at" name="expires_at" type="datetime-local"
            value="<?= Helpers::e($expiresVal) ?>"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
          <p class="text-xs text-gray-400 mt-1">Leave blank for no expiry.</p>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-1">
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <input type="checkbox" name="one_per_customer" value="1"
            <?= ($coupon['one_per_customer'] ?? 1) ? 'checked' : '' ?>
            class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-400">
          <span class="text-sm text-gray-700">One use per customer (per email address)</span>
        </label>
      </div>
    </div>

    <!-- Status -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
      <label class="flex items-center gap-3 cursor-pointer select-none">
        <input type="checkbox" name="is_active" value="1"
          <?= ($coupon['is_active'] ?? 1) ? 'checked' : '' ?>
          class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-400">
        <div>
          <div class="text-sm font-medium text-gray-800">Active</div>
          <div class="text-xs text-gray-400">Only active coupons can be redeemed.</div>
        </div>
      </label>
    </div>

    <div class="flex gap-3 pt-1">
      <button type="submit"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors">
        <?= $isEdit ? 'Save Changes' : 'Create Coupon' ?>
      </button>
      <a href="/admin/coupons"
         class="text-sm font-medium text-gray-500 hover:text-gray-700 px-4 py-2.5 transition-colors">Cancel</a>
    </div>
  </form>
</div>

<script>
function updateDiscountLabel() {
  var type = document.getElementById('discount_type').value;
  document.getElementById('discount_unit').textContent =
    type === 'percentage' ? '(e.g. 50 for 50%)' : '(e.g. 9.99 for $9.99)';
}
function togglePlanSelect() {
  var allChecked = document.getElementById('applies_all').checked;
  document.getElementById('plan-select').classList.toggle('hidden', allChecked);
}
updateDiscountLabel();
</script>
