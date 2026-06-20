<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="font-semibold text-gray-900 text-lg">Blog Posts</h2>
    <p class="text-sm text-gray-400 mt-0.5"><?= $total ?> post<?= $total !== 1 ? 's' : '' ?> total</p>
  </div>
  <a href="/admin/blog/create" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-colors">+ New Post</a>
</div>

<?php if (empty($posts)): ?>
<div class="bg-white rounded-2xl border border-gray-100 p-14 text-center">
  <div class="text-5xl mb-4">✍️</div>
  <p class="text-gray-400 text-sm">No posts yet.</p>
  <a href="/admin/blog/create" class="inline-block mt-4 text-primary-600 font-medium text-sm hover:underline">Create your first post →</a>
</div>
<?php else: ?>
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
  <table class="w-full text-sm">
    <thead>
      <tr class="border-b border-gray-100 text-left">
        <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Title</th>
        <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide hidden sm:table-cell">Author</th>
        <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide hidden md:table-cell">Status</th>
        <th class="px-5 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide hidden lg:table-cell">Published</th>
        <th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($posts as $p): ?>
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-5 py-3.5">
          <div class="font-medium text-gray-900 truncate max-w-xs"><?= Helpers::e($p['title']) ?></div>
          <div class="text-xs text-gray-400 mt-0.5 font-mono truncate">/blog/<?= Helpers::e($p['slug']) ?></div>
        </td>
        <td class="px-5 py-3.5 text-gray-500 hidden sm:table-cell"><?= Helpers::e($p['author']) ?></td>
        <td class="px-5 py-3.5 hidden md:table-cell">
          <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full <?= $p['status'] === 'published' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' ?>">
            <span class="w-1.5 h-1.5 rounded-full <?= $p['status'] === 'published' ? 'bg-green-500' : 'bg-yellow-400' ?>"></span>
            <?= ucfirst($p['status']) ?>
          </span>
        </td>
        <td class="px-5 py-3.5 text-gray-400 text-xs hidden lg:table-cell">
          <?= $p['published_at'] ? date('d M Y', strtotime($p['published_at'])) : '—' ?>
        </td>
        <td class="px-5 py-3.5">
          <div class="flex items-center gap-2 justify-end">
            <?php if ($p['status'] === 'published'): ?>
            <a href="/blog/<?= Helpers::e($p['slug']) ?>" target="_blank" class="text-xs text-gray-400 hover:text-primary-600 transition-colors hidden sm:inline">View →</a>
            <?php endif; ?>
            <a href="/admin/blog/<?= $p['id'] ?>/edit" class="border border-gray-200 hover:border-primary-300 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Edit</a>
            <form method="POST" action="/admin/blog/<?= $p['id'] ?>/delete" onsubmit="return confirm('Delete this post permanently?')">
              <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Csrf::token() ?>">
              <button type="submit" class="border border-red-200 hover:bg-red-50 text-red-500 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($pages > 1): ?>
<div class="flex items-center justify-center gap-2 mt-6">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
  <a href="?page=<?= $i ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-sm <?= $i === $page ? 'bg-primary-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-primary-300' ?> transition-colors"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
