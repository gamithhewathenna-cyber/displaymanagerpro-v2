<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>
<form method="POST" action="/admin/content/features/save" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Page Header</h2>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($content['title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
        <input type="text" name="subtitle" value="<?= htmlspecialchars($content['subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Feature Cards</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <?php for ($i = 1; $i <= 12; $i++): ?>
      <div class="bg-gray-50 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-xs font-bold text-gray-400 w-6">#<?= $i ?></span>
          <input type="text" name="f<?= $i ?>_icon" value="<?= htmlspecialchars($content["f{$i}_icon"]) ?>" class="w-14 border border-gray-200 rounded-lg px-2 py-1.5 text-center text-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="emoji">
          <input type="text" name="f<?= $i ?>_title" value="<?= htmlspecialchars($content["f{$i}_title"]) ?>" class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Feature title">
        </div>
        <textarea name="f<?= $i ?>_desc" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Description"><?= htmlspecialchars($content["f{$i}_desc"]) ?></textarea>
      </div>
      <?php endfor; ?>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Bottom CTA</h2>
    <div class="grid grid-cols-3 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="cta_title" value="<?= htmlspecialchars($content['cta_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
        <input type="text" name="cta_subtitle" value="<?= htmlspecialchars($content['cta_subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Button Text</label>
        <input type="text" name="cta_button" value="<?= htmlspecialchars($content['cta_button']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>
  <?php require VIEWS_PATH . '/admin/content/_seo_fields.php'; ?>

  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Features Content</button>
</form>
