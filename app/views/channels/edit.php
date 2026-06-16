<div class="max-w-2xl">
  <a href="/channels/<?= $channel['id'] ?>" class="text-sm text-gray-400 hover:text-gray-600 mb-6 inline-flex items-center gap-1">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to channel
  </a>

  <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-7 mt-3">
    <h2 class="text-lg font-bold text-gray-900 mb-6">Channel Settings</h2>
    <form method="POST" action="/channels/<?= $channel['id'] ?>" class="space-y-6">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Channel name</label>
          <input type="text" name="name" required value="<?= Helpers::e($channel['name']) ?>"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
          <input type="text" name="description" value="<?= Helpers::e($channel['description']) ?>"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Orientation</label>
          <select name="orientation" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="landscape" <?= $channel['orientation']==='landscape'?'selected':'' ?>>Landscape (16:9)</option>
            <option value="portrait" <?= $channel['orientation']==='portrait'?'selected':'' ?>>Portrait (9:16)</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Slide duration (seconds)</label>
          <input type="number" name="slide_duration" value="<?= $channel['slide_duration'] ?>" min="3" max="60"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Transition effect</label>
          <select name="transition" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <?php foreach (['fade','slide-left','slide-right','zoom','crossfade'] as $fx): ?>
            <option value="<?= $fx ?>" <?= $channel['transition_effect']===$fx?'selected':'' ?>><?= ucwords(str_replace('-',' ',$fx)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Auto refresh</label>
          <select name="auto_refresh" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <?php foreach ([5,15,30,60] as $m): ?>
            <option value="<?= $m ?>" <?= $channel['auto_refresh_minutes']==$m?'selected':'' ?>><?= $m ?> minutes</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Background colour</label>
          <input type="color" name="bg_color" value="<?= Helpers::e($channel['background_color']) ?>" class="w-12 h-11 border border-gray-200 rounded-xl cursor-pointer p-1">
        </div>
        <div class="sm:col-span-2">
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" <?= $channel['is_active']?'checked':'' ?> class="w-5 h-5 rounded text-primary-500 focus:ring-primary-500">
            <div>
              <div class="text-sm font-medium text-gray-700">Channel is active</div>
              <div class="text-xs text-gray-400">Uncheck to pause this screen without deleting it</div>
            </div>
          </label>
        </div>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Save Settings</button>
        <a href="/channels/<?= $channel['id'] ?>" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Cancel</a>
      </div>
    </form>
  </div>
</div>
