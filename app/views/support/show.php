<?php $user = Session::get('user'); $isAdmin = $user['role'] === 'admin'; ?>

<div class="max-w-3xl">
  <a href="<?= $isAdmin ? '/admin/tickets' : '/support' ?>" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Back to tickets</a>

  <div class="bg-white rounded-2xl border border-gray-100 mb-5">
    <div class="px-6 py-5 border-b border-gray-50 flex items-start justify-between gap-4">
      <div>
        <div class="font-mono text-xs text-gray-400 mb-1"><?= $ticket['ticket_number'] ?></div>
        <div class="text-lg font-bold text-gray-900"><?= Helpers::e($ticket['subject']) ?></div>
        <div class="text-sm text-gray-400 mt-1">
          Opened <?= date('M j, Y g:ia', strtotime($ticket['created_at'])) ?>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full
          <?= $ticket['status']==='open'?'bg-red-50 text-red-700':($ticket['status']==='pending'?'bg-yellow-50 text-yellow-700':'bg-gray-100 text-gray-500') ?>">
          <?= ucfirst($ticket['status']) ?>
        </span>
        <?php if ($isAdmin && $ticket['status'] !== 'closed'): ?>
        <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/close">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <button class="border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Close</button>
        </form>
        <?php elseif ($isAdmin && $ticket['status'] === 'closed'): ?>
        <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/reopen">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <button class="border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Reopen</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- Replies -->
    <div class="divide-y divide-gray-50">
      <?php foreach ($replies as $r): ?>
      <div class="px-6 py-5 <?= $r['is_admin_reply'] ? 'bg-indigo-50/40' : '' ?>">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-7 h-7 rounded-full <?= $r['is_admin_reply']?'bg-indigo-100':'bg-gray-100' ?> flex items-center justify-center text-xs font-bold <?= $r['is_admin_reply']?'text-indigo-600':'text-gray-600' ?>">
            <?= strtoupper(substr($r['author_name'], 0, 1)) ?>
          </div>
          <div class="font-medium text-sm text-gray-900"><?= Helpers::e($r['author_name']) ?></div>
          <?php if ($r['is_admin_reply']): ?>
          <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-1.5 py-0.5 rounded">Support</span>
          <?php endif; ?>
          <span class="text-xs text-gray-400 ml-auto"><?= Helpers::timeAgo($r['created_at']) ?></span>
        </div>
        <div class="text-sm text-gray-700 leading-relaxed pl-9 whitespace-pre-line"><?= Helpers::e($r['message']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Reply form -->
  <?php if ($ticket['status'] !== 'closed'): ?>
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h3 class="font-semibold text-gray-900 mb-4 text-sm">Add Reply</h3>
    <form method="POST" action="/<?= $isAdmin?'admin/tickets':'support' ?>/<?= $ticket['id'] ?>/reply" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <textarea name="message" required rows="5" placeholder="Type your reply…"
        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Send Reply</button>
    </form>
  </div>
  <?php else: ?>
  <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-center text-sm text-gray-400">
    This ticket is closed. <?php if ($isAdmin): ?>Reopen it above to reply.<?php endif; ?>
  </div>
  <?php endif; ?>
</div>
