<?php $storagePercent = $maxStorage > 0 ? min(100, round(($storageUsed / $maxStorage) * 100)) : 0; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<!-- Upload Zone -->
<div id="drop-zone" class="border-2 border-dashed border-gray-200 hover:border-primary-400 rounded-2xl p-10 text-center mb-6 transition-all cursor-pointer bg-white" onclick="document.getElementById('file-input').click()">
  <div class="text-4xl mb-3">☁️</div>
  <div class="font-semibold text-gray-700 mb-1">Drop images here or click to upload</div>
  <div class="text-sm text-gray-400 mb-4">JPG, PNG, WEBP · Images already at 1920 × 1080 upload instantly · Others must be cropped first</div>
  <div class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
    Choose Images
  </div>
  <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
</div>

<!-- Upload progress -->
<div id="upload-status" class="hidden mb-6 bg-white rounded-2xl border border-gray-100 p-5">
  <div class="font-medium text-sm text-gray-700 mb-3" id="upload-status-label">Uploading…</div>
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
      <button onclick="openPostCropper(<?= $m['id'] ?>, this)"
        class="bg-white hover:bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V3h4M17 3h4v4M21 17v4h-4M7 21H3v-4M7 7l10 10"/></svg>
        Re-crop
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

<!-- ── Unified Crop Modal ─────────────────────────────────────────────────── -->
<div id="crop-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" id="crop-backdrop"></div>

  <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden" style="max-height:90vh;">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
      <div class="min-w-0">
        <div class="flex items-center gap-3 flex-wrap">
          <h3 class="font-semibold text-gray-900" id="crop-title">Crop Image</h3>
          <span id="crop-queue-badge" class="hidden text-xs bg-primary-100 text-primary-700 font-bold px-2.5 py-0.5 rounded-full"></span>
        </div>
        <p class="text-xs text-gray-400 mt-0.5 truncate" id="crop-subtitle">Select the crop area · Output: 1920 × 1080 px</p>
      </div>
      <button id="crop-close-btn" onclick="closeCropper()" class="flex-shrink-0 ml-3 text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Canvas -->
    <div class="flex-1 bg-gray-900 flex items-center justify-center overflow-hidden" style="min-height:200px;">
      <img id="crop-image" src="" alt="crop" class="block max-w-full" style="display:none;max-height:calc(90vh - 165px);">
    </div>

    <!-- Footer -->
    <div class="flex-shrink-0 px-5 py-4 border-t border-gray-100 bg-white">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

        <!-- Rotate tools -->
        <div class="flex items-center gap-2">
          <span class="text-xs text-gray-500 font-medium">Rotate:</span>
          <button onclick="cropperInstance&&cropperInstance.rotate(-90)" title="Rotate left"
            class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
          </button>
          <button onclick="cropperInstance&&cropperInstance.rotate(90)" title="Rotate right"
            class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/></svg>
          </button>
          <button onclick="cropperInstance&&cropperInstance.reset()"
            class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium transition-colors">
            Reset
          </button>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center gap-3">
          <span class="text-xs text-gray-400 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg font-mono hidden sm:inline">1920 × 1080 px</span>
          <!-- Cancel (post-upload only) -->
          <button id="crop-cancel-btn" onclick="closeCropper()"
            class="hidden px-4 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
            Cancel
          </button>
          <!-- Primary action -->
          <button id="crop-save-btn" onclick="handleCropAction()"
            class="px-5 py-2 rounded-xl text-sm font-semibold bg-primary-500 hover:bg-primary-600 text-white transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span id="crop-save-label">Crop & Upload</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
const csrf = '<?= Csrf::token() ?>';

// ── State ─────────────────────────────────────────────────────────────────
let cropperInstance = null;
let cropMode        = null; // 'pre-upload' | 'post-upload'

// pre-upload queue
let cropQueue       = [];
let cropQueueIndex  = 0;
let croppedBlobs    = [];

