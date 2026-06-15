<!-- tickets.php -->
<div class="flex gap-2 mb-5">
  <?php foreach ([''=> 'All', 'open'=>'Open','pending'=>'Pending','closed'=>'Closed'] as $s => $label): ?>
  <a href="/admin/tickets<?= $s?"?status=$s":'' ?>" class="text-xs font-medium px-3 py-1.5 rounded-lg transition-colors <?= $status===$s?'bg-primary-500 text-white':'bg-white border border-gray-200 text-gray-600 hover:border-primary-300' ?>">
    <?= $label ?>
  </a>
  <?php endforeach; ?>
</div>
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
  <table class="w-full text-sm">
    <thead>
      <tr class="text-xs text-gray-400 bg-gray-50 border-b border-gray-100">
        <th class="px-5 py-3 text-left font-medium">Ticket</th>
        <th class="px-5 py-3 text-left font-medium">Customer</th>
        <th class="px-5 py-3 text-left font-medium">Status</th>
        <th class="px-5 py-3 text-left font-medium">Updated</th>
        <th class="px-5 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($tickets as $t): ?>
      <tr class="hover:bg-gray-50/50">
        <td class="px-5 py-3">
          <div class="font-mono text-xs text-gray-400"><?= $t['ticket_number'] ?></div>
          <div class="font-medium text-gray-900"><?= Helpers::e($t['subject']) ?></div>
        </td>
        <td class="px-5 py-3">
          <div class="text-gray-900"><?= Helpers::e($t['user_name']) ?></div>
          <div class="text-xs text-gray-400"><?= Helpers::e($t['user_email']) ?></div>
        </td>
        <td class="px-5 py-3">
          <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full <?= $t['status']==='open'?'bg-red-50 text-red-700':($t['status']==='pending'?'bg-yellow-50 text-yellow-700':'bg-gray-100 text-gray-500') ?>">
            <?= ucfirst($t['status']) ?>
          </span>
        </td>
        <td class="px-5 py-3 text-gray-400 text-xs"><?= Helpers::timeAgo($t['updated_at']) ?></td>
        <td class="px-5 py-3 text-right">
          <a href="/admin/tickets/<?= $t['id'] ?>" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Reply →</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($tickets)): ?>
      <tr><td colspan="5" class="text-center py-10 text-gray-400 text-sm">No tickets found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
