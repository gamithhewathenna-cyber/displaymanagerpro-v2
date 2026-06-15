<?php $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($displayUrl); ?>

<div class="mb-6 flex items-start justify-between gap-4">
  <div>
    <a href="/channels" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      All channels
    </a>
    <h2 class="text-lg font-bold text-gray-900"><?= Helpers::e($channel['name']) ?></h2>
    <div class="flex items-center gap-3 mt-1">
      <span class="text-xs text-gray-400"><?= ucfirst($channel['orientation']) ?> · <?= $channel['slide_duration'] ?>s per slide</span>
      <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full <?= $channel['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
        <span class="w-1.5 h-1.5 rounded-full <?= $channel['is_active'] ? 'bg-green-400' : 'bg-gray-400' ?>"></span>
        <?= $channel['is_active'] ? 'Live' : 'Inactive' ?>
      </span>
    </div>
  </div>
  <div class="flex gap-2 flex-shrink-0">
    <a href="/display/<?= $channel['slug'] ?>" target="_blank" class="flex items-center gap-1.5 border border-gray-200 hover:border-gray-300 text-gray-600 text-sm font-medium px-3.5 py-2 rounded-xl transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
      Preview
    </a>
    <a href="/channels/<?= $channel['id'] ?>/edit" class="flex items-center gap-1.5 border border-gray-200 hover:border-gray-300 text-gray-600 text-sm font-medium px-3.5 py-2 rounded-xl transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      Settings
    </a>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

  <!-- Slides Panel -->
  <div class="xl:col-span-2 space-y-4">

    <!-- Add slide -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900 text-sm">Add slides from your media library</h3>
        <span id="slide-limit-badge" class="text-xs font-medium px-2.5 py-1 rounded-full <?= count($slides) >= $maxSlides ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' ?>">
          <span id="slide-current"><?= count($slides) ?></span> / <?= $maxSlides ?> slides
        </span>
      </div>
      <?php if (count($slides) >= $maxSlides): ?>
      <div id="slide-limit-warning" class="mb-3 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 text-xs px-3 py-2.5 rounded-xl">
        <svg class="w-4 h-4 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        Slide limit reached (<?= $maxSlides ?>). Remove a slide to add another, or upgrade your plan.
      </div>
      <?php else: ?>
      <div id="slide-limit-warning" class="hidden mb-3 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-800 text-xs px-3 py-2.5 rounded-xl">
        <svg class="w-4 h-4 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        Slide limit reached (<?= $maxSlides ?>). Remove a slide to add another, or upgrade your plan.
      </div>
      <?php endif; ?>
      <div id="media-grid" class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-48 overflow-y-auto mb-4 <?= count($slides) >= $maxSlides ? 'opacity-50 pointer-events-none' : '' ?>" id="media-grid-wrap">
        <?php foreach ($media as $m): ?>
        <div onclick="addSlide(<?= $m['id'] ?>, this)"
          class="aspect-square rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-primary-400 transition-all group relative"
          title="<?= Helpers::e($m['original_name']) ?>">
          <img src="<?= Helpers::e($m['public_url']) ?>" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-primary-500/0 group-hover:bg-primary-500/20 transition-all flex items-center justify-center">
            <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($media)): ?>
        <div class="col-span-6 text-center py-6 text-gray-400 text-sm">
          No media yet. <a href="/media" class="text-primary-600 font-medium">Upload images first →</a>
        </div>
        <?php endif; ?>
      </div>
      <a href="/media" class="text-xs text-primary-600 hover:text-primary-700 font-medium">+ Upload more images to media library</a>
    </div>

    <!-- Current slides -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900 text-sm">Slideshow order <span class="text-gray-400 font-normal">(drag to reorder)</span></h3>
      </div>

      <?php if (empty($slides)): ?>
      <div class="text-center py-12 text-gray-400">
        <div class="text-4xl mb-3">🖼</div>
        <div class="text-sm">Click images above to add them to this channel</div>
      </div>
      <?php else: ?>
      <div id="slide-list" class="space-y-2">
        <?php foreach ($slides as $i => $slide): ?>
        <div class="slide-item flex items-center gap-3 p-3 bg-gray-50 rounded-xl group cursor-grab active:cursor-grabbing" data-id="<?= $slide['id'] ?>">
          <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          <div class="w-14 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-200">
            <img src="<?= Helpers::e($slide['public_url']) ?>" class="w-full h-full object-cover">
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-medium text-gray-700 truncate"><?= Helpers::e($slide['original_name']) ?></div>
            <div class="text-xs text-gray-400">Slide <?= $i + 1 ?></div>
          </div>
          <button onclick="removeSlide(<?= $slide['id'] ?>, this)" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right Panel: QR / URL -->
  <div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-900 mb-4 text-sm">Display URL</h3>
      <div class="bg-gray-50 rounded-xl p-3 mb-4">
        <div class="text-xs font-mono text-gray-600 break-all"><?= Helpers::e($displayUrl) ?></div>
      </div>
      <div class="space-y-2">
        <button onclick="navigator.clipboard.writeText('<?= Helpers::e($displayUrl) ?>').then(()=>{this.textContent='Copied!';setTimeout(()=>this.textContent='Copy URL',2000)})"
          class="w-full flex items-center justify-center gap-2 border border-gray-200 hover:border-primary-300 text-gray-700 text-sm font-medium py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
          Copy URL
        </button>
        <a href="/display/<?= $channel['slug'] ?>" target="_blank"
          class="w-full flex items-center justify-center gap-2 border border-gray-200 hover:border-primary-300 text-gray-700 text-sm font-medium py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          Open on TV
        </a>
      </div>
    </div>

    <!-- QR Code -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
      <h3 class="font-semibold text-gray-900 mb-4 text-sm">QR Code</h3>
      <div class="flex justify-center mb-4">
        <img src="<?= Helpers::e($qrUrl) ?>" alt="QR Code" class="w-40 h-40 rounded-xl">
      </div>
      <div class="space-y-2">
        <a href="<?= Helpers::e($qrUrl) ?>" download="channel-qr.png"
          class="w-full flex items-center justify-center gap-2 border border-gray-200 hover:border-primary-300 text-gray-700 text-sm font-medium py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          Download QR
        </a>
      </div>
    </div>

    <!-- Danger zone -->
    <div class="bg-white rounded-2xl border border-red-100 p-5">
      <h3 class="font-semibold text-red-700 mb-3 text-sm">Danger Zone</h3>
      <form method="POST" action="/channels/<?= $channel['id'] ?>/delete" onsubmit="return confirm('Delete this channel and all its slides? This cannot be undone.')">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit" class="w-full flex items-center justify-center gap-2 border border-red-200 hover:bg-red-50 text-red-600 text-sm font-medium py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          Delete Channel
        </button>
      </form>
    </div>
  </div>
