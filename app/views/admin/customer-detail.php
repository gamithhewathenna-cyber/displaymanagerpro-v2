<div class="mb-5">
  <a href="/admin/customers" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    All customers
  </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
  <!-- Customer info -->
  <div class="xl:col-span-2 space-y-5">
    <div class="bg-white rounded-xl border border-gray-100 p-5">
      <div class="flex items-start justify-between mb-5">
        <div>
          <div class="text-xl font-bold text-gray-900"><?= Helpers::e($customer['name']) ?></div>
          <div class="text-sm text-gray-400"><?= Helpers::e($customer['email']) ?></div>
          <div class="text-xs text-gray-400 mt-1">Joined <?= date('M j, Y', strtotime($customer['created_at'])) ?></div>
        </div>
        <div class="flex gap-2">
          <?php if ($customer['status'] === 'active'): ?>
          <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/suspend">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
            <button class="border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Suspend</button>
          </form>
          <?php else: ?>
          <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/activate">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
            <button class="border border-green-200 text-green-600 hover:bg-green-50 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Activate</button>
          </form>
          <?php endif; ?>
          <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/reset-password">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
            <button class="border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Send Reset</button>
          </form>
          <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/delete" onsubmit="return confirm('Permanently delete this customer and all their data?')">
            <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
            <button class="border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">Delete</button>
          </form>
        </div>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div class="bg-gray-50 rounded-lg p-3 text-center">
          <div class="text-xs text-gray-400 mb-1">Plan</div>
          <div class="font-semibold text-sm text-gray-900"><?= Helpers::e($customer['plan_name'] ?? '—') ?></div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-center">
          <div class="text-xs text-gray-400 mb-1">Sub Status</div>
          <div class="font-semibold text-sm text-gray-900"><?= ucfirst($customer['sub_status'] ?? '—') ?></div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-center">
          <div class="text-xs text-gray-400 mb-1">Channels</div>
          <div class="font-semibold text-sm text-gray-900"><?= count($channels) ?></div>
        </div>
      </div>
    </div>

    <!-- Channels -->
    <?php if (!empty($channels)): ?>
    <div class="bg-white rounded-xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-900 mb-4 text-sm">Channels (<?= count($channels) ?>)</h3>
      <div class="space-y-2">
        <?php foreach ($channels as $ch): ?>
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
          <div>
            <div class="text-sm font-medium text-gray-900"><?= Helpers::e($ch['name']) ?></div>
            <div class="text-xs text-gray-400"><?= $ch['slide_count'] ?> slides · <?= ucfirst($ch['orientation']) ?></div>
          </div>
          <span class="text-xs <?= $ch['is_active']?'text-green-600':'text-gray-400' ?>"><?= $ch['is_active']?'Live':'Inactive' ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Payments -->
    <?php if (!empty($payments)): ?>
    <div class="bg-white rounded-xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-900 mb-4 text-sm">Payment History</h3>
      <div class="space-y-2">
        <?php foreach (array_slice($payments,0,5) as $p): ?>
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm">
          <span class="text-gray-600"><?= date('M j, Y', strtotime($p['created_at'])) ?></span>
          <span class="font-semibold text-gray-900">$<?= number_format($p['amount'],2) ?> <?= $p['currency'] ?></span>
          <span class="text-xs px-2 py-0.5 rounded-full <?= $p['status']==='succeeded'?'bg-green-50 text-green-700':'bg-red-50 text-red-700' ?>"><?= ucfirst($p['status']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Tickets -->
  <div>
    <?php if (!empty($tickets)): ?>
    <div class="bg-white rounded-xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-900 mb-4 text-sm">Support Tickets</h3>
      <div class="space-y-2">
        <?php foreach ($tickets as $t): ?>
        <a href="/admin/tickets/<?= $t['id'] ?>" class="block p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
          <div class="text-xs font-mono text-gray-400 mb-1"><?= $t['ticket_number'] ?></div>
          <div class="text-sm font-medium text-gray-900 truncate"><?= Helpers::e($t['subject']) ?></div>
          <span class="text-xs px-1.5 py-0.5 rounded-full <?= $t['status']==='open'?'bg-red-50 text-red-600':($t['status']==='pending'?'bg-yellow-50 text-yellow-600':'bg-gray-100 text-gray-500') ?>"><?= ucfirst($t['status']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
