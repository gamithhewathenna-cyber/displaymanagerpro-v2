<?php // admin/blog/index.php ?>
<div class="flex items-center justify-between mb-5">
  <div>
    <h2 class="font-semibold text-gray-900">News & Updates</h2>
    <p class="text-sm text-gray-400 mt-0.5"><?= $total ?> article<?= $total !== 1 ? 's' : '' ?> total</p>
  </div>
  <a href="/admin/blog/create" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-colors">+ New Article</a>
</div>

<?php if (empty($articles)): ?>
<div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
  <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6"/></svg>
  <p class="text-gray-400 text-sm mb-3">No articles yet.</p>
  <a href="/admin/blog/create" class="text-primary-600 font-medium text-sm">Write your first article →</a>
</div>
<?php else: ?>
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
  <table class="w-full text-sm">
    <thead>
      <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wide">
        <th class="text-left px-5 py-3">Title</th>
        <th class="text-left px-5 py-3 hidden md:table-cell">Category</th>
        <th class="text-left px-5 py-3 hidden lg:table-cell">Author</th>
        <th class="text-left px-5 py-3">Status</th>
        <th class="text-left px-5 py-3 hidden md:table-cell">Published</th>
        <th class="text-left px-5 py-3 hidden sm:table-cell">Views</th>
        <th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($articles as $a): ?>
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-5 py-3.5">
          <div class="font-medium text-gray-900 leading-snug"><?= Helpers::e($a['title']) ?></div>
          <div class="text-xs text-gray-400 mt-0.5 font-mono">/news/<?= Helpers::e($a['slug']) ?></div>
        </td>
        <td class="px-5 py-3.5 hidden md:table-cell text-gray-500"><?= $a['category'] ? Helpers::e($a['category']) : '—' ?></td>
        <td class="px-5 py-3.5 hidden lg:table-cell text-gray-500"><?= Helpers::e($a['author_name'] ?? $a['author_display'] ?? '—') ?></td>
        <td class="px-5 py-3.5">
          <?php if ($a['status'] === 'published'): ?>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
              <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Published
            </span>
          <?php else: ?>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
              <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Draft
            </span>
          <?php endif; ?>
        </td>
        <td class="px-5 py-3.5 hidden md:table-cell text-gray-400 text-xs">
          <?= $a['published_at'] ? date('M j, Y', strtotime($a['published_at'])) : '—' ?>
        </td>
        <td class="px-5 py-3.5 hidden sm:table-cell text-gray-400"><?= number_format((int)$a['view_count']) ?></td>
        <td class="px-5 py-3.5">
          <div class="flex items-center gap-2 justify-end">
            <?php if ($a['status'] === 'published'): ?>
            <a href="/news/<?= Helpers::e($a['slug']) ?>" target="_blank" class="text-gray-400 hover:text-primary-600 transition-colors" title="View live">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <?php endif; ?>
            <a href="/admin/blog/<?= $a['id'] ?>/edit" class="border border-gray-200 hover:border-primary-300 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Edit</a>
            <form method="POST" action="/admin/blog/<?= $a['id'] ?>/delete" onsubmit="return confirm('Delete this article? This cannot be undone.')">
              <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
              <button type="submit" class="border border-red-200 hover:bg-red-50 text-red-500 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="flex items-center justify-between mt-5 text-sm">
  <span class="text-gray-400">Page <?= $page ?> of <?= $totalPages ?></span>
  <div class="flex gap-2">
    <?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>" class="border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">← Prev</a>
    <?php endif; ?>
    <?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>" class="border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">Next →</a>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>