// post-upload
let postCropMediaId = null;

// ── Upload Zone ───────────────────────────────────────────────────────────
const zone = document.getElementById('drop-zone');
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-primary-400','bg-primary-50'); });
zone.addEventListener('dragleave', () => zone.classList.remove('border-primary-400','bg-primary-50'));
zone.addEventListener('drop', e => {
  e.preventDefault();
  zone.classList.remove('border-primary-400','bg-primary-50');
  handleFiles(e.dataTransfer.files);
});
document.getElementById('file-input').addEventListener('change', e => {
  handleFiles(e.target.files);
  e.target.value = ''; // allow re-selecting same file
});

// ── Dimension check helper ────────────────────────────────────────────────
function getImageDimensions(file) {
  return new Promise(resolve => {
    const img = new Image();
    const url = URL.createObjectURL(file);
    img.onload  = () => { URL.revokeObjectURL(url); resolve({ w: img.naturalWidth,  h: img.naturalHeight }); };
    img.onerror = () => { URL.revokeObjectURL(url); resolve({ w: 0, h: 0 }); };
    img.src = url;
  });
}

// ── File validation + route: auto-upload if already 1920×1080 ─────────────
async function handleFiles(files) {
  const items  = document.getElementById('upload-items');
  const status = document.getElementById('upload-status');

  const directUpload = []; // already 1920×1080
  const needsCrop    = []; // wrong size — cropping mandatory

  for (const file of files) {
    if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
      status.classList.remove('hidden');
      items.innerHTML += errorItem(file.name, 'Not a supported format (JPG, PNG, WEBP only).');
      continue;
    }
    if (file.size > 30 * 1048576) {
      status.classList.remove('hidden');
      items.innerHTML += errorItem(file.name, `File too large (${(file.size/1048576).toFixed(1)} MB). Max 30 MB.`);
      continue;
    }

    const dims = await getImageDimensions(file);
    if (dims.w === 1920 && dims.h === 1080) {
      directUpload.push(file);
    } else {
      needsCrop.push(file);
    }
  }

  // Upload correct-size images immediately
  if (directUpload.length > 0) {
    status.classList.remove('hidden');
    items.innerHTML += directUpload.map(f =>
      `<div class="flex items-center gap-3 text-sm p-2.5 rounded-lg bg-blue-50 text-blue-700">
        <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
        <span class="flex-1 truncate font-medium">${f.name}</span>
        <span class="text-xs opacity-60">Already 1920 × 1080 — uploading…</span>
      </div>`
    ).join('');
    await uploadDirectFiles(directUpload);
  }

  // Open mandatory crop queue for wrong-size images
  if (needsCrop.length > 0) {
    cropQueue      = needsCrop;
    cropQueueIndex = 0;
    croppedBlobs   = [];
    cropMode       = 'pre-upload';
    openQueueItem();
  }
}

async function uploadDirectFiles(files) {
  const items = document.getElementById('upload-items');
  const formData = new FormData();
  formData.append('_csrf_token', csrf);
  for (const f of files) formData.append('files[]', f, f.name);

  try {
    const r = await fetch('/media/upload-cropped', { method: 'POST', body: formData });
    const d = await r.json();
    d.uploaded?.forEach(f => items.insertAdjacentHTML('beforeend', successItem(f.name, `${f.size} · ${f.width ?? '?'}×${f.height ?? '?'}`)));
    d.errors?.forEach(e   => items.insertAdjacentHTML('beforeend', errorItem(e, '')));
    if (d.uploaded?.length > 0 && cropQueue.length === 0) setTimeout(() => location.reload(), 1200);
  } catch(e) {
    items.insertAdjacentHTML('beforeend', errorItem('Upload failed', e.message));
  }
}

