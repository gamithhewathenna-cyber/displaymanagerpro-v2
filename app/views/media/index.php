<?php $storagePercent = $maxStorage > 0 ? min(100, round(($storageUsed / $maxStorage) * 100)) : 0; ?>

<!-- Cropper.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<!-- Upload Zone -->
<div id="drop-zone" class="border-2 border-dashed border-gray-200 hover:border-primary-400 rounded-2xl p-10 text-center mb-6 transition-all cursor-pointer bg-white" onclick="document.getElementById('file-input').click()">
  <div class="text-4xl mb-3">☁️</div>
  <div class="font-semibold text-gray-700 mb-1">Drop images here or click to upload</div>
  <div class="text-sm text-gray-400 mb-4">JPG, PNG, WEBP · Max 1MB per file</div>
  <div class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
    Choose Images
  </div>
  <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
</div>

<!-- Upload progress -->
<div id="upload-status" class="hidden mb-6 bg-white rounded-2xl border border-gray-100 p-5">
  <div class="font-medium text-sm text-gray-700 mb-3">Uploading...</div>
  <div id="upload-items" class="space-y-2"></div>
</div>

<!-- Storage + stats bar -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
  <div class="flex items-center gap-4">
    <h3 class="font-semibold text-gray-900">Media Library</h3>
    <span class="text-sm text-gray-400"><?= count($media) ?> image<?= count($media) != 1 ? 's' : '' ?></span>
  </div>
  <div class="flex items-center gap-2 text-xs text-gray-400">
    <div class="w-24 sm:w-32 bg-gray-100 rounded-full h-1.5">
      <div class="h-1.5 rounded-full <?= $storagePercent > 85 ? 'bg-red-400' : 'bg-primary-400' ?>" style="width:<?= $storagePercent ?>%"></div>
    </div>
    <span><?= Helpers::formatBytes($storageUsed) ?> used</span>
  </div>
</div>

<!-- Media Grid -->
<?php if (empty($media)): ?>
<div class="bg-white rounded-2xl border border-gray-100 text-center py-20">
  <div class="text-5xl mb-4">🖼️</div>
  <div class="font-semibold text-gray-700 mb-2">No images yet</div>
  <div class="text-gray-400 text-sm">Upload your menu boards, specials, or promotional images above.</div>
</div>
<?php else: ?>
<div id="media-grid" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
  <?php foreach ($media as $m): ?>
  <div class="group relative aspect-square rounded-xl overflow-hidden bg-gray-100 border-2 border-transparent hover:border-primary-400 transition-all" data-id="<?= $m['id'] ?>">
    <img src="<?= Helpers::e($m['public_url']) ?>" alt="<?= Helpers::e($m['original_name']) ?>" class="w-full h-full object-cover" data-media-img>
    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
      <button onclick="openCropper(<?= $m['id'] ?>, this)"
        class="bg-white hover:bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V3h4M17 3h4v4M21 17v4h-4M7 21H3v-4M7 7l10 10"/></svg>
        Crop
      </button>
      <button onclick="deleteMedia(<?= $m['id'] ?>, this)"
        class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
        Delete
      </button>
    </div>
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-2 opacity-0 group-hover:opacity-100 transition-opacity">
      <div class="text-white text-xs truncate"><?= Helpers::e($m['original_name']) ?></div>
      <div class="text-gray-300 text-xs"><?= Helpers::formatBytes($m['file_size']) ?><?= $m['width'] ? " · {$m['width']}×{$m['height']}" : '' ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ── Crop Modal ──────────────────────────────────────────────────────────── -->
<div id="crop-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeCropper()"></div>

  <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
      <div>
        <h3 class="font-semibold text-gray-900">Crop &amp; Resize Image</h3>
        <p class="text-xs text-gray-400 mt-0.5">Select the area to keep · Output will be saved as <strong>1920 × 1080</strong></p>
      </div>
      <button onclick="closeCropper()" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-100">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Cropper canvas area -->
    <div class="flex-1 overflow-hidden bg-gray-900 flex items-center justify-center" style="min-height:0;">
      <div class="w-full h-full" style="max-height:calc(90vh - 160px);">
        <img id="crop-image" src="" alt="crop" class="block max-w-full max-h-full" style="display:none;">
      </div>
    </div>

    <!-- Toolbar -->
    <div class="flex-shrink-0 px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
      <!-- Rotation controls -->
      <div class="flex items-center gap-2">
        <span class="text-xs text-gray-500 font-medium">Rotate:</span>
        <button onclick="cropperInstance && cropperInstance.rotate(-90)" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors" title="Rotate left">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
        </button>
        <button onclick="cropperInstance && cropperInstance.rotate(90)" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors" title="Rotate right">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
        </button>
        <button onclick="cropperInstance && cropperInstance.reset()" class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium transition-colors">Reset</button>
      </div>

      <!-- Output info + save -->
      <div class="flex items-center gap-3">
        <span class="text-xs text-gray-400 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg font-mono">Output: 1920 × 1080 px</span>
        <button onclick="closeCropper()" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cancel</button>
        <button id="crop-save-btn" onclick="saveCrop()"
          class="px-5 py-2 rounded-xl text-sm font-semibold bg-primary-500 hover:bg-primary-600 text-white transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Crop to 1920 × 1080
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const csrf = '<?= Csrf::token() ?>';
let cropperInstance = null;
let cropMediaId     = null;

