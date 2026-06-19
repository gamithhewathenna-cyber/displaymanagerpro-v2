<?php // admin/blog/edit.php ?>
<div class="mb-6 flex items-center gap-3">
  <a href="/admin/blog" class="text-gray-400 hover:text-gray-600 transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
  </a>
  <h2 class="font-semibold text-gray-900">Edit Article</h2>
  <?php if ($article['status'] === 'published'): ?>
  <a href="/news/<?= Helpers::e($article['slug']) ?>" target="_blank" class="ml-auto flex items-center gap-1 text-xs text-primary-600 hover:text-primary-700 font-medium">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
    View live
  </a>
  <?php endif; ?>
</div>

<form method="POST" action="/admin/blog/<?= $article['id'] ?>" enctype="multipart/form-data" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Main column -->
    <div class="lg:col-span-2 space-y-5">

      <!-- Title -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Title *</label>
        <input type="text" name="title" id="article-title" required
          value="<?= Helpers::e($article['title']) ?>"
          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-100">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 mt-4">URL Slug</label>
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-400">/news/</span>
          <input type="text" name="slug" id="article-slug"
            value="<?= Helpers::e($article['slug']) ?>"
            class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-100">
        </div>
      </div>

      <!-- Excerpt -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Excerpt <span class="text-gray-400 font-normal">(optional – shown in listings)</span></label>
        <textarea name="excerpt" rows="3" maxlength="600"
          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-100 resize-none"><?= Helpers::e($article['excerpt'] ?? '') ?></textarea>
      </div>

      <!-- Body -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-2">
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Content *</label>
          <span class="text-xs text-gray-400">HTML supported</span>
        </div>
        <div class="flex flex-wrap gap-1 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
          <?php
            $btns = [
              ['B','<strong>','</strong>','font-bold'],
              ['I','<em>','</em>','italic'],
              ['H2','<h2>','</h2>',''],
              ['H3','<h3>','</h3>',''],
              ['UL','<ul>\n  <li>','</li>\n</ul>',''],
              ['OL','<ol>\n  <li>','</li>\n</ol>',''],
              ['Link','<a href="">','</a>',''],
              ['IMG','<img src="" alt="" class="w-full rounded-lg my-4">','',''],
            ];
            foreach ($btns as [$label, $open, $close, $cls]):
          ?>
          <button type="button" class="text-xs <?= $cls ?> border border-gray-200 bg-white hover:bg-primary-50 hover:border-primary-300 px-2.5 py-1 rounded transition-colors"
            onclick="insertTag('body-editor','<?= addslashes($open) ?>','<?= addslashes($close) ?>')"><?= $label ?></button>
          <?php endforeach; ?>
        </div>
        <textarea name="body" id="body-editor" rows="20" required
          class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm font-mono focus:outline-none focus:border-primary-400 focus:ring-1 focus:ring-primary-100 resize-y"><?= htmlspecialchars($article['body'], ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <!-- SEO -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">SEO Settings</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">SEO Title</label>
            <input type="text" name="seo_title" maxlength="255"
              value="<?= Helpers::e($article['seo_title'] ?? '') ?>"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Meta Description</label>
            <textarea name="seo_description" rows="2" maxlength="500"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 resize-none"><?= Helpers::e($article['seo_description'] ?? '') ?></textarea>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Focus Keyword</label>
            <input type="text" name="focus_keyword" maxlength="100"
              value="<?= Helpers::e($article['focus_keyword'] ?? '') ?>"
              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
          </div>
        </div>
      </div>

    </div>

    <!-- Sidebar column -->
    <div class="space-y-5">

      <!-- Publish settings -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Publish</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Status</label>
            <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400 bg-white">
              <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
              <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Publish Date</label>
            <input type="datetime-local" name="published_at"
              value="<?= $article['published_at'] ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : '' ?>"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
          </div>
        </div>
        <div class="mt-5 flex gap-2">
          <button type="submit" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
            Update Article
          </button>
          <a href="/admin/blog" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-500 hover:bg-gray-50 transition-colors">
            Cancel
          </a>
        </div>
      </div>

      <!-- Featured image -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Featured Image</h3>
        <?php if ($article['featured_image_url']): ?>
        <img src="<?= Helpers::e($article['featured_image_url']) ?>" alt="" class="w-full rounded-lg object-cover max-h-40 mb-3" id="img-preview">
        <?php else: ?>
        <img id="img-preview" class="hidden w-full rounded-lg object-cover max-h-40 mb-3" alt="Preview">
        <?php endif; ?>
        <label class="block w-full border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer hover:border-primary-300 transition-colors">
          <input type="file" name="featured_image" accept="image/*" class="hidden" onchange="previewImg(this)">
          <p class="text-xs text-gray-400"><?= $article['featured_image_url'] ? 'Click to replace image' : 'Click to upload image' ?></p>
          <p class="text-xs text-gray-300 mt-1">JPG, PNG, WebP – max 1 MB</p>
        </label>
      </div>

      <!-- Details -->
      <div class="bg-white rounded-xl border border-gray-100 p-5">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Details</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Category</label>
            <input type="text" name="category" maxlength="100" list="category-suggestions"
              value="<?= Helpers::e($article['category'] ?? '') ?>"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
            <datalist id="category-suggestions">
              <option value="Tips">
              <option value="Product Update">
              <option value="Case Study">
              <option value="News">
              <option value="Tutorial">
            </datalist>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1.5">Author Name</label>
            <input type="text" name="author_name" maxlength="100"
              value="<?= Helpers::e($article['author_name'] ?? '') ?>"
              class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
          </div>
        </div>
      </div>

      <!-- Meta -->
      <div class="bg-white rounded-xl border border-gray-100 p-5 text-xs text-gray-400 space-y-1">
        <div>Created: <?= date('M j, Y g:i a', strtotime($article['created_at'])) ?></div>
        <div>Updated: <?= date('M j, Y g:i a', strtotime($article['updated_at'])) ?></div>
        <div>Views: <?= number_format((int)$article['view_count']) ?></div>
      </div>

    </div>
  </div>
</form>

<script>
function insertTag(id, open, close) {
  var ta = document.getElementById(id);
  var s = ta.selectionStart, e = ta.selectionEnd;
  var sel = ta.value.substring(s, e);
  ta.value = ta.value.substring(0, s) + open + sel + close + ta.value.substring(e);
  ta.focus();
  ta.selectionStart = s + open.length;
  ta.selectionEnd   = s + open.length + sel.length;
}
function previewImg(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var p = document.getElementById('img-preview');
      p.src = e.target.result;
      p.classList.remove('hidden');
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
