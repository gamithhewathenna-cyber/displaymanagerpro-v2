<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>

<form method="POST" action="/admin/content/home/save" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

  <!-- Hero -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">🦸 Hero Section</h2>
    <div class="space-y-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Badge Text</label>
        <input type="text" name="badge_text" value="<?= htmlspecialchars($content['badge_text']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Headline Line 1</label>
          <input type="text" name="hero_title_1" value="<?= htmlspecialchars($content['hero_title_1']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Headline Line 2 (gradient)</label>
          <input type="text" name="hero_title_2" value="<?= htmlspecialchars($content['hero_title_2']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
        <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($content['hero_subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
        <textarea name="hero_description" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['hero_description']) ?></textarea></div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Primary Button Text</label>
          <input type="text" name="cta_primary" value="<?= htmlspecialchars($content['cta_primary']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Secondary Button Text</label>
          <input type="text" name="cta_secondary" value="<?= htmlspecialchars($content['cta_secondary']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">📊 Stats Bar</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="bg-gray-50 rounded-xl p-3">
        <label class="block text-xs font-medium text-gray-500 mb-1">Stat <?= $i ?> Number</label>
        <input type="text" name="stat_<?= $i ?>_num" value="<?= htmlspecialchars($content["stat_{$i}_num"]) ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 mb-2">
        <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
        <input type="text" name="stat_<?= $i ?>_label" value="<?= htmlspecialchars($content["stat_{$i}_label"]) ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Features section title -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">⚡ Features Section</h2>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
        <input type="text" name="features_title" value="<?= htmlspecialchars($content['features_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Section Subtitle</label>
        <input type="text" name="features_subtitle" value="<?= htmlspecialchars($content['features_subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>

  <!-- How it works -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">🔢 How It Works</h2>
    <div class="mb-4"><label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
      <input type="text" name="hiw_title" value="<?= htmlspecialchars($content['hiw_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="bg-gray-50 rounded-xl p-3">
        <label class="block text-xs font-medium text-gray-500 mb-1">Step <?= $i ?> Title</label>
        <input type="text" name="step_<?= $i ?>_title" value="<?= htmlspecialchars($content["step_{$i}_title"]) ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
        <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
        <textarea name="step_<?= $i ?>_desc" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content["step_{$i}_desc"]) ?></textarea>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Testimonials -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">⭐ Testimonials</h2>
    <div class="space-y-4">
      <?php for ($i = 1; $i <= 3; $i++): ?>
      <div class="bg-gray-50 rounded-xl p-4">
        <label class="block text-xs font-medium text-gray-500 mb-1">Quote <?= $i ?></label>
        <textarea name="testimonial_<?= $i ?>_quote" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm mb-2 resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content["testimonial_{$i}_quote"]) ?></textarea>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
            <input type="text" name="testimonial_<?= $i ?>_name" value="<?= htmlspecialchars($content["testimonial_{$i}_name"]) ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
            <input type="text" name="testimonial_<?= $i ?>_role" value="<?= htmlspecialchars($content["testimonial_{$i}_role"]) ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Bottom CTA -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">🚀 Bottom CTA</h2>
    <div class="space-y-4">
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="cta_title" value="<?= htmlspecialchars($content['cta_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
        <input type="text" name="cta_subtitle" value="<?= htmlspecialchars($content['cta_subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      <div><label class="block text-xs font-medium text-gray-500 mb-1">Button Text</label>
        <input type="text" name="cta_button" value="<?= htmlspecialchars($content['cta_button']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
    </div>
  </div>

  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Homepage Content</button>
</form>
