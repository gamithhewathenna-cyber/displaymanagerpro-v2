<?php $canCreate = count($channels) < $maxScreens && Subscription::isActive($sub); ?>

<div class="flex items-center justify-between mb-6">
  <div>
    <p class="text-sm text-gray-500"><?= count($channels) ?> of <?= $maxScreens ?> screen<?= $maxScreens > 1 ? 's' : '' ?> used</p>
  </div>
  <?php if ($canCreate): ?>
  <a href="/channels/create" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-colors">+ New Channel</a>
  <?php endif; ?>
</div>

<?php if (empty($channels)): ?>
<div class="bg-white rounded-2xl border border-gray-100 text-center py-20">
  <div class="text-6xl mb-5">📺</div>
  <div class="text-xl font-bold text-gray-800 mb-2">Create your first channel</div>
  <p class="text-gray-400 text-sm mb-7 max-w-sm mx-auto">Each channel maps to one TV screen. Give it a name, upload your images, then open the display URL on your TV.</p>
  <?php if ($canCreate): ?>
  <a href="/channels/create" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Create First Channel</a>
  <?php elseif (!Subscription::isActive($sub)): ?>
  <a href="/billing" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Activate Subscription</a>
  <?php endif; ?>
</div>
<?php else: ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
  <?php foreach ($channels as $ch): ?>
  <div class="bg-white rounded-2xl border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all overflow-hidden">
    <!-- Channel header -->
    <div class="flex items-center justify-between px-5 pt-5 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
          <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <div>
          <div class="font-semibold text-gray-900"><?= Helpers::e($ch['name']) ?></div>
          <div class="text-xs text-gray-400"><?= ucfirst($ch['orientation']) ?> · <?= $ch['slide_duration'] ?>s slides</div>
        </div>
      </div>
      <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full <?= $ch['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
        <span class="w-1.5 h-1.5 rounded-full <?= $ch['is_active'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
        <?= $ch['is_active'] ? 'Live' : 'Inactive' ?>
      </span>
    </div>

    <!-- Slide preview strip -->
    <div class="px-5 pb-3">
      <div class="text-xs text-gray-400 mb-2"><?= $ch['slide_count'] ?> slide<?= $ch['slide_count'] != 1 ? 's' : '' ?></div>
      <div class="flex gap-1.5 h-14">
        <?php for ($i = 0; $i < min(5, $ch['slide_count']); $i++): ?>
        <div class="flex-1 bg-gray-100 rounded-lg"></div>
        <?php endfor; ?>
        <?php if ($ch['slide_count'] === 0): ?>
        <div class="flex-1 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center">
          <span class="text-gray-300 text-xl">+</span>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Display URL -->
    <div class="px-5 pb-4">
      <div class="bg-gray-50 rounded-lg px-3 py-2 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        <span class="text-xs text-gray-500 font-mono truncate">/display/<?= Helpers::e($ch['slug']) ?></span>
        <button onclick="copyUrl('<?= Helpers::e(Helpers::displayUrl($ch['slug'])) ?>')" class="ml-auto text-xs text-indigo-600 hover:text-indigo-700 font-medium flex-shrink-0">Copy</button>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex border-t border-gray-50">
      <a href="/channels/<?= $ch['id'] ?>" class="flex-1 flex items-center justify-center gap-1.5 py-3 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Manage
      </a>
      <a href="/display/<?= $ch['slug'] ?>" target="_blank" class="flex-1 flex items-center justify-center gap-1.5 py-3 text-xs font-semibold text-gray-500 hover:bg-gray-50 transition-colors border-l border-gray-50">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Preview
      </a>
      <a href="/channels/<?= $ch['id'] ?>/edit" class="flex-1 flex items-center justify-center gap-1.5 py-3 text-xs font-semibold text-gray-500 hover:bg-gray-50 transition-colors border-l border-gray-50">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if ($canCreate): ?>
  <a href="/channels/create" class="bg-white border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center py-14 hover:border-indigo-300 hover:bg-indigo-50/30 transition-all group">
    <div class="w-12 h-12 rounded-xl bg-gray-100 group-hover:bg-indigo-100 flex items-center justify-center mb-3 transition-colors">
      <svg class="w-6 h-6 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    </div>
    <div class="font-semibold text-gray-500 group-hover:text-indigo-600 text-sm transition-colors">Add another screen</div>
    <div class="text-xs text-gray-400 mt-1"><?= $maxScreens - count($channels) ?> slot<?= ($maxScreens - count($channels)) > 1 ? 's' : '' ?> remaining</div>
  </a>
  <?php endif; ?>
</div>
<?php endif; ?>

<script>
function copyUrl(url) {
  navigator.clipboard.writeText(url).then(() => {
    const btn = event.target;
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 2000);
  });
}
</script>
