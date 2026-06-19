<?php // marketing/news-article.php – Individual article page
  $_baseUrl = rtrim(Helpers::baseUrl(), '/');
  $_shareUrl = Helpers::e($_baseUrl . '/news/' . $article['slug']);
  $_shareTitle = Helpers::e($article['title']);
?>
<?php
  // Override SEO meta for this article (layout reads $title + we inject OG via the head override below)
  // The layout uses $title for <title>; seo_description is passed via $article and accessed in head below.
?>

<!-- Structured data (JSON-LD) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": <?= json_encode($article['title']) ?>,
  "description": <?= json_encode($article['seo_description'] ?: $article['excerpt'] ?: '') ?>,
  <?php if ($article['featured_image_url']): ?>
  "image": <?= json_encode($article['featured_image_url']) ?>,
  <?php endif; ?>
  "author": {
    "@type": "Person",
    "name": <?= json_encode($article['author_name'] ?: 'Admin') ?>
  },
  "publisher": {
    "@type": "Organization",
    "name": <?= json_encode(Settings::get('company_name', APP_NAME)) ?>
  },
  <?php if ($article['published_at']): ?>
  "datePublished": <?= json_encode(date('c', strtotime($article['published_at']))) ?>,
  "dateModified": <?= json_encode(date('c', strtotime($article['updated_at'] ?: $article['published_at']))) ?>,
  <?php endif; ?>
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "<?= $_shareUrl ?>"
  }
}
</script>

<!-- Breadcrumb -->
<nav class="max-w-4xl mx-auto px-4 pt-10 pb-2" aria-label="Breadcrumb">
  <ol class="flex items-center gap-2 text-sm text-gray-400">
    <li><a href="/" class="hover:text-primary-600 transition-colors">Home</a></li>
    <li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li><a href="/news" class="hover:text-primary-600 transition-colors">News & Updates</a></li>
    <li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></li>
    <li class="text-gray-600 truncate max-w-xs"><?= Helpers::e($article['title']) ?></li>
  </ol>
</nav>

<!-- Article -->
<article class="max-w-4xl mx-auto px-4 pb-20">

  <!-- Header -->
  <header class="mb-10">
    <?php if ($article['category']): ?>
    <a href="/news?category=<?= urlencode($article['category']) ?>"
      class="inline-block text-xs font-semibold text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-4 hover:bg-primary-100 transition-colors">
      <?= Helpers::e($article['category']) ?>
    </a>
    <?php endif; ?>
    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-6"><?= Helpers::e($article['title']) ?></h1>
    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-400">
      <?php if ($article['author_name']): ?>
      <span class="flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <?= Helpers::e($article['author_name']) ?>
      </span>
      <?php endif; ?>
      <?php if ($article['published_at']): ?>
      <time datetime="<?= Helpers::e($article['published_at']) ?>" class="flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <?= date('F j, Y', strtotime($article['published_at'])) ?>
      </time>
      <?php endif; ?>
      <span class="flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        <?= number_format((int)$article['view_count']) ?> views
      </span>
    </div>
  </header>

  <!-- Featured image -->
  <?php if ($article['featured_image_url']): ?>
  <figure class="mb-10 rounded-2xl overflow-hidden">
    <img src="<?= Helpers::e($article['featured_image_url']) ?>" alt="<?= Helpers::e($article['title']) ?>" class="w-full max-h-[480px] object-cover">
  </figure>
  <?php endif; ?>

  <!-- Content -->
  <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed article-body">
    <?= $article['body'] ?>
  </div>

  <!-- Social sharing -->
  <div class="mt-12 pt-8 border-t border-gray-100">
    <p class="text-sm font-semibold text-gray-500 mb-4">Share this article</p>
    <div class="flex flex-wrap gap-3">
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $_shareUrl ?>" target="_blank" rel="noopener"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
        Facebook
      </a>
      <a href="https://twitter.com/intent/tweet?url=<?= $_shareUrl ?>&text=<?= $_shareTitle ?>" target="_blank" rel="noopener"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-black hover:bg-gray-900 text-white text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        X / Twitter
      </a>
      <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $_shareUrl ?>" target="_blank" rel="noopener"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#0077B5] hover:bg-[#006097] text-white text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        LinkedIn
      </a>
      <button onclick="copyUrl()" id="copy-btn"
        class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
        Copy link
      </button>
    </div>
  </div>

</article>

<!-- Related articles -->
<?php if (!empty($related)): ?>
<section class="bg-gray-50 py-20 px-4">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Related articles</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($related as $r): ?>
      <a href="/news/<?= Helpers::e($r['slug']) ?>" class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md hover:border-primary-100 transition-all flex flex-col">
        <?php if ($r['featured_image_url']): ?>
        <div class="h-40 overflow-hidden">
          <img src="<?= Helpers::e($r['featured_image_url']) ?>" alt="<?= Helpers::e($r['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
        <?php else: ?>
        <div class="h-40 bg-gradient-to-br from-primary-50 to-indigo-100"></div>
        <?php endif; ?>
        <div class="p-5 flex flex-col flex-1">
          <?php if ($r['category']): ?>
          <span class="text-xs font-semibold text-primary-600 mb-1"><?= Helpers::e($r['category']) ?></span>
          <?php endif; ?>
          <h3 class="font-semibold text-gray-900 leading-snug mb-2 group-hover:text-primary-600 transition-colors"><?= Helpers::e($r['title']) ?></h3>
          <?php if ($r['excerpt']): ?>
          <p class="text-xs text-gray-400 leading-relaxed flex-1"><?= Helpers::e(mb_strimwidth($r['excerpt'], 0, 120, '…')) ?></p>
          <?php endif; ?>
          <div class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-50">
            <?= $r['published_at'] ? date('M j, Y', strtotime($r['published_at'])) : '' ?>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-10">
      <a href="/news" class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors">
        View all articles
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<style>
.article-body h1,.article-body h2,.article-body h3 { font-weight:700; color:#111827; margin:1.5em 0 0.75em; }
.article-body h1 { font-size:2em; }
.article-body h2 { font-size:1.5em; }
.article-body h3 { font-size:1.25em; }
.article-body p  { margin:0 0 1.25em; }
.article-body ul,.article-body ol { padding-left:1.5em; margin:0 0 1.25em; }
.article-body li { margin-bottom:0.4em; }
.article-body a  { color:#6366f1; text-decoration:underline; }
.article-body a:hover { color:#4f46e5; }
.article-body img { max-width:100%; height:auto; border-radius:0.75rem; margin:1.5em 0; }
.article-body blockquote { border-left:4px solid #6366f1; padding:0.75em 1em; margin:1.5em 0; background:#f0f0ff; border-radius:0 0.5rem 0.5rem 0; color:#374151; font-style:italic; }
.article-body strong { font-weight:600; color:#111827; }
.article-body code  { background:#f3f4f6; padding:0.2em 0.4em; border-radius:0.3rem; font-size:0.875em; }
.article-body pre  { background:#1f2937; color:#e5e7eb; padding:1em 1.25em; border-radius:0.75rem; overflow-x:auto; margin:1.5em 0; }
.article-body pre code { background:none; padding:0; font-size:0.85em; }
</style>

<script>
function copyUrl() {
  navigator.clipboard.writeText(window.location.href).then(function() {
    var btn = document.getElementById('copy-btn');
    btn.textContent = 'Copied!';
    setTimeout(function(){ btn.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>Copy link'; }, 2000);
  });
}
</script>
