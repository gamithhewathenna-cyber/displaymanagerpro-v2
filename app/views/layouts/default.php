<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helpers::e($title ?? 'Dashboard') ?> – <?= Helpers::e(Settings::get('company_name', APP_NAME)) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: { extend: { fontFamily: { sans: ['Poppins','sans-serif'] }, colors: { primary: { 50:'#f0f0ff',100:'#e0e0ff',200:'#c7c7ff',500:'#6366f1',600:'#4f46e5',700:'#4338ca' } } } }
  }
</script>
<style>
  body { font-family: 'Poppins', sans-serif; }
  .sidebar-item.active { background: #eef2ff; color: #4f46e5; font-weight: 600; }
  .sidebar-item.active svg { color: #4f46e5; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="flex min-h-screen">

  <!-- Sidebar -->
  <aside class="w-64 bg-white border-r border-gray-100 flex-shrink-0 fixed top-0 left-0 h-full z-30 flex flex-col">
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-gray-100">
      <?php $__logo = Settings::get('company_logo', ''); ?>
      <a href="/dashboard" class="flex items-center gap-2 font-bold text-lg text-gray-900 min-w-0">
        <?php if ($__logo): ?>
          <img src="<?= Helpers::e($__logo) ?>" alt="<?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>" class="max-h-9 max-w-[160px] object-contain">
        <?php else: ?>
          <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <span class="truncate"><?= Helpers::e(Settings::get('company_name', APP_NAME)) ?></span>
        <?php endif; ?>
      </a>
    </div>

    <!-- Nav -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
      <?php
        $currentPath = strtok($_SERVER['REQUEST_URI'], '?');
        $navItems = [
          ['/dashboard',  'Dashboard',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>'],
          ['/channels',   'TV Channels','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
          ['/media',      'Media',      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
          ['/billing',    'Billing',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
          ['/support',    'Support',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>'],
          ['/profile',    'Profile',    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
        ];
        foreach ($navItems as [$href, $label, $iconPath]):
          $active = ($currentPath === $href || str_starts_with($currentPath, $href . '/')) && $href !== '/dashboard'
                    || $currentPath === $href;
      ?>
      <a href="<?= $href ?>" class="sidebar-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-colors text-sm <?= $active ? 'active' : '' ?>">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $iconPath ?></svg>
        <?= $label ?>
      </a>
      <?php endforeach; ?>
    </nav>

    <!-- User -->
    <div class="p-4 border-t border-gray-100">
      <?php $u = Session::get('user'); ?>
      <div class="flex items-center gap-3 px-2 mb-3">
        <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm flex-shrink-0">
          <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="min-w-0">
          <div class="text-sm font-semibold text-gray-900 truncate"><?= Helpers::e($u['name'] ?? '') ?></div>
          <div class="text-xs text-gray-400 truncate"><?= Helpers::e($u['email'] ?? '') ?></div>
        </div>
      </div>
      <a href="/logout" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Sign Out
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 ml-64 flex flex-col min-h-screen">
    <!-- Top bar -->
    <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-8 sticky top-0 z-20">
      <h1 class="text-lg font-semibold text-gray-900"><?= Helpers::e($title ?? 'Dashboard') ?></h1>
      <div class="flex items-center gap-3">
        <?php if (Settings::get('stripe_mode') === 'test'): ?>
          <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-1 rounded-full">TEST MODE</span>
        <?php endif; ?>
        <a href="/channels/create" class="bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
          + New Channel
        </a>
      </div>
    </header>

    <!-- Flash messages -->
    <div id="flash-container" class="px-8 pt-4">
      <?php if ($msg = Session::getFlash('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
          <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          <?= Helpers::e($msg) ?>
        </div>
      <?php endif; ?>
      <?php if ($msg = Session::getFlash('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2">
          <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
          <?= Helpers::e($msg) ?>
        </div>
      <?php endif; ?>
    </div>

    <main class="flex-1 p-8">
      <?php require VIEWS_PATH . '/' . $content_view . '.php'; ?>
    </main>
  </div>
</div>

<script>
  setTimeout(() => {
    document.querySelectorAll('#flash-container > div').forEach(el => el.remove());
  }, 5000);
</script>
</body>
</html>
