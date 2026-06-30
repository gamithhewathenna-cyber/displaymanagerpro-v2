<!-- BLOG HERO -->
<section class="hero-gradient text-white py-16 px-4">
  <div class="max-w-4xl mx-auto text-center">
    <span class="inline-block text-xs font-semibold text-indigo-300 bg-white/10 px-3 py-1 rounded-full mb-4">Resources</span>
    <h1 class="text-4xl sm:text-5xl font-extrabold mb-4">News & Updates</h1>
    <p class="text-gray-300 text-lg max-w-2xl mx-auto">Tips, guides, and industry insights to help you get the most from your digital signage.</p>
  </div>
</section>

<!-- POSTS GRID -->
<section class="py-20 px-4 bg-white">
  <div class="max-w-6xl mx-auto">

    <?php if (empty($posts)): ?>
    <div class="text-center py-24">
      <div class="text-5xl mb-4">📝</div>
      <h2 class="text-xl font-semibold text-gray-700 mb-2">No posts yet</h2>
      <p class="text-gray-400 text-sm">Check back soon for tips, guides, and industry insights.</p>
    </div>

    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($posts as $p): ?>
      <article class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-lg hover:border-primary-100 transition-all flex flex-col">

        <?php if ($p['featured_image']): ?>
        <a href="/blog/<?= Helpers::e($p['slug']) ?>" class="block overflow-hidden" style="aspect-ratio:16/9;">
          <img src="<?= Helpers::e($p['featured_image']) ?>" alt="<?= Helpers::e($p['title']) ?>"
            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
        </a>
        <?php else: ?>
        <a href="/blog/<?= Helpers::e($p['slug']) ?>" class="flex items-center justify-center bg-gradient-to-br from-primary-50 to-indigo-100" style="aspect-ratio:16/9;">
          <svg class="w-10 h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h2"/></svg>
        </a>
        <?php endif; ?>

        <div class="p-6 flex flex-col flex-1">
          <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
            <span><?= Helpers::e($p['author']) ?></span>
            <span>·</span>
            <span><?= $p['published_at'] ? date('d M Y', strtotime($p['published_at'])) : '' ?></span>
          </div>
          <h2 class="font-bold text-gray-900 text-lg leading-snug mb-3 flex-1">
            <a href="/blog/<?= Helpers::e($p['slug']) ?>" class="hover:text-primary-600 transition-colors">
              <?= Helpers::e($p['title']) ?>
            </a>
          </h2>
          <?php if ($p['excerpt']): ?>
          <p class="text-sm text-gray-500 leading-relaxed mb-4 line-clamp-3"><?= Helpers::e($p['excerpt']) ?></p>
          <?php endif; ?>
          <a href="/blog/<?= Helpers::e($p['slug']) ?>" class="text-primary-600 font-semibold text-sm hover:text-primary-700 transition-colors mt-auto">
            Read more →
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div class="flex items-center justify-center gap-2 mt-14">
      <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>" class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-primary-300 transition-colors">← Previous</a>
      <?php endif; ?>
      <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?page=<?= $i ?>" class="w-9 h-9 rounded-xl flex items-center justify-center text-sm <?= $i === $page ? 'bg-primary-500 text-white font-semibold' : 'border border-gray-200 text-gray-600 hover:border-primary-300' ?> transition-colors"><?= $i ?></a>
      <?php endfor; ?>
      <?php if ($page < $pages): ?>
      <a href="?page=<?= $page + 1 ?>" class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:border-primary-300 transition-colors">Next →</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

  </div>
</section>


<?php require VIEWS_PATH . '/marketing/_cta.php'; ?>
