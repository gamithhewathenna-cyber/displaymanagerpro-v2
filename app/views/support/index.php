<?php // support/index.php ?>
<div class="flex items-center justify-between mb-5">
  <div class="text-sm text-gray-400"><?= count($tickets) ?> ticket<?= count($tickets)!=1?'s':'' ?></div>
  <a href="/support/create" class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">+ Open Ticket</a>
</div>

<?php if (empty($tickets)): ?>
<div class="bg-white rounded-2xl border border-gray-100 text-center py-16">
  <div class="text-5xl mb-4">🎫</div>
  <div class="font-semibold text-gray-700 mb-1">No support tickets</div>
  <div class="text-gray-400 text-sm">Need help? Open a ticket and we'll get back to you quickly.</div>
</div>
<?php else: ?>
<div class="space-y-3">
  <?php foreach ($tickets as $t): ?>
  <a href="/support/<?= $t['id'] ?>" class="block bg-white rounded-2xl border border-gray-100 hover:border-primary-200 p-5 transition-all">
    <div class="flex items-start justify-between gap-4">
      <div>
        <div class="font-mono text-xs text-gray-400 mb-1"><?= $t['ticket_number'] ?></div>
        <div class="font-semibold text-gray-900"><?= Helpers::e($t['subject']) ?></div>
        <div class="text-xs text-gray-400 mt-1">Updated <?= Helpers::timeAgo($t['updated_at']) ?></div>
      </div>
      <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full flex-shrink-0
        <?= $t['status']==='open'?'bg-red-50 text-red-700':($t['status']==='pending'?'bg-yellow-50 text-yellow-700':'bg-gray-100 text-gray-500') ?>">
        <?= ucfirst($t['status']) ?>
      </span>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>
