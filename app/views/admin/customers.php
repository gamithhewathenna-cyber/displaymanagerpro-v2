<!-- customers.php -->
<?php if (basename(__FILE__) === 'customers.php'): ?>
<div class="flex items-center gap-4 mb-5">
  <form method="GET" class="w-full sm:max-w-sm">
    <input type="text" name="q" value="<?= Helpers::e($search) ?>" placeholder="Search customers…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
  </form>
</div>
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
  <div class="overflow-x-auto">
  <table class="w-full text-sm min-w-[540px]">
    <thead>
      <tr class="text-xs text-gray-400 bg-gray-50 border-b border-gray-100">
        <th class="px-4 py-3 text-left font-medium">Customer</th>
        <th class="px-4 py-3 text-left font-medium hidden sm:table-cell">Plan</th>
        <th class="px-4 py-3 text-left font-medium">Status</th>
        <th class="px-4 py-3 text-left font-medium hidden md:table-cell">Account</th>
        <th class="px-4 py-3 text-left font-medium hidden lg:table-cell">Joined</th>
        <th class="px-4 py-3"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($customers as $c): ?>
      <tr class="hover:bg-gray-50/40">
        <td class="px-4 py-3">
          <div class="font-medium text-gray-900"><?= Helpers::e($c['name']) ?></div>
          <div class="text-xs text-gray-400"><?= Helpers::e($c['email']) ?></div>
        </td>
        <td class="px-4 py-3 text-gray-600 text-xs hidden sm:table-cell"><?= Helpers::e($c['plan_name'] ?? '—') ?></td>
        <td class="px-4 py-3">
          <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full <?= $c['sub_status']==='active'?'bg-green-50 text-green-700':($c['sub_status']==='trialing'?'bg-blue-50 text-blue-700':'bg-gray-100 text-gray-500') ?>">
            <?= ucfirst($c['sub_status'] ?? 'none') ?>
          </span>
        </td>
        <td class="px-4 py-3 hidden md:table-cell">
          <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full <?= $c['status']==='active'?'bg-green-50 text-green-700':($c['status']==='suspended'?'bg-red-50 text-red-700':'bg-yellow-50 text-yellow-700') ?>">
            <?= ucfirst($c['status']) ?>
          </span>
        </td>
        <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell"><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
        <td class="px-4 py-3 text-right">
          <a href="/admin/customers/<?= $c['id'] ?>" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View →</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($customers)): ?>
      <tr><td colspan="6" class="text-center py-10 text-gray-400 text-sm">No customers found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>
