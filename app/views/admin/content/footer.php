<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>
<form method="POST" action="/admin/content/footer/save" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Footer General</h2>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Tagline (below logo)</label>
        <input type="text" name="tagline" value="<?= htmlspecialchars($content['tagline']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Copyright Text</label>
        <input type="text" name="copyright" value="<?= htmlspecialchars($content['copyright']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>
  <?php for ($col = 1; $col <= 3; $col++): ?>
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-4">Column <?= $col ?></h2>
    <div class="mb-4"><label class="block text-xs font-medium text-gray-500 mb-1">Column Title</label>
      <input type="text" name="col<?= $col ?>_title" value="<?= htmlspecialchars($content["col{$col}_title"]) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Links (one per line: Label|/url)</label>
      <textarea name="col<?= $col ?>_links" rows="5" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content["col{$col}_links"]) ?></textarea>
      <p class="text-xs text-gray-400 mt-1">Format: Link Text|/url — one per line</p>
    </div>
  </div>
  <?php endfor; ?>
  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Footer Content</button>
</form>
