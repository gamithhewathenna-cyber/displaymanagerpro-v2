<?php $page = 'terms'; ?>
<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>

<form method="POST" action="/admin/content/terms/save" class="space-y-5" id="policy-form">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-semibold text-gray-900">Terms & Conditions</h2>
      <a href="/terms" target="_blank" class="text-xs text-primary-600 hover:underline font-medium">View page →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Page Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($content['title']) ?>"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
        <input type="text" name="last_updated" value="<?= htmlspecialchars($content['last_updated']) ?>"
          placeholder="e.g. June 1, 2025"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-gray-900">Content</h2>
      <button type="button" onclick="togglePreview()" class="text-xs text-primary-600 font-medium hover:underline" id="preview-btn">Show Preview</button>
    </div>
    <p class="text-xs text-gray-400 mb-3">Use basic HTML tags: <code class="bg-gray-100 px-1 rounded">&lt;h2&gt;</code> for section headings, <code class="bg-gray-100 px-1 rounded">&lt;p&gt;</code> for paragraphs, <code class="bg-gray-100 px-1 rounded">&lt;ul&gt;&lt;li&gt;</code> for lists, <code class="bg-gray-100 px-1 rounded">&lt;strong&gt;</code> for bold text.</p>

    <div class="flex flex-wrap gap-1.5 mb-2">
      <?php $btns = [['<h2>H2</h2>','insertH2'],['<h3>H3</h3>','insertH3'],['<strong>B</strong>','wrapBold'],['<em>I</em>','wrapItalic'],['¶ P','insertP'],['• List','insertUL']]; ?>
      <?php foreach ($btns as [$label, $fn]): ?>
      <button type="button" onclick="editorAction('<?= $fn ?>')"
        class="px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors border border-gray-200">
        <?= $label ?>
      </button>
      <?php endforeach; ?>
    </div>

    <textarea name="body" id="policy-editor" rows="28"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y"><?= htmlspecialchars($content['body']) ?></textarea>

    <div id="policy-preview" class="hidden mt-4 border border-gray-200 rounded-xl p-6 prose prose-sm max-w-none text-gray-700 leading-relaxed bg-gray-50"></div>
  </div>

  <div class="flex gap-3">
    <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Save Terms & Conditions</button>
    <a href="/terms" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Preview Page</a>
  </div>
</form>

<style>
  #policy-preview h2 { font-size:1.1rem; font-weight:700; margin:1.25rem 0 .5rem; color:#111; }
  #policy-preview h3 { font-size:.95rem; font-weight:600; margin:1rem 0 .4rem; color:#374151; }
  #policy-preview p  { margin:.5rem 0; }
  #policy-preview ul { list-style:disc; padding-left:1.5rem; margin:.5rem 0; }
  #policy-preview strong { font-weight:600; }
</style>
<script>
function togglePreview() {
  var editor  = document.getElementById('policy-editor');
  var preview = document.getElementById('policy-preview');
  var btn     = document.getElementById('preview-btn');
  var hidden  = preview.classList.contains('hidden');
  preview.innerHTML = hidden ? editor.value : '';
  preview.classList.toggle('hidden', !hidden);
  btn.textContent = hidden ? 'Hide Preview' : 'Show Preview';
}
function editorAction(action) {
  var ta = document.getElementById('policy-editor');
  var start = ta.selectionStart, end = ta.selectionEnd, sel = ta.value.slice(start, end);
  var ins = '';
  if (action === 'insertH2')   ins = '\n<h2>' + (sel || 'Heading') + '</h2>\n';
  if (action === 'insertH3')   ins = '\n<h3>' + (sel || 'Sub-heading') + '</h3>\n';
  if (action === 'wrapBold')   ins = '<strong>' + (sel || 'bold text') + '</strong>';
  if (action === 'wrapItalic') ins = '<em>' + (sel || 'italic') + '</em>';
  if (action === 'insertP')    ins = '\n<p>' + (sel || 'Paragraph text here.') + '</p>\n';
  if (action === 'insertUL')   ins = '\n<ul>\n  <li>Item one</li>\n  <li>Item two</li>\n</ul>\n';
  ta.value = ta.value.slice(0, start) + ins + ta.value.slice(end);
  ta.focus();
  ta.selectionStart = ta.selectionEnd = start + ins.length;
}
</script>
