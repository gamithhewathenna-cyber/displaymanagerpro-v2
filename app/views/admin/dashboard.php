<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-7">
  <?php
    $cards = [
      ['Total Customers', $totalCustomers, '#ec4899', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
      ['Active Subs', $activeSubCount, '#10b981', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
      ['Monthly Revenue', '$'.number_format($monthlyRevenue,2), '#f59e0b', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
      ['Total Screens', $totalChannels, '#8b5cf6', 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
      ['Open Tickets', $openTickets, '#ef4444', 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
      ['USD Today', '$0', '#06b6d4', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
    ];
    foreach ($cards as [$label, $value, $color, $icon]):
  ?>
  <div class="bg-white rounded-xl border border-gray-100 p-4">
    <div class="flex items-center justify-between mb-3">
      <div class="text-xs text-gray-400 font-medium"><?= $label ?></div>
      <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:<?= $color ?>18">
        <svg class="w-4 h-4" style="color:<?= $color ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $icon ?>"/></svg>
      </div>
    </div>
    <div class="text-2xl font-bold text-gray-900"><?= $value ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Recent signups -->
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
  <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
    <h2 class="font-semibold text-gray-900 text-sm">Recent Signups</h2>
    <a href="/admin/customers" class="text-xs text-primary-600 font-medium hover:text-primary-700">View all →</a>
  </div>
  <?php if (empty($recentSignups)): ?>
  <div class="text-center py-10 text-gray-400 text-sm">No customers yet.</div>
  <?php else: ?>
  <div class="overflow-x-auto">
  <table class="w-full text-sm min-w-[520px]">
    <thead>
      <tr class="text-xs text-gray-400 bg-gray-50">
        <th class="px-4 py-2.5 text-left font-medium">Customer</th>
        <th class="px-4 py-2.5 text-left font-medium hidden sm:table-cell">Plan</th>
        <th class="px-4 py-2.5 text-left font-medium">Status</th>
        <th class="px-4 py-2.5 text-left font-medium hidden md:table-cell">Joined</th>
        <th class="px-4 py-2.5"></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      <?php foreach ($recentSignups as $c): ?>
      <tr class="hover:bg-gray-50/50">
        <td class="px-4 py-3">
          <div class="font-medium text-gray-900"><?= Helpers::e($c['name']) ?></div>
          <div class="text-xs text-gray-400"><?= Helpers::e($c['email']) ?></div>
        </td>
        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell"><?= Helpers::e($c['plan_name'] ?? '—') ?></td>
        <td class="px-4 py-3">
          <span class="inline-flex text-xs font-semibold px-2 py-0.5 rounded-full
            <?= $c['sub_status']==='active'?'bg-green-50 text-green-700':
               ($c['sub_status']==='trialing'?'bg-blue-50 text-blue-700':'bg-gray-100 text-gray-500') ?>">
            <?= ucfirst($c['sub_status'] ?? 'none') ?>
          </span>
        </td>
        <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell"><?= Helpers::timeAgo($c['created_at']) ?></td>
        <td class="px-4 py-3 text-right">
          <a href="/admin/customers/<?= $c['id'] ?>" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View →</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>
