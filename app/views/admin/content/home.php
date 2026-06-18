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

  <!-- Hero Slides Text -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">🎞️ Hero Slider — Slide Text</h2>
    <p class="text-xs text-gray-400 mb-5">Each slide can have its own heading and description. Slides without text use the hero section defaults above. Upload images for each slide in the section below after saving.</p>
    <div class="space-y-4">
      <?php for ($i = 1; $i <= 6; $i++): ?>
      <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
        <div class="flex items-center gap-2 mb-3">
          <span class="w-6 h-6 rounded-lg bg-primary-50 text-primary-600 font-bold text-xs flex items-center justify-center"><?= $i ?></span>
          <span class="text-sm font-semibold text-gray-700">Slide <?= $i ?></span>
          <?php if ($i === 1): ?><span class="text-xs text-gray-400 ml-1">(leave blank to use Hero heading above)</span><?php endif; ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Heading</label>
            <input type="text" name="slide_<?= $i ?>_heading" value="<?= htmlspecialchars($content["slide_{$i}_heading"] ?? '') ?>"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              placeholder="<?= $i === 1 ? 'e.g. Update Every Screen In Seconds' : 'Slide ' . $i . ' heading' ?>">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Short Description</label>
            <input type="text" name="slide_<?= $i ?>_sub" value="<?= htmlspecialchars($content["slide_{$i}_sub"] ?? '') ?>"
              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              placeholder="1–2 sentence description for this slide">
          </div>
        </div>
      </div>
      <?php endfor; ?>
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

  <?php require VIEWS_PATH . '/admin/content/_seo_fields.php'; ?>

  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Homepage Content</button>
</form>

<!-- Hero Slider Images (separate upload forms, one per slide) -->
<?php
  $_hSlots = [];
  for ($i = 1; $i <= 6; $i++) $_hSlots[$i] = Settings::get("content_home_slide_{$i}", '');
?>
<div class="bg-white rounded-2xl border border-gray-100 p-6 mt-6">
  <h2 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">🖼️ Hero Slider — Slide Images</h2>
  <p class="text-xs text-gray-400 mb-5">Upload one PNG image per slide. The image appears on the right side of that slide. Empty slots show a built-in mockup. Recommended: <strong>1280 × 800 px</strong> (16:10 landscape).</p>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php for ($i = 1; $i <= 6; $i++): ?>
    <?php $cur = $_hSlots[$i]; ?>
    <div class="border border-gray-100 rounded-xl p-4">
      <!-- Header -->
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <span class="w-6 h-6 rounded-lg bg-primary-50 text-primary-600 font-bold text-xs flex items-center justify-center"><?= $i ?></span>
          <span class="text-sm font-medium text-gray-700">Slide <?= $i ?></span>
        </div>
        <?php if ($cur): ?>
        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Uploaded</span>
        <?php else: ?>
        <span class="text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full">Empty</span>
        <?php endif; ?>
      </div>
      <!-- Preview -->
      <div class="rounded-lg overflow-hidden bg-gray-100 border border-gray-200 mb-3" style="aspect-ratio:16/10;">
        <?php if ($cur): ?>
          <img src="<?= Helpers::e($cur) ?>?v=<?= time() ?>" alt="Slide <?= $i ?>" class="w-full h-full object-cover">
        <?php else: ?>
          <div class="w-full h-full flex flex-col items-center justify-center gap-1.5 text-gray-300">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-xs text-gray-400">No image</span>
          </div>
        <?php endif; ?>
      </div>
      <!-- Upload -->
      <form method="POST" action="/admin/content/home/slide/<?= $i ?>" enctype="multipart/form-data" class="space-y-2">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <div class="flex items-center gap-2">
          <label class="flex-1 min-w-0 block">
            <span class="sr-only">Choose PNG</span>
            <input type="file" name="slide_image" accept="image/png"
              class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
          </label>
          <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-3 py-1.5 rounded-lg text-xs transition-colors whitespace-nowrap">
            <?= $cur ? 'Replace' : 'Upload' ?>
          </button>
        </div>
      </form>
      <!-- Remove -->
      <?php if ($cur): ?>
      <form method="POST" action="/admin/content/home/slide/<?= $i ?>/remove" class="mt-2">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit" onclick="return confirm('Remove slide <?= $i ?>?')"
          class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Remove image</button>
      </form>
      <?php endif; ?>
    </div>
    <?php endfor; ?>
  </div>
</div>

<!-- Static Banner Image -->
<?php $_bannerImg = Settings::get('content_home_banner', ''); ?>
<div class="bg-white rounded-2xl border border-gray-100 p-6 mt-6">
  <h2 class="font-semibold text-gray-900 mb-1 flex items-center gap-2">🌅 Static Banner Image</h2>
  <p class="text-xs text-gray-400 mb-5">A full-width image displayed directly below the trust bar. Replace it any time. Recommended: <strong>1920 × 400–600 px</strong> (wide, short landscape).</p>
  <div class="flex flex-col sm:flex-row gap-6 items-start">
    <!-- Preview -->
    <div class="w-full sm:w-72 rounded-xl overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0" style="aspect-ratio:16/5;">
      <?php if ($_bannerImg): ?>
        <img src="<?= Helpers::e($_bannerImg) ?>?v=<?= time() ?>" alt="Banner" class="w-full h-full object-cover">
      <?php else: ?>
        <div class="w-full h-full flex flex-col items-center justify-center gap-1.5 text-gray-300">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span class="text-xs text-gray-400">No banner image</span>
        </div>
      <?php endif; ?>
    </div>
    <!-- Actions -->
    <div class="flex-1 space-y-3">
      <form method="POST" action="/admin/content/home/banner" enctype="multipart/form-data" class="flex items-center gap-3">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <label class="flex-1 min-w-0">
          <span class="sr-only">Choose PNG</span>
          <input type="file" name="banner_image" accept="image/png"
            class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
        </label>
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition-colors whitespace-nowrap">
          <?= $_bannerImg ? 'Replace' : 'Upload' ?>
        </button>
      </form>
      <?php if ($_bannerImg): ?>
      <form method="POST" action="/admin/content/home/banner/remove">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit" onclick="return confirm('Remove the static banner image?')"
          class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Remove banner</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>
