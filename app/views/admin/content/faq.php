<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>
<form method="POST" action="/admin/content/faq/save" class="space-y-6">
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
    <h2 class="font-semibold text-gray-900 mb-5">Questions & Answers</h2>
    <div class="space-y-4">
      <?php for ($i = 1; $i <= 10; $i++): ?>
      <div class="bg-gray-50 rounded-xl p-4">
        <label class="block text-xs font-bold text-primary-600 mb-1">Q<?= $i ?></label>
        <input type="text" name="q<?= $i ?>" value="<?= htmlspecialchars($content["q{$i}"]) ?>" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium mb-2 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Question">
        <textarea name="a<?= $i ?>" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Answer"><?= htmlspecialchars($content["a{$i}"]) ?></textarea>
      </div>
      <?php endfor; ?>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-4">"Still have questions?" Box</h2>
    <div class="grid grid-cols-3 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="still_title" value="<?= htmlspecialchars($content['still_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
        <input type="text" name="still_subtitle" value="<?= htmlspecialchars($content['still_subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Button Text</label>
        <input type="text" name="still_button" value="<?= htmlspecialchars($content['still_button']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>
  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save FAQ Content</button>
</form>
