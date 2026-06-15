<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>
<form method="POST" action="/admin/content/contact/save" class="space-y-6">
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
    <h2 class="font-semibold text-gray-900 mb-5">Contact Details</h2>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Email Label</label>
        <input type="text" name="email_label" value="<?= htmlspecialchars($content['email_label']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Email Description</label>
        <input type="text" name="email_desc" value="<?= htmlspecialchars($content['email_desc']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Hours Label</label>
        <input type="text" name="hours_label" value="<?= htmlspecialchars($content['hours_label']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Hours Value</label>
        <input type="text" name="hours_value" value="<?= htmlspecialchars($content['hours_value']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Form Title</label>
        <input type="text" name="form_title" value="<?= htmlspecialchars($content['form_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Side CTA Box</h2>
    <div class="grid grid-cols-3 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="cta_title" value="<?= htmlspecialchars($content['cta_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
        <input type="text" name="cta_desc" value="<?= htmlspecialchars($content['cta_desc']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Button Text</label>
        <input type="text" name="cta_button" value="<?= htmlspecialchars($content['cta_button']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>
  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Contact Content</button>
</form>
