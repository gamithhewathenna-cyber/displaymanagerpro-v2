<?php // marketing/news.php – News & Updates listing ?>

<!-- Hero -->
<section class="hero-gradient text-white py-20 px-4">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-indigo-300 bg-white/10 px-3 py-1 rounded-full mb-4">News & Updates</span>
    <h1 class="text-5xl font-extrabold mb-4">Latest from our blog</h1>
    <p class="text-gray-300 text-lg">Tips, product updates, and insights for digital signage.</p>
  </div>
</section>

<!-- Articles grid -->
<section class="py-20 px-4">
  <div class="max-w-6xl mx-auto">

    <?php if (empty($articles)): ?>
    <div class="text-center py-20">
      <svg class="w-16 h-16 text-gray-200 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
      <p class="text-gray-400 text-lg">No articles published yet. Check back soon!</p>
    </div>
    <?php else: ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($articles as $a): ?>
      <article>
        <a href="/news/<?= Helpers::e($a['slug']) ?>" class="group block bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary-100 transition-all h-full flex flex-col">
          <?php if ($a['featured_image_url']): ?>
          <div class="h-52 overflow-hidden">
            <img src="<?= Helpers::e($a['featured_image_url']) ?>" alt="<?= Helpers::e($a['title']) ?>"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
          </div>
          <?php else: ?>
          <div class="h-52 bg-gradient-to-br from-primary-50 to-indigo-100 flex items-center justify-center">
            <svg class="w-14 h-14 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
          </div>
          <?php endif; ?>
          <div class="p-6 flex flex-col flex-1">
            <?php if ($a['category']): ?>
            <span class="text-xs font-semibold text-primary-600 mb-2"><?= Helpers::e($a['category']) ?></span>
            <?php endif; ?>
            <h2 class="font-bold text-gray-900 text-lg leading-snug mb-2 group-hover:text-primary-600 transition-colors"><?= Helpers::e($a['title']) ?></h2>
            <?php if ($a['excerpt']): ?>
            <p class="text-sm text-gray-500 leading-relaxed flex-1"><?= Helpers::e($a['excerpt']) ?></p>
            <?php endif; ?>
            <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-50 text-xs text-gray-400">
              <span><?= Helpers::e($a['author_name'] ?? '') ?></span>
              <time datetime="<?= Helpers::e($a['published_at'] ?? '') ?>"><?= $a['published_at'] ? date('M j, Y', strtotime($a['published_at'])) : '' ?></time>
            </div>
          </div>
        </a>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="flex items-center justify-center gap-2 mt-14" aria-label="Pagination">
      <?php if ($page > 1): ?>
      <a href="/news?page=<?= $page - 1 ?>"
        class="flex items-center gap-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-primary-300 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Previous
      </a>
      <?php endif; ?>
      <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
      <a href="/news?page=<?= $p ?>"
        class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors <?= $p === $page ? 'bg-primary-500 text-white border border-primary-500' : 'border border-gray-200 text-gray-600 hover:bg-gray-50' ?>">
        <?= $p ?>
      </a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
      <a href="/news?page=<?= $page + 1 ?>"
        class="flex items-center gap-1 px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:border-primary-300 transition-colors">
        Next
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</section>
