<?php
  $slots = [];
  for ($i = 1; $i <= 6; $i++) {
      $slots[$i] = Settings::get("content_slider_image_{$i}", '');
  }
?>

<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>

<div class="max-w-3xl">

  <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700 mb-6">
    <strong>Feature Slider Images</strong> — Upload up to 6 PNG screenshots or feature images. These appear in the homepage feature showcase section beside your heading and CTA. Recommended size: <strong>1280 × 800 px</strong> (16:10) or similar landscape ratio.
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <?php for ($i = 1; $i <= 6; $i++): ?>
    <?php $current = $slots[$i]; ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">

      <!-- Slot header -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <span class="w-7 h-7 rounded-lg bg-primary-50 text-primary-600 font-bold text-xs flex items-center justify-center"><?= $i ?></span>
          <span class="font-semibold text-gray-800 text-sm">Slide <?= $i ?></span>
        </div>
        <?php if ($current): ?>
        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Uploaded</span>
        <?php else: ?>
        <span class="text-xs bg-gray-100 text-gray-400 font-semibold px-2 py-0.5 rounded-full">Empty</span>
        <?php endif; ?>
      </div>

      <!-- Preview -->
      <div class="mb-4 rounded-xl overflow-hidden bg-gray-100 border border-gray-200" style="aspect-ratio:16/10;">
        <?php if ($current): ?>
          <img src="<?= Helpers::e($current) ?>?v=<?= time() ?>" alt="Slide <?= $i ?>" class="w-full h-full object-cover">
        <?php else: ?>
          <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-gray-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs text-gray-400">No image uploaded</span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Upload form -->
      <form method="POST" action="/admin/content/slider/image/<?= $i ?>" enctype="multipart/form-data" class="space-y-2">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <div class="flex items-center gap-2">
          <label class="flex-1 min-w-0 block">
            <span class="sr-only">Choose PNG image</span>
            <input type="file" name="slider_image" accept="image/png"
              class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
          </label>
          <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-lg text-xs transition-colors whitespace-nowrap">
            <?= $current ? 'Replace' : 'Upload' ?>
          </button>
        </div>
        <p class="text-xs text-gray-400">PNG only · Max 5 MB · Recommended 1280 × 800 px</p>
      </form>

      <!-- Remove -->
      <?php if ($current): ?>
      <form method="POST" action="/admin/content/slider/image/<?= $i ?>/remove" class="mt-2">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit"
          onclick="return confirm('Remove slide <?= $i ?> image?')"
          class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">
          Remove image
        </button>
      </form>
      <?php endif; ?>

    </div>
    <?php endfor; ?>
  </div>

  <!-- Tip -->
  <div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-4 text-xs text-gray-500">
    <strong class="text-gray-700">Tips:</strong> Use consistent dimensions across all slides for a polished look.
    Screenshots at 1280 × 800 px (landscape) work best.
    Empty slots are simply skipped — the slider shows only uploaded images.
    If no images are uploaded, the homepage displays elegant placeholder slides automatically.
  </div>

</div>
