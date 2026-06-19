<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>

<?php $i1 = Settings::get('content_about_image1',''); $i2 = Settings::get('content_about_image2',''); ?>

<form method="POST" action="/admin/content/about/save" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

  <!-- Hero -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">🏠 Hero Section</h2>
    <div>
      <label class="block text-xs font-medium text-gray-500 mb-1">Hero Subtitle <span class="text-gray-400">(paragraph below the headline)</span></label>
      <textarea name="hero_subtitle" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['hero_subtitle']) ?></textarea>
    </div>
  </div>

  <!-- Section 1 – Who We Help -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">🏢 Section 1 — Who We Help</h2>
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Badge Text</label>
          <input type="text" name="s1_badge" value="<?= htmlspecialchars($content['s1_badge']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
          <input type="text" name="s1_title" value="<?= htmlspecialchars($content['s1_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 1</label>
        <textarea name="s1_body1" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['s1_body1']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 2</label>
        <textarea name="s1_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['s1_body2']) ?></textarea>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Stat Number</label>
          <input type="text" name="s1_stat_num" value="<?= htmlspecialchars($content['s1_stat_num']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Stat Label</label>
          <input type="text" name="s1_stat_label" value="<?= htmlspecialchars($content['s1_stat_label']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Industries List <span class="text-gray-400">(one per line)</span></label>
        <textarea name="s1_industries" rows="10" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['s1_industries']) ?></textarea>
      </div>
    </div>
  </div>

  <!-- Section 2 – The Smarter Way -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">☁️ Section 2 — The Smarter Way</h2>
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Badge Text</label>
          <input type="text" name="s2_badge" value="<?= htmlspecialchars($content['s2_badge']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
          <input type="text" name="s2_title" value="<?= htmlspecialchars($content['s2_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 1</label>
        <textarea name="s2_body1" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['s2_body1']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 2</label>
        <textarea name="s2_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['s2_body2']) ?></textarea>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Stat Number</label>
          <input type="text" name="s2_stat_num" value="<?= htmlspecialchars($content['s2_stat_num']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Stat Label</label>
          <input type="text" name="s2_stat_label" value="<?= htmlspecialchars($content['s2_stat_label']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Bullet Points <span class="text-gray-400">(one per line)</span></label>
        <textarea name="s2_bullets" rows="7" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['s2_bullets']) ?></textarea>
      </div>
    </div>
  </div>

  <!-- Section 3 – Why Choose Us -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">⭐ Section 3 — Why Choose Us</h2>
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Badge Text</label>
          <input type="text" name="s3_badge" value="<?= htmlspecialchars($content['s3_badge']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
          <input type="text" name="s3_title" value="<?= htmlspecialchars($content['s3_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
        <input type="text" name="s3_subtitle" value="<?= htmlspecialchars($content['s3_subtitle']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php for ($r = 1; $r <= 6; $r++): ?>
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
          <div class="flex items-center gap-2 mb-3">
            <span class="w-6 h-6 rounded-lg bg-primary-50 text-primary-600 font-bold text-xs flex items-center justify-center"><?= $r ?></span>
            <span class="text-sm font-medium text-gray-700">Reason <?= $r ?></span>
          </div>
          <div class="grid grid-cols-4 gap-2 mb-2">
            <div>
              <label class="block text-xs text-gray-400 mb-1">Icon</label>
              <input type="text" name="s3_r<?= $r ?>_icon" value="<?= htmlspecialchars($content["s3_r{$r}_icon"]) ?>" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="col-span-3">
              <label class="block text-xs text-gray-400 mb-1">Title</label>
              <input type="text" name="s3_r<?= $r ?>_title" value="<?= htmlspecialchars($content["s3_r{$r}_title"]) ?>" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
          </div>
          <div>
            <label class="block text-xs text-gray-400 mb-1">Description</label>
            <textarea name="s3_r<?= $r ?>_desc" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content["s3_r{$r}_desc"]) ?></textarea>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- Our Vision -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">🌟 Our Vision</h2>
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Badge Text</label>
          <input type="text" name="vision_badge" value="<?= htmlspecialchars($content['vision_badge']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
          <input type="text" name="vision_title" value="<?= htmlspecialchars($content['vision_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 1</label>
        <textarea name="vision_body1" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['vision_body1']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 2</label>
        <textarea name="vision_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['vision_body2']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Paragraph 3</label>
        <textarea name="vision_body3" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['vision_body3']) ?></textarea>
      </div>
    </div>
  </div>

  <!-- Bottom CTA -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5 flex items-center gap-2">🚀 Bottom CTA</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="cta_title" value="<?= htmlspecialchars($content['cta_title']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Body Line 1</label>
        <textarea name="cta_body1" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['cta_body1']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Body Line 2</label>
        <textarea name="cta_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($content['cta_body2']) ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Button Text</label>
        <input type="text" name="cta_button" value="<?= htmlspecialchars($content['cta_button']) ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
    </div>
  </div>

  <?php require VIEWS_PATH . '/admin/content/_seo_fields.php'; ?>

  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save About Page</button>
</form>

<!-- Images -->
<div class="bg-white rounded-2xl border border-gray-100 p-6 mt-6">
  <h2 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">🖼️ Section Images</h2>
  <p class="text-xs text-gray-400 mb-6">Image 1 appears in the "Who We Help" section. Image 2 appears in "The Smarter Way" section. Recommended: <strong>800 × 600 px</strong> (4:3). JPG, PNG, or WebP, max 5 MB.</p>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <?php foreach ([1 => 'Who We Help', 2 => 'The Smarter Way'] as $slot => $label):
      $cur = ($slot === 1) ? $i1 : $i2;
    ?>
    <div class="border border-gray-100 rounded-xl p-4">
      <div class="flex items-center justify-between mb-3">
        <div>
          <div class="text-sm font-semibold text-gray-700">Image <?= $slot ?></div>
          <div class="text-xs text-gray-400"><?= $label ?> section</div>
        </div>
        <?php if ($cur): ?>
        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Uploaded</span>
        <?php else: ?>
        <span class="text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full">Placeholder</span>
        <?php endif; ?>
      </div>
      <!-- Preview -->
      <div class="rounded-xl overflow-hidden bg-gray-100 border border-gray-200 mb-3" style="aspect-ratio:4/3;">
        <?php if ($cur): ?>
          <img src="<?= Helpers::e($cur) ?>?v=<?= time() ?>" alt="About image <?= $slot ?>" class="w-full h-full object-cover">
        <?php else: ?>
          <div class="w-full h-full flex flex-col items-center justify-center gap-1.5 text-gray-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-xs text-gray-400">No image — showing placeholder</span>
          </div>
        <?php endif; ?>
      </div>
      <!-- Upload -->
      <form method="POST" action="/admin/content/about/image/<?= $slot ?>" enctype="multipart/form-data" class="space-y-2">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <div class="flex items-center gap-2">
          <label class="flex-1 min-w-0 block">
            <input type="file" name="about_image" accept="image/jpeg,image/png,image/webp"
              class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
          </label>
          <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-3 py-1.5 rounded-lg text-xs transition-colors whitespace-nowrap">
            <?= $cur ? 'Replace' : 'Upload' ?>
          </button>
        </div>
      </form>
      <?php if ($cur): ?>
      <form method="POST" action="/admin/content/about/image/<?= $slot ?>/remove" class="mt-2">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit" onclick="return confirm('Remove this image?')" class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Remove image</button>
      </form>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
