<div class="max-w-xl w-full">
  <a href="/admin/plans" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← All plans</a>
  <div class="bg-white rounded-xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Create New Plan</h2>
    <form method="POST" action="/admin/plans" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

      <div class="flex gap-3 pb-2 border-b border-gray-100">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Create Plan</button>
        <a href="/admin/plans" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Cancel</a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Plan Name</label>
          <input type="text" name="name" required placeholder="e.g. Enterprise" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Price Monthly (AUD)</label>
          <input type="number" name="price_monthly" step="0.01" value="0" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Price Annual (AUD)</label>
          <input type="number" name="price_annual" step="0.01" value="0" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Max Screens</label>
          <input type="number" name="max_screens" value="1" min="1" max="100" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Max Slides per Screen</label>
          <input type="number" name="max_slides" value="15" min="1" max="50" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <p class="text-xs text-gray-400 mt-1">Max images per TV channel (1–50)</p></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Storage Limit (MB)</label>
          <input type="number" name="max_storage_mb" value="512" min="100" step="1" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <p class="text-xs text-gray-400 mt-1">e.g. 512 = 500 MB, 1024 = 1 GB, 2048 = 2 GB</p></div>
        <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">PayPal Plan ID (Monthly)</label>
          <input type="text" name="stripe_price_id_monthly" placeholder="P-XXXXXXXXXXXXXXXXXXXX" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">PayPal Plan ID (Annual)</label>
          <input type="text" name="stripe_price_id_annual" placeholder="P-XXXXXXXXXXXXXXXXXXXX" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="sm:col-span-2"><label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="has_trial" value="1" checked class="rounded text-primary-500">
          <span class="text-sm font-medium text-gray-700">Offer free trial for this plan</span>
        </label>
        <p class="text-xs text-gray-400 mt-1 ml-6">When checked, this plan shows a "14-day free trial" badge and delays the first charge. Uncheck for plans that charge immediately (or are free, like Starter).</p></div>
        <div class="sm:col-span-2"><label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary-500">
          <span class="text-sm font-medium text-gray-700">Plan is active (shown to customers)</span>
        </label></div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Create Plan</button>
        <a href="/admin/plans" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Cancel</a>
      </div>
    </form>
  </div>
</div>
