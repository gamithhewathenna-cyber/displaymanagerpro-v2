<?php
$isEdit    = !is_null($post);
$formAction = $isEdit ? '/admin/blog/' . $post['id'] : '/admin/blog';
?>

<div class="flex items-center gap-3 mb-6">
  <a href="/admin/blog" class="text-gray-400 hover:text-gray-600 transition-colors text-sm">← Blog Posts</a>
  <span class="text-gray-200">/</span>
  <h2 class="font-semibold text-gray-900"><?= $isEdit ? 'Edit Post' : 'New Post' ?></h2>
  <?php if ($isEdit && $post['status'] === 'published'): ?>
  <a href="/blog/<?= Helpers::e($post['slug']) ?>" target="_blank" class="ml-auto text-xs text-primary-600 hover:underline">View live →</a>
  <?php endif; ?>
</div>

<form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-6">
  <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Csrf::token() ?>">

  <!-- Left: main content -->
  <div class="space-y-5">

    <!-- Title -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Title <span class="text-red-400">*</span></label>
        <input type="text" name="title" id="post-title" required autofocus
          value="<?= Helpers::e($post['title'] ?? '') ?>"
          placeholder="Enter post title…"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 font-medium">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Slug <span class="text-gray-400 font-normal">(URL path — auto-generated if left blank)</span></label>
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-400 flex-shrink-0">/blog/</span>
          <input type="text" name="slug" id="post-slug"
            value="<?= Helpers::e($post['slug'] ?? '') ?>"
            placeholder="post-url-slug"
            class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 font-mono">
        </div>
      </div>
    </div>

    <!-- Excerpt -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <label class="block text-xs font-medium text-gray-700 mb-1">Excerpt <span class="text-gray-400 font-normal">(short summary shown on the blog listing page)</span></label>
      <textarea name="excerpt" rows="3" placeholder="A brief description of what this post covers…"
        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= Helpers::e($post['excerpt'] ?? '') ?></textarea>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <label class="block text-xs font-medium text-gray-700 mb-1">Content <span class="text-gray-400 font-normal">(HTML supported)</span></label>
      <textarea name="content" rows="20" placeholder="Write your post content here. You can use HTML tags like &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;a&gt;, etc."
        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y font-mono leading-relaxed"><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
      <p class="text-xs text-gray-400 mt-1">Tip: Use &lt;h2&gt; for headings, &lt;p&gt; for paragraphs, &lt;ul&gt;&lt;li&gt; for lists, &lt;strong&gt; for bold.</p>
    </div>

    <!-- SEO -->
    <div class="bg-white rounded-2xl border border-primary-100 p-6 space-y-4">
      <h3 class="font-semibold text-gray-900 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        SEO
      </h3>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Meta Title <span class="text-gray-400 font-normal">(defaults to post title if blank)</span></label>
        <input type="text" name="meta_title" maxlength="70"
          value="<?= Helpers::e($post['meta_title'] ?? '') ?>"
          placeholder="SEO title for search results…"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="text-xs text-gray-400 mt-1">Recommended: 50–70 characters</p>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Meta Description</label>
        <textarea name="meta_description" rows="2" maxlength="160"
          placeholder="Summary shown below the title in search results…"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= Helpers::e($post['meta_description'] ?? '') ?></textarea>
        <p class="text-xs text-gray-400 mt-1">Recommended: 120–160 characters</p>
      </div>
    </div>

  </div>

  <!-- Right: sidebar controls -->
  <div class="space-y-5">

    <!-- Publish -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
      <h3 class="font-semibold text-gray-900 text-sm">Publish</h3>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
          <option value="draft"     <?= ($post['status'] ?? 'draft') === 'draft'     ? 'selected' : '' ?>>Draft</option>
          <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Author</label>
        <input type="text" name="author" value="<?= Helpers::e($post['author'] ?? 'Admin') ?>"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <?php if ($isEdit && $post['published_at']): ?>
      <p class="text-xs text-gray-400">Published: <?= date('d M Y, H:i', strtotime($post['published_at'])) ?></p>
      <?php endif; ?>
      <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
        <?= $isEdit ? 'Save Changes' : 'Create Post' ?>
      </button>
    </div>

    <!-- Featured Image -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-3">
      <h3 class="font-semibold text-gray-900 text-sm">Featured Image</h3>

      <?php if ($isEdit && !empty($post['featured_image'])): ?>
      <div class="relative rounded-xl overflow-hidden bg-gray-50" style="aspect-ratio:16/9;">
        <img src="<?= Helpers::e($post['featured_image']) ?>" alt="Featured" class="w-full h-full object-cover">
      </div>
      <div class="flex gap-2">
        <label class="flex-1 cursor-pointer border border-gray-200 hover:border-primary-300 text-gray-600 text-xs font-medium px-3 py-2 rounded-lg transition-colors text-center">
          Replace
          <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" class="hidden">
        </label>
        <button type="submit" name="remove_image" value="1"
          onclick="return confirm('Remove featured image?')"
          class="border border-red-200 hover:bg-red-50 text-red-500 text-xs font-medium px-3 py-2 rounded-lg transition-colors">
          Remove
        </button>
      </div>
      <?php else: ?>
      <div class="border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center py-8 text-center">
        <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <label class="cursor-pointer text-primary-600 text-xs font-medium hover:underline">
          Upload image
          <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" class="hidden">
        </label>
        <p class="text-xs text-gray-400 mt-1">JPG, PNG or WebP · Max 5 MB</p>
      </div>
      <?php endif; ?>
    </div>

  </div>
</form>

<script>
// Auto-generate slug from title (only when slug is empty or on new post)
(function(){
  var titleEl = document.getElementById('post-title');
  var slugEl  = document.getElementById('post-slug');
  var userEdited = <?= $isEdit && !empty($post['slug']) ? 'true' : 'false' ?>;
  if (titleEl && slugEl) {
    slugEl.addEventListener('input', function(){ userEdited = true; });
    titleEl.addEventListener('input', function(){
      if (userEdited) return;
      slugEl.value = titleEl.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
    });
  }
})();
</script>
