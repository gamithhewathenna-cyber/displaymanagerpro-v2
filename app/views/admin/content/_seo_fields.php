  <!-- SEO -->
  <div class="bg-white rounded-2xl border border-primary-200 p-6">
    <h2 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">
      <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      SEO Settings
    </h2>
    <p class="text-xs text-gray-400 mb-4">These fields control how this page appears in Google and other search engines.</p>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">SEO Title <span class="text-gray-400 font-normal">(overrides browser tab title in search results)</span></label>
        <input type="text" name="seo_title" maxlength="70"
          value="<?= htmlspecialchars($content['seo_title'] ?? '') ?>"
          placeholder="e.g. Digital Signage Software for Restaurants | <?= Helpers::e(Settings::get('company_name', 'DisplayNex')) ?>"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-gray-400 mt-1">Recommended: 50–70 characters</p>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Meta Description <span class="text-gray-400 font-normal">(shown below the title in search results)</span></label>
        <textarea name="seo_description" rows="2" maxlength="160"
          placeholder="e.g. Manage all your TV screens from one cloud dashboard. 14-day free trial, no credit card required."
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['seo_description'] ?? '') ?></textarea>
        <p class="text-xs text-gray-400 mt-1">Recommended: 120–160 characters</p>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Focus Keyphrase <span class="text-gray-400 font-normal">(primary keyword this page targets)</span></label>
        <input type="text" name="seo_keyphrase"
          value="<?= htmlspecialchars($content['seo_keyphrase'] ?? '') ?>"
          placeholder="e.g. digital signage software for restaurants"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-gray-400 mt-1">One phrase (2–5 words) you want this page to rank for</p>
      </div>
    </div>
  </div>
