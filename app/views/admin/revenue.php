<div class="mb-5">
  <div class="bg-white rounded-xl border border-gray-100 px-5 py-3 inline-flex items-center gap-3">
    <div class="text-sm text-gray-500">Total Revenue</div>
    <div class="text-2xl font-bold text-gray-900">$<?= number_format($total, 2) ?> USD</div>
  </div>
</div>
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto">
  <table class="w-full text-sm min-w-[520px]">
    <thead><tr class="text-xs text-gray-400 bg-gray-50 border-b border-gray-100">
      <th class="px-4 py-3 text-left font-medium">Date</th>
      <th class="px-4 py-3 text-left font-medium">Customer</th>
      <th class="px-4 py-3 text-left font-medium hidden md:table-cell">Description</th>
      <th class="px-4 py-3 text-right font-medium">Amount</th>
      <th class="px-4 py-3 text-right font-medium hidden sm:table-cell">Invoice</th>
    </tr></thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($payments as $p): ?>
      <tr class="hover:bg-gray-50/50">
        <td class="px-4 py-3 text-gray-500 text-xs"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
        <td class="px-4 py-3">
          <div class="text-gray-900"><?= Helpers::e($p['user_name']) ?></div>
          <div class="text-xs text-gray-400"><?= Helpers::e($p['user_email']) ?></div>
        </td>
        <td class="px-4 py-3 text-gray-600 hidden md:table-cell"><?= Helpers::e($p['description'] ?? 'Subscription') ?></td>
        <td class="px-4 py-3 text-right font-semibold text-gray-900">$<?= number_format($p['amount'],2) ?> <?= $p['currency'] ?></td>
        <td class="px-4 py-3 text-right hidden sm:table-cell">
          <?php if ($p['invoice_url']): ?>
          <a href="<?= Helpers::e($p['invoice_url']) ?>" target="_blank" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View</a>
          <?php else: ?><span class="text-gray-300 text-xs">—</span><?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($payments)): ?><tr><td colspan="5" class="text-center py-10 text-gray-400 text-sm">No payments yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
