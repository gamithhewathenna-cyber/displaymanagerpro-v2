<?php
$trialDaysLeft = Subscription::trialDaysLeft($sub);
$isTrialing    = Subscription::isTrialing($sub);
$isActive      = Subscription::isActive($sub);
$screenCount   = count($channels);
$storagePercent = $maxStorage > 0 ? min(100, round(($storageUsed / $maxStorage) * 100)) : 0;
?>

<!-- Trial / Status Banner -->
<?php if ($isTrialing && $trialDaysLeft > 0): ?>
<div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6 flex items-center justify-between">
  <div class="flex items-center gap-3">
    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
      <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
      <div class="font-semibold text-indigo-900 text-sm"><?= $trialDaysLeft ?> days left in your free trial</div>
      <div class="text-indigo-600 text-xs">Add a payment method to keep your screens running after the trial.</div>
    </div>
  </div>
  <a href="/billing" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex-shrink-0">Activate Plan</a>
</div>
<?php elseif (!$isActive): ?>
<div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center justify-between">
  <div class="flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    <div class="font-semibold text-red-900 text-sm">Your subscription has expired. Screens are paused.</div>
  </div>
  <a href="/billing" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex-shrink-0">Reactivate</a>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
  <?php
    $stats = [
      ['Plan', Helpers::e($sub['plan_name'] ?? 'None'), 'Subscription', '#6366f1', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
      ['Screens', "$screenCount / $maxScreens", 'TV Channels', '#10b981', 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
      ['Storage Used', Helpers::formatBytes($storageUsed), 'Of '.Helpers::formatBytes($maxStorage), '#f59e0b', 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
      ['Status', $isTrialing ? 'Trial' : ($isActive ? 'Active' : 'Expired'), $isTrialing ? "$trialDaysLeft days left" : ($isActive ? 'Subscription active' : 'Renew to continue'), $isActive ? '#10b981' : '#ef4444', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    foreach ($stats as [$label, $value, $sub2, $color, $icon]):
  ?>
  <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-start gap-4">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:<?= $color ?>18">
      <svg class="w-5 h-5" style="color:<?= $color ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $icon ?>"/></svg>
    </div>
    <div>
      <div class="text-xs text-gray-400 font-medium mb-0.5"><?= $label ?></div>
      <div class="text-xl font-bold text-gray-900 leading-tight"><?= $value ?></div>
      <div class="text-xs text-gray-400 mt-0.5"><?= $sub2 ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Channels -->
<div class="bg-white rounded-2xl border border-gray-100 mb-6">
  <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
    <h2 class="font-semibold text-gray-900">Your TV Channels</h2>
    <?php if ($screenCount < $maxScreens): ?>
    <a href="/channels/create" class="bg-primary-500 hover:bg-primary-600 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-colors">+ New Channel</a>
    <?php endif; ?>
  </div>
  <?php if (empty($channels)): ?>
  <div class="text-center py-16">
    <div class="text-5xl mb-4">📺</div>
    <div class="font-semibold text-gray-700 mb-1">No channels yet</div>
    <div class="text-gray-400 text-sm mb-5">Create your first channel and start displaying content on your screens.</div>
    <a href="/channels/create" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors">Create First Channel</a>
  </div>
  <?php else: ?>
  <div class="divide-y divide-gray-50">
    <?php foreach ($channels as $ch): ?>
    <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
      <div class="w-9 h-9 rounded-xl bg-<?= $ch['is_active'] ? 'indigo' : 'gray' ?>-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-<?= $ch['is_active'] ? 'indigo' : 'gray' ?>-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <div class="flex-1 min-w-0">
        <div class="font-semibold text-sm text-gray-900"><?= Helpers::e($ch['name']) ?></div>
        <div class="text-xs text-gray-400"><?= $ch['slide_count'] ?> slide<?= $ch['slide_count'] != 1 ? 's' : '' ?> · <?= ucfirst($ch['orientation']) ?></div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full <?= $ch['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
          <span class="w-1.5 h-1.5 rounded-full <?= $ch['is_active'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
          <?= $ch['is_active'] ? 'Live' : 'Inactive' ?>
        </span>
        <a href="/channels/<?= $ch['id'] ?>" class="text-xs text-primary-600 hover:text-primary-700 font-medium px-2.5 py-1 hover:bg-primary-50 rounded-lg transition-colors">Manage →</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Storage bar -->
<div class="bg-white rounded-2xl border border-gray-100 p-5">
  <div class="flex items-center justify-between mb-3">
    <div class="font-medium text-sm text-gray-700">Storage used</div>
    <div class="text-xs text-gray-400"><?= Helpers::formatBytes($storageUsed) ?> of <?= Helpers::formatBytes($maxStorage) ?></div>
  </div>
  <div class="w-full bg-gray-100 rounded-full h-2">
    <div class="h-2 rounded-full transition-all <?= $storagePercent > 85 ? 'bg-red-500' : 'bg-primary-500' ?>" style="width:<?= $storagePercent ?>%"></div>
  </div>
  <?php if ($storagePercent > 85): ?>
  <p class="text-xs text-red-600 mt-2">Storage almost full. <a href="/billing" class="underline">Upgrade your plan</a> for more space.</p>
  <?php endif; ?>
</div>
