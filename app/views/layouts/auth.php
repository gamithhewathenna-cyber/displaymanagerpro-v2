<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helpers::e($title ?? 'Account') ?> – <?= Helpers::e(Settings::get('company_name', APP_NAME)) ?></title>
<?php $_fav = Settings::get('site_favicon',''); if ($_fav): ?><link rel="icon" href="<?= Helpers::e($_fav) ?>?v=<?= filemtime(PUBLIC_PATH.$_fav) ?>"><?php endif; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: { extend: { fontFamily: { sans: ['Poppins','sans-serif'] }, colors: { primary: { 50:'#f0f0ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca' } } } }
  }
</script>
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 flex items-center justify-center p-4">

<div class="w-full max-w-md">
  <!-- Logo -->
  <div class="text-center mb-8">
    <?php $__logo = Settings::get('company_logo', ''); ?>
    <a href="/" class="inline-flex items-center justify-center gap-2 font-bold text-xl text-gray-900">
      <?php if ($__logo): ?>
        <img src="<?= Helpers::e($__logo) ?>" alt="<?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>" class="max-h-12 max-w-[200px] object-contain">
      <?php else: ?>
        <div class="w-9 h-9 bg-primary-500 rounded-xl flex items-center justify-center shadow-md shadow-primary-500/30">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>
      <?php endif; ?>
    </a>
  </div>

  <!-- Flash -->
  <?php if ($msg = Session::getFlash('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
      <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
      <?= Helpers::e($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($msg = Session::getFlash('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
      <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
      <?= Helpers::e($msg) ?>
    </div>
  <?php endif; ?>

  <!-- Card -->
  <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/80 border border-gray-100 p-6 sm:p-8">
    <?php require VIEWS_PATH . '/' . $content_view . '.php'; ?>
  </div>
</div>
</body>
</html>