// ── Pre-upload crop queue ─────────────────────────────────────────────────
function openQueueItem() {
  const file  = cropQueue[cropQueueIndex];
  const total = cropQueue.length;
  const isLast = cropQueueIndex === total - 1;

  // Modal labels
  document.getElementById('crop-title').textContent   = 'Crop Before Upload';
  document.getElementById('crop-subtitle').textContent = `${file.name} · Output will be saved as 1920 × 1080 px`;
  document.getElementById('crop-save-label').textContent = total > 1
    ? (isLast ? `Crop & Upload All (${total})` : `Crop & Next →`)
    : 'Crop & Upload';

  // Queue badge
  const badge = document.getElementById('crop-queue-badge');
  if (total > 1) {
    badge.textContent = `${cropQueueIndex + 1} / ${total}`;
    badge.classList.remove('hidden');
  } else {
    badge.classList.add('hidden');
  }

  // Button visibility — no skip allowed for mandatory crop
  document.getElementById('crop-cancel-btn').classList.add('hidden');
  document.getElementById('crop-close-btn').style.display = 'none';

  showCropModal(URL.createObjectURL(file));
}

async function handleCropAction() {
  if (cropMode === 'pre-upload') {
    await cropAndCollect();
  } else {
    await savePostCrop();
  }
}

async function cropAndCollect() {
  if (!cropperInstance) return;

  const btn = document.getElementById('crop-save-btn');
  setSaveBtnLoading(btn, 'Processing…');

  const file   = cropQueue[cropQueueIndex];
  const canvas = cropperInstance.getCroppedCanvas({ width: 1920, height: 1080, imageSmoothingQuality: 'high' });

  // Output as JPEG for best compression (works even if input was PNG/WEBP)
  const blob = await compressToBlob(canvas, 'image/jpeg');

  // Replace extension with .jpg for the stored name
  const baseName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
  croppedBlobs.push(new File([blob], baseName, { type: 'image/jpeg' }));

  cropQueueIndex++;

  if (cropQueueIndex >= cropQueue.length) {
    closeCropper();
    uploadCroppedFiles();
  } else {
    // Destroy old cropper before loading next image
    if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
    openQueueItem();
  }

  resetSaveBtn(btn);
}


async function uploadCroppedFiles() {
  const status = document.getElementById('upload-status');
  const items  = document.getElementById('upload-items');
  const label  = document.getElementById('upload-status-label');
  status.classList.remove('hidden');
  items.innerHTML = '';
  label.textContent = 'Uploading…';

  const formData = new FormData();
  formData.append('_csrf_token', csrf);
  for (const f of croppedBlobs) {
    formData.append('files[]', f, f.name);
  }

  try {
    const r = await fetch('/media/upload-cropped', { method: 'POST', body: formData });
    const d = await r.json();
    label.textContent = 'Upload complete';
    d.uploaded?.forEach(f => items.insertAdjacentHTML('beforeend', successItem(f.name, `${f.size} · ${f.width ?? '?'}×${f.height ?? '?'}`)));
    d.errors?.forEach(e  => items.insertAdjacentHTML('beforeend', errorItem(e, '')));
    if (d.uploaded?.length > 0) setTimeout(() => location.reload(), 1200);
  } catch(e) {
    items.insertAdjacentHTML('beforeend', errorItem('Upload failed', e.message));
  }
}

// ── Post-upload crop (Re-crop on existing library image) ──────────────────
function openPostCropper(id, btn) {
  const card = btn.closest('[data-id]');
  const src  = card.querySelector('[data-media-img]').src.split('?')[0];
  postCropMediaId = id;
  cropMode        = 'post-upload';

  document.getElementById('crop-title').textContent    = 'Re-crop & Resize Image';
  document.getElementById('crop-subtitle').textContent = 'Select the area to keep · Saved as 1920 × 1080 px';
  document.getElementById('crop-save-label').textContent = 'Apply Crop';
  document.getElementById('crop-queue-badge').classList.add('hidden');
  document.getElementById('crop-skip-btn').classList.add('hidden');
  document.getElementById('crop-cancel-btn').classList.remove('hidden');
  document.getElementById('crop-close-btn').style.display = '';

  showCropModal(src + '?v=' + Date.now());
}