</div>

<script>
const channelId = <?= $channel['id'] ?>;
const csrf = '<?= Csrf::token() ?>';
const maxSlides = <?= $maxSlides ?>;
let slideCount = <?= count($slides) ?>;

function updateLimitUI() {
  const badge    = document.getElementById('slide-limit-badge');
  const warning  = document.getElementById('slide-limit-warning');
  const grid     = document.getElementById('media-grid');
  const current  = document.getElementById('slide-current');
  const atLimit  = slideCount >= maxSlides;

  if (current) current.textContent = slideCount;
  if (badge) {
    badge.className = 'text-xs font-medium px-2.5 py-1 rounded-full ' + (atLimit ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500');
  }
  if (warning) warning.classList.toggle('hidden', !atLimit);
  if (grid) {
    grid.classList.toggle('opacity-50', atLimit);
    grid.classList.toggle('pointer-events-none', atLimit);
  }
}

async function addSlide(mediaId, el) {
  if (slideCount >= maxSlides) {
    alert(`This channel has reached its limit of ${maxSlides} slides. Remove a slide or upgrade your plan.`);
    return;
  }
  el.style.opacity = '0.5';
  const r = await fetch(`/channels/${channelId}/slides`, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf_token=${encodeURIComponent(csrf)}&media_id=${mediaId}`
  });
  const d = await r.json();
  if (d.success) { location.reload(); }
  else { el.style.opacity='1'; alert(d.error || 'Failed to add slide'); }
}

async function removeSlide(slideId, btn) {
  if (!confirm('Remove this slide from the channel?')) return;
  btn.disabled = true;
  const r = await fetch(`/channels/${channelId}/slides/${slideId}/delete`, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf_token=${encodeURIComponent(csrf)}`
  });
  const d = await r.json();
  if (d.success) {
    btn.closest('.slide-item').remove();
    slideCount = Math.max(0, slideCount - 1);
    updateLimitUI();
  } else { btn.disabled=false; alert(d.error || 'Failed'); }
}

// Simple drag-to-reorder
let dragEl = null;
document.querySelectorAll('.slide-item').forEach(el => {
  el.addEventListener('dragstart', e => { dragEl = el; el.classList.add('opacity-50'); e.dataTransfer.effectAllowed = 'move'; });
  el.addEventListener('dragend', () => { el.classList.remove('opacity-50'); saveOrder(); });
  el.addEventListener('dragover', e => { e.preventDefault(); const list = el.parentNode; list.insertBefore(dragEl, el); });
  el.setAttribute('draggable', true);
});

async function saveOrder() {
  const ids = [...document.querySelectorAll('.slide-item')].map(el => el.dataset.id);
  await fetch(`/channels/${channelId}/reorder`, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf_token=${encodeURIComponent(csrf)}&order=${encodeURIComponent(JSON.stringify(ids))}`
  });
}
</script>
