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

<!-- Featured Image -->
<?php if ($post['featured_image']): ?>
<div class="w-full bg-gray-100" style="max-height:480px;overflow:hidden;">
  <img src="<?= Helpers::e($post['featured_image']) ?>" alt="<?= Helpers::e($post['title']) ?>"
    class="w-full object-cover" style="max-height:480px;">
</div>
<?php endif; ?>

<!-- Article -->
<section class="py-14 px-4 bg-white">
  <div class="max-w-3xl mx-auto">

    <!-- Back -->
    <a href="/blog" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-primary-600 transition-colors mb-8">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to Blog
    </a>

    <!-- Meta -->
    <div class="flex items-center gap-3 text-xs text-gray-400 mb-5">
      <span class="font-medium text-gray-600"><?= Helpers::e($post['author']) ?></span>
      <?php if ($pubDate): ?>
      <span>·</span>
      <time datetime="<?= Helpers::e($post['published_at']) ?>"><?= $pubDate ?></time>
      <?php endif; ?>
    </div>

    <!-- Title -->
    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight mb-6">
      <?= Helpers::e($post['title']) ?>
    </h1>

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
      <a href="/blog" class="text-sm text-primary-600 font-semibold hover:text-primary-700 transition-colors">← All Articles</a>
      <a href="/register" class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-sm">
        Start Free Trial →
      </a>
    </div>

  </div>
</section>

<!-- Related Pages -->
<section class="py-10 px-4 bg-gray-50 border-t border-gray-100">
  <div class="max-w-5xl mx-auto">
    <p class="text-center text-xs font-semibold text-gray-400 uppercase tracking-widest mb-5">You May Also Like</p>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <a href="/features" class="bg-white border border-gray-100 hover:border-primary-300 rounded-xl p-4 flex items-center gap-3 transition-all group hover:shadow-sm">
        <span class="text-xl flex-shrink-0">⚡</span>
        <div><div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Features</div><div class="text-xs text-gray-400">What's included</div></div>
      </a>
      <a href="/pricing" class="bg-white border border-gray-100 hover:border-primary-300 rounded-xl p-4 flex items-center gap-3 transition-all group hover:shadow-sm">
        <span class="text-xl flex-shrink-0">💳</span>
        <div><div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Pricing</div><div class="text-xs text-gray-400">Plans & trial</div></div>
      </a>
      <a href="/industries" class="bg-white border border-gray-100 hover:border-primary-300 rounded-xl p-4 flex items-center gap-3 transition-all group hover:shadow-sm">
        <span class="text-xl flex-shrink-0">🏢</span>
        <div><div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Industries</div><div class="text-xs text-gray-400">Your sector</div></div>
      </a>
      <a href="/blog" class="bg-white border border-gray-100 hover:border-primary-300 rounded-xl p-4 flex items-center gap-3 transition-all group hover:shadow-sm">
        <span class="text-xl flex-shrink-0">📝</span>
        <div><div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">More Articles</div><div class="text-xs text-gray-400">Tips & guides</div></div>
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