async function savePostCrop() {
  if (!cropperInstance || !postCropMediaId) return;

  const btn = document.getElementById('crop-save-btn');
  setSaveBtnLoading(btn, 'Saving…');

  const data = cropperInstance.getData(true);
  const body = new URLSearchParams({
    _csrf_token: csrf,
    x: data.x, y: data.y,
    width: data.width, height: data.height,
    rotate: data.rotate ?? 0,
  });

  try {
    const r = await fetch(`/media/${postCropMediaId}/crop`, { method: 'POST', body });
    const d = await r.json();
    if (d.success) {
      const card = document.querySelector(`[data-id="${postCropMediaId}"]`);
      if (card) {
        const img  = card.querySelector('[data-media-img]');
        img.src = img.src.split('?')[0] + '?v=' + Date.now();
      }
      closeCropper();
    } else {
      alert(d.error || 'Crop failed.');
    }
  } catch(e) {
    alert('Network error: ' + e.message);
  }

  resetSaveBtn(btn);
}

// ── Shared crop modal helpers ─────────────────────────────────────────────
function showCropModal(src) {
  const modal = document.getElementById('crop-modal');
  const img   = document.getElementById('crop-image');

  img.style.display = 'none';
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
  img.src = src;
  img.style.display = 'block';
}

function closeCropper() {
  if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
  const modal = document.getElementById('crop-modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.body.style.overflow = '';
  postCropMediaId = null;
}

// Compress canvas to JPEG blob, trying quality levels to keep a reasonable size
async function compressToBlob(canvas, mime) {
  for (const q of [0.92, 0.85, 0.78, 0.70]) {
    const blob = await new Promise(r => canvas.toBlob(r, mime, q));
    if (blob && blob.size <= 4 * 1048576) return blob; // under 4MB is fine for the 5MB endpoint
  }
  return await new Promise(r => canvas.toBlob(r, mime, 0.70));
}

// ── Delete ────────────────────────────────────────────────────────────────
async function deleteMedia(id, btn) {
  if (!confirm('Delete this image? If it\'s used in a channel, you\'ll need to remove it from the channel first.')) return;
  btn.disabled = true;
  const r = await fetch(`/media/${id}/delete`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `_csrf_token=${encodeURIComponent(csrf)}`
  });
  const d = await r.json();
  if (d.success) {
    btn.closest('[data-id]').remove();
  } else {
    btn.disabled = false;
    alert(d.error || 'Could not delete image.');
  }
}

// ── UI helpers ────────────────────────────────────────────────────────────
function successItem(name, detail) {
  return `<div class="flex items-center gap-3 text-sm p-2.5 rounded-lg bg-green-50 text-green-800">
    <span>✓</span><span class="flex-1 truncate font-medium">${name}</span><span class="text-xs opacity-60">${detail}</span>
  </div>`;
}
function errorItem(name, detail) {
  return `<div class="flex items-center gap-3 text-sm p-2.5 rounded-lg bg-red-50 text-red-800">
    <span>✗</span><span class="flex-1 truncate font-medium">${name}</span><span class="text-xs opacity-60">${detail}</span>
  </div>`;
}
function setSaveBtnLoading(btn, label) {
  btn.disabled = true;
  btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> ${label}`;
}
function resetSaveBtn(btn) {
  btn.disabled = false;
  btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span id="crop-save-label">${document.getElementById('crop-save-label')?.textContent||'Save'}</span>`;
}

// Close on Escape (only in post-upload mode)
document.addEventListener('keydown', e => { if (e.key === 'Escape' && cropMode === 'post-upload') closeCropper(); });
// Close on backdrop click (only in post-upload mode)
document.getElementById('crop-backdrop').addEventListener('click', () => { if (cropMode === 'post-upload') closeCropper(); });
</script>