// ── Drag & drop upload ────────────────────────────────────────────────────
const zone = document.getElementById('drop-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-primary-400','bg-primary-50'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-primary-400','bg-primary-50'));
zone.addEventListener('drop', e => { e.preventDefault(); zone.classList.remove('border-primary-400','bg-primary-50'); handleFiles(e.dataTransfer.files); });
document.getElementById('file-input').addEventListener('change', e => handleFiles(e.target.files));

async function handleFiles(files) {
  const status = document.getElementById('upload-status');
  const items  = document.getElementById('upload-items');
  status.classList.remove('hidden');
  items.innerHTML = '';

  const formData = new FormData();
  formData.append('_csrf_token', csrf);
  let validCount = 0;

  for (const file of files) {
    if (file.size > 1048576) {
      addUploadItem(items, file.name, 'error', `Too large (${(file.size/1024).toFixed(0)}KB). Max 1MB.`);
      continue;
    }
    if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
      addUploadItem(items, file.name, 'error', 'Not a supported format (JPG, PNG, WEBP only).');
      continue;
    }
    formData.append('files[]', file);
    validCount++;
  }

  if (validCount === 0) return;

  try {
    const r = await fetch('/media/upload', { method:'POST', body:formData });
    const d = await r.json();
    d.uploaded?.forEach(f => { addUploadItem(items, f.name, 'success', `${f.size} · ${f.width ?? '?'}×${f.height ?? '?'}`); });
    d.errors?.forEach(e => { addUploadItem(items, e, 'error', ''); });
    if (d.uploaded?.length > 0) setTimeout(() => location.reload(), 1200);
  } catch(e) {
    addUploadItem(items, 'Upload failed', 'error', e.message);
  }
}

function addUploadItem(container, name, type, detail) {
  const div = document.createElement('div');
  div.className = `flex items-center gap-3 text-sm p-2.5 rounded-lg ${type==='success'?'bg-green-50 text-green-800':'bg-red-50 text-red-800'}`;
  div.innerHTML = `<span>${type==='success'?'✓':'✗'}</span><span class="flex-1 truncate font-medium">${name}</span><span class="text-xs opacity-60">${detail}</span>`;
  container.appendChild(div);
}

async function deleteMedia(id, btn) {
  if (!confirm('Delete this image? If it\'s used in a channel, you\'ll need to remove it from the channel first.')) return;
  btn.disabled = true;
  const r = await fetch(`/media/${id}/delete`, {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:`_csrf_token=${encodeURIComponent(csrf)}`
  });
  const d = await r.json();
  if (d.success) {
    btn.closest('[data-id]').remove();
  } else {
    btn.disabled = false;
    alert(d.error || 'Could not delete image.');
  }
}

// ── Cropper ───────────────────────────────────────────────────────────────
function openCropper(id, btn) {
  const card = btn.closest('[data-id]');
  const src  = card.querySelector('[data-media-img]').src.split('?')[0];
  cropMediaId = id;

  const modal = document.getElementById('crop-modal');
  const img   = document.getElementById('crop-image');

  img.src = src + '?v=' + Date.now();
  img.style.display = 'block';
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.body.style.overflow = 'hidden';

  img.onload = () => {
    if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
    cropperInstance = new Cropper(img, {
      aspectRatio: 16 / 9,
      viewMode: 1,
      dragMode: 'move',
      autoCropArea: 1,
      responsive: true,
      restore: false,
      guides: true,
      center: true,
      highlight: true,
      cropBoxMovable: true,
      cropBoxResizable: true,
      toggleDragModeOnDblclick: false,
    });
  };
}

function closeCropper() {
  if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
  const modal = document.getElementById('crop-modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.body.style.overflow = '';
  document.getElementById('crop-image').style.display = 'none';
  cropMediaId = null;
}

async function saveCrop() {
  if (!cropperInstance || !cropMediaId) return;

  const btn = document.getElementById('crop-save-btn');
  btn.disabled = true;
  btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving…';

  const data = cropperInstance.getData(true); // rounded integers in original image coords

  const body = new URLSearchParams({
    _csrf_token: csrf,
    x:      data.x,
    y:      data.y,
    width:  data.width,
    height: data.height,
    rotate: data.rotate ?? 0,
  });

  try {
    const r = await fetch(`/media/${cropMediaId}/crop`, { method:'POST', body });
    const d = await r.json();

    if (d.success) {
      // Refresh the image in the grid (cache-bust)
      const card = document.querySelector(`[data-id="${cropMediaId}"]`);
      if (card) {
        const img = card.querySelector('[data-media-img]');
        const base = img.src.split('?')[0];
        img.src = base + '?v=' + Date.now();
      }
      closeCropper();
    } else {
      alert(d.error || 'Crop failed.');
    }
  } catch(e) {
    alert('Network error: ' + e.message);
  }

  btn.disabled = false;
  btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Crop to 1920 × 1080';
}

// Close on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCropper(); });
</script>
