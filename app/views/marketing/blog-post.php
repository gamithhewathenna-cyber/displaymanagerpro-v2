<?php
$pubDate     = $post['published_at'] ? date('d F Y', strtotime($post['published_at'])) : '';
$metaDesc    = $post['meta_description'] ?: $post['excerpt'] ?: '';
$seoTitle    = $post['meta_title']       ?: $post['title'];
?>
<!-- Article JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": <?= json_encode($post['title']) ?>,
  "description": <?= json_encode($metaDesc) ?>,
  "author": { "@type": "Person", "name": <?= json_encode($post['author']) ?> },
  "datePublished": <?= json_encode($post['published_at'] ?? '') ?>,
  "dateModified":  <?= json_encode($post['updated_at']  ?? '') ?>
  <?php if ($post['featured_image']): ?>,
  "image": <?= json_encode(Helpers::baseUrl(ltrim($post['featured_image'], '/'))) ?>
  <?php endif; ?>
}
</script>

<!-- Hero -->
<div class="hero-gradient py-14 px-4 text-center">
  <div class="max-w-[1200px] mx-auto">
    <div class="flex items-center justify-center gap-3 text-sm text-indigo-200 mb-4">
      <span class="font-medium text-white"><?= Helpers::e($post['author']) ?></span>
      <?php if ($pubDate): ?>
      <span class="opacity-50">·</span>
      <time datetime="<?= Helpers::e($post['published_at']) ?>" class="opacity-75"><?= $pubDate ?></time>
      <?php endif; ?>
    </div>
    <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
      <?= Helpers::e($post['title']) ?>
    </h1>
  </div>
</div>

<!-- Article -->
<section class="py-14 px-4 bg-white">
  <div class="max-w-[1200px] mx-auto">

    <!-- Back -->
    <a href="/blog" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-primary-600 transition-colors mb-8">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to News & Updates
    </a>

    <!-- Featured image / placeholder -->
    <div class="mb-8 rounded-2xl overflow-hidden bg-gray-100" style="width:100%;max-width:1000px;aspect-ratio:1000/600;">
      <?php if ($post['featured_image']): ?>
      <img src="<?= Helpers::e($post['featured_image']) ?>" alt="<?= Helpers::e($post['title']) ?>"
        class="w-full h-full object-cover">
      <?php else: ?>
      <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 text-gray-300">
        <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span class="text-sm font-medium">1000 × 600</span>
      </div>
      <?php endif; ?>
    </div>

    <!-- Excerpt lead -->
    <?php if ($post['excerpt']): ?>
    <p class="text-lg text-gray-500 leading-relaxed mb-8 border-l-4 border-primary-200 pl-4">
      <?= Helpers::e($post['excerpt']) ?>
    </p>
    <?php endif; ?>

    <!-- Content -->
    <?php if ($post['content']): ?>
    <div class="blog-prose">
      <?= $post['content'] ?>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="mt-14 pt-8 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
      <a href="/blog" class="text-sm text-primary-600 font-semibold hover:text-primary-700 transition-colors">← News & Updates</a>
      <a href="/register" class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-sm">
        Start Free Trial →
      </a>
    </div>

  </div>
</section>


<style>
.blog-prose { color: #374151; font-size: .9375rem; line-height: 1.75; }
.blog-prose h2 { font-size: 1.5rem; font-weight: 700; color: #111827; margin: 2rem 0 .75rem; }
.blog-prose h3 { font-size: 1.15rem; font-weight: 600; color: #1f2937; margin: 1.5rem 0 .5rem; }
.blog-prose p  { margin: 0 0 1.25rem; }
.blog-prose ul, .blog-prose ol { padding-left: 1.5rem; margin: 0 0 1.25rem; }
.blog-prose li { margin-bottom: .4rem; }
.blog-prose ul li { list-style-type: disc; }
.blog-prose ol li { list-style-type: decimal; }
.blog-prose strong { font-weight: 600; color: #111827; }
.blog-prose a  { color: #4f46e5; text-decoration: underline; text-underline-offset: 2px; }
.blog-prose a:hover { color: #4338ca; }
.blog-prose blockquote { border-left: 4px solid #e0e0ff; padding: .75rem 1rem; margin: 1.5rem 0; background: #f8f8ff; border-radius: 0 .75rem .75rem 0; color: #4b5563; font-style: italic; }
.blog-prose img { max-width: 100%; border-radius: .75rem; margin: 1.5rem 0; }
.blog-prose hr  { border: none; border-top: 1px solid #e5e7eb; margin: 2rem 0; }
.blog-prose code { background: #f3f4f6; padding: .1em .3em; border-radius: .3em; font-size: .875em; font-family: monospace; }
</style>

<?php require VIEWS_PATH . '/marketing/_cta.php'; ?>
