<?php
$now      = time();
$isEdit   = !empty($editing);
$formAction = $isEdit
    ? '/channels/' . $channel['id'] . '/schedules/' . $editing['id']
    : '/channels/' . $channel['id'] . '/schedules';

// Decode editing media_ids for pre-check
$editMediaIds = $isEdit ? (json_decode($editing['media_ids'], true) ?: []) : [];

// Compute status of each schedule
function scheduleStatus(array $s): string {
    $now = time();
    $start = strtotime($s['starts_at']);
    $end   = $s['ends_at'] ? strtotime($s['ends_at']) : null;
    if ($start > $now) return 'upcoming';
    if ($end !== null && $end <= $now) return 'expired';
    return 'active';
}
?>

<!-- Header -->
<div class="flex items-center gap-3 mb-6">
  <a href="/channels/<?= $channel['id'] ?>" class="text-gray-400 hover:text-gray-600 transition-colors text-sm inline-flex items-center gap-1">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    <?= Helpers::e($channel['name']) ?>
  </a>
  <span class="text-gray-200">/</span>
  <h2 class="font-semibold text-gray-900">Content Schedules</h2>
  <span class="ml-1 text-xs bg-indigo-100 text-indigo-700 font-semibold px-2.5 py-0.5 rounded-full">Pro</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">

  <!-- Left: Schedule list -->
  <div class="space-y-4">

    <?php if (empty($schedules)): ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-14 text-center">
      <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <p class="text-gray-700 font-semibold text-sm mb-1">No schedules yet</p>
      <p class="text-gray-400 text-xs">Use the form to schedule content for specific dates and times.</p>
    </div>
    <?php else: ?>

    <?php foreach ($schedules as $s):
      $status    = scheduleStatus($s);
      $mediaList = json_decode($s['media_ids'], true) ?: [];
      $badgeCls  = match($status) {
        'active'   => 'bg-green-50 text-green-700',
        'upcoming' => 'bg-blue-50 text-blue-700',
        default    => 'bg-gray-100 text-gray-400',
      };
      $dotCls = match($status) {
        'active'   => 'bg-green-500',
        'upcoming' => 'bg-blue-400',
        default    => 'bg-gray-300',
      };
    ?>
    <div class="bg-white rounded-2xl border <?= $status === 'active' ? 'border-green-200' : 'border-gray-100' ?> p-5">
      <div class="flex items-start justify-between gap-3 mb-3">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full <?= $badgeCls ?>">
              <span class="w-1.5 h-1.5 rounded-full <?= $dotCls ?>"></span>
              <?= ucfirst($status) ?>
            </span>
            <?php if ($s['priority'] > 0): ?>
            <span class="text-xs text-gray-400">Priority <?= $s['priority'] ?></span>
            <?php endif; ?>
          </div>
          <h3 class="font-semibold text-gray-900 text-sm truncate"><?= Helpers::e($s['name']) ?></h3>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <a href="/channels/<?= $channel['id'] ?>/schedules?edit=<?= $s['id'] ?>#schedule-form"
            class="border border-gray-200 hover:border-indigo-300 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
            Edit
          </a>
          <form method="POST" action="/channels/<?= $channel['id'] ?>/schedules/<?= $s['id'] ?>/delete"
            onsubmit="return confirm('Delete this schedule? This cannot be undone.')">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Csrf::token() ?>">
            <button type="submit" class="border border-red-200 hover:bg-red-50 text-red-500 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
              Delete
            </button>
          </form>
        </div>
      </div>

      <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>Starts: <strong class="text-gray-700"><?= date('d M Y, H:i', strtotime($s['starts_at'])) ?></strong></span>
        </div>
        <?php if ($s['ends_at']): ?>
        <div class="flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
          <span>Ends: <strong class="text-gray-700"><?= date('d M Y, H:i', strtotime($s['ends_at'])) ?></strong></span>
        </div>
        <?php else: ?>
        <span class="text-gray-400">No end date (runs indefinitely)</span>
        <?php endif; ?>
        <span class="text-gray-400"><?= count($mediaList) ?> image<?= count($mediaList) !== 1 ? 's' : '' ?></span>
      </div>

      <!-- Slide thumbnails preview -->
      <?php if (!empty($mediaList)): ?>
      <div class="flex gap-1.5 flex-wrap">
        <?php
        $placeholders = implode(',', array_fill(0, count($mediaList), '?'));
        $previewMedia = Database::fetchAll("SELECT * FROM media WHERE id IN ($placeholders)", $mediaList);
        $previewById  = array_column($previewMedia, null, 'id');
        foreach ($mediaList as $mid):
          $pm = $previewById[$mid] ?? null;
          if (!$pm) continue;
        ?>
        <div class="w-12 h-8 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
          <img src="<?= Helpers::e($pm['public_url']) ?>" class="w-full h-full object-cover">
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

  </div>

  <!-- Right: Create / Edit form -->
  <div id="schedule-form">
    <div class="bg-white rounded-2xl border <?= $isEdit ? 'border-indigo-200' : 'border-gray-100' ?> p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-4">
        <?= $isEdit ? 'Edit Schedule' : 'New Schedule' ?>
      </h3>

      <form method="POST" action="<?= $formAction ?>" class="space-y-4">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Csrf::token() ?>">

        <!-- Name -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Schedule Name <span class="text-red-400">*</span></label>
          <input type="text" name="name" required autofocus
            value="<?= Helpers::e($editing['name'] ?? '') ?>"
            placeholder="e.g. Happy Hour Menu, Christmas Promo…"
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>

        <!-- Start date/time -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">Go Live <span class="text-red-400">*</span></label>
          <input type="datetime-local" name="starts_at" required
            value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($editing['starts_at'])) : '' ?>"
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>

        <!-- End date/time -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">
            End Date/Time
            <span class="text-gray-400 font-normal">(leave blank for no end)</span>
          </label>
          <input type="datetime-local" name="ends_at"
            value="<?= ($isEdit && $editing['ends_at']) ? date('Y-m-d\TH:i', strtotime($editing['ends_at'])) : '' ?>"
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>

        <!-- Priority -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-1">
            Priority
            <span class="text-gray-400 font-normal">(0–10, higher wins if schedules overlap)</span>
          </label>
          <input type="number" name="priority" min="0" max="10"
            value="<?= (int)($editing['priority'] ?? 0) ?>"
            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>

        <!-- Media selection -->
        <div>
          <label class="block text-xs font-medium text-gray-700 mb-2">
            Images to Show <span class="text-red-400">*</span>
            <span class="text-gray-400 font-normal">(select one or more)</span>
          </label>

          <?php if (empty($media)): ?>
          <div class="border border-dashed border-gray-200 rounded-xl p-4 text-center text-xs text-gray-400">
            No images in your library.
            <a href="/media" class="text-indigo-600 font-medium hover:underline">Upload images first →</a>
          </div>
          <?php else: ?>
          <div class="grid grid-cols-3 gap-2 max-h-56 overflow-y-auto pr-1">
            <?php foreach ($media as $m): ?>
            <?php $checked = in_array($m['id'], $editMediaIds); ?>
            <label class="relative cursor-pointer group">
              <input type="checkbox" name="media_ids[]" value="<?= $m['id'] ?>"
                <?= $checked ? 'checked' : '' ?>
                class="sr-only peer">
              <div class="aspect-square rounded-xl overflow-hidden border-2 transition-all
                peer-checked:border-indigo-500 border-transparent bg-gray-100
                group-hover:border-indigo-300">
                <img src="<?= Helpers::e($m['public_url']) ?>"
                  alt="<?= Helpers::e($m['original_name']) ?>"
                  class="w-full h-full object-cover">
              </div>
              <!-- Checkmark overlay -->
              <div class="absolute top-1 right-1 w-5 h-5 bg-indigo-500 rounded-full items-center justify-center hidden peer-checked:flex shadow-sm">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
          <p class="text-xs text-gray-400 mt-1.5">
            <a href="/media" class="text-indigo-600 font-medium hover:underline">+ Upload more images</a>
          </p>
          <?php endif; ?>
        </div>

        <div class="flex gap-2 pt-1">
          <button type="submit"
            class="flex-1 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
            <?= $isEdit ? 'Save Changes' : 'Create Schedule' ?>
          </button>
          <?php if ($isEdit): ?>
          <a href="/channels/<?= $channel['id'] ?>/schedules"
            class="border border-gray-200 hover:border-gray-300 text-gray-600 font-medium py-2.5 px-4 rounded-xl text-sm transition-colors">
            Cancel
          </a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- How it works -->
    <div class="mt-4 bg-indigo-50 rounded-2xl p-4 text-xs text-indigo-700 space-y-1.5">
      <p class="font-semibold text-indigo-800">How scheduling works</p>
      <p>When a schedule is active, your TV displays the scheduled images <strong>instead of</strong> the default channel slides.</p>
      <p>If multiple schedules overlap, the one with the highest priority wins.</p>
      <p>Your TV picks up changes automatically on its next refresh cycle.</p>
    </div>
  </div>

</div>
