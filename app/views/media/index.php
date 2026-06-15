<?php $storagePercent = $maxStorage > 0 ? min(100, round(($storageUsed / $maxStorage) * 100)) : 0; ?>

<!-- Upload Zone -->
<div id="drop-zone" class="border-2 border-dashed border-gray-200 hover:border-primary-400 rounded-2xl p-10 text-center mb-6 transition-all cursor-pointer bg-white" onclick="document.getElementById('file-input').click()">
  <div class="text-4xl mb-3">☁️</div>
  <div class="font-semibold text-gray-700 mb-1">Drop images here or click to upload</div>
  <div class="text-sm text-gray-400 mb-4">JPG, PNG, WEBP · Max 500KB per file</div>
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
<div class="flex items-center justify-between mb-4 gap-4">
  <div class="flex items-center gap-4">
    <h3 class="font-semibold text-gray-900">Media Library</h3>
    <span class="text-sm text-gray-400"><?= count($media) ?> image<?= count($media) != 1 ? 's' : '' ?></span>
  </div>
  <div class="flex items-center gap-2 text-xs text-gray-400">
    <div class="w-32 bg-gray-100 rounded-full h-1.5">
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
    <img src="<?= Helpers::e($m['public_url']) ?>" alt="<?= Helpers::e($m['original_name']) ?>" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex flex-col items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
      <button onclick="deleteMedia(<?= $m['id'] ?>, this)" class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
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

<script>
const csrf = '<?= Csrf::token() ?>';

// Drag and drop
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
    if (file.size > 512000) {
      addUploadItem(items, file.name, 'error', `Too large (${(file.size/1024).toFixed(0)}KB). Max 500KB.`);
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
</script>
