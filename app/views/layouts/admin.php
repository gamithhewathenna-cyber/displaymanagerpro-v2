<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helpers::e($title ?? 'Admin') ?> – <?= Helpers::e(Settings::get('company_name', APP_NAME)) ?> Admin</title>
<?php $_fav = Settings::get('site_favicon',''); if ($_fav): ?><link rel="icon" href="<?= Helpers::e($_fav) ?>?v=<?= filemtime(PUBLIC_PATH.$_fav) ?>"><?php endif; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: { extend: { fontFamily: { sans: ['Poppins','sans-serif'] }, colors: { primary: { 50:'#fdf2f8',100:'#fce7f3',200:'#fbcfe8',300:'#f9a8d4',400:'#f472b6',500:'#ec4899',600:'#db2777',700:'#be185d',800:'#9d174d',900:'#831843' } } } }
  }
</script>
<style>body{font-family:'Poppins',sans-serif;}.active-nav{background:#9d174d;color:#fff;}</style>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 hidden md:hidden" onclick="closeSidebar()"></div>

<div class="flex min-h-screen">

  <!-- Admin Sidebar -->
  <aside id="admin-sidebar" class="w-60 bg-gray-900 fixed top-0 left-0 h-full z-40 flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-200">
    <div class="h-16 flex items-center px-5 border-b border-gray-700">
      <?php $__logo = Settings::get('company_logo', ''); ?>
      <a href="/admin" class="flex items-center gap-2 text-white font-bold min-w-0">
        <?php if ($__logo): ?>
          <div class="bg-white rounded-md px-2 py-1 flex-shrink-0">
            <img src="<?= Helpers::e($__logo) ?>" alt="<?= Helpers::e(Settings::get('company_name', APP_NAME)) ?>" class="max-h-7 max-w-[120px] object-contain block">
          </div>
        <?php else: ?>
          <div class="w-7 h-7 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <span class="text-sm truncate">Admin Panel</span>
        <?php endif; ?>
      </a>
    </div>
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
      <?php
        $cp = strtok($_SERVER['REQUEST_URI'],'?');
        $items = [
          ['/admin','Dashboard','M3 7h18M3 12h18M3 17h18'],
          ['/admin/customers','Customers','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
          ['/admin/plans','Plans','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
          ['/admin/tickets','Support Tickets','M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
          ['/admin/revenue','Revenue','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
          ['/admin/coupons','Coupons','M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
          ['/admin/content','Content','M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
          ['/admin/blog','News & Updates','M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h2'],
          ['/admin/settings','Settings','M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        foreach ($items as [$href,$label,$path]):
          $active = $cp === $href || ($href !== '/admin' && str_starts_with($cp, $href));
      ?>
      <a href="<?= $href ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors <?= $active ? 'active-nav' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?>">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $path ?>"/></svg>
        <?= $label ?>
      </a>
      <?php endforeach; ?>
    </nav>
    <div class="p-3 border-t border-gray-700 space-y-0.5">
      <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Customer View
      </a>
      <a href="/logout" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-400 hover:bg-red-900/40 hover:text-red-400 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Sign Out
      </a>
    </div>
  </aside>

  <div class="flex-1 md:ml-60 flex flex-col min-h-screen">
    <header class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-4 md:px-6 sticky top-0 z-20">
      <div class="flex items-center gap-3">
        <!-- Hamburger (mobile only) -->
        <button onclick="openSidebar()" class="md:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" aria-label="Open menu">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-base font-semibold text-gray-800"><?= Helpers::e($title ?? 'Admin') ?></h1>
      </div>
      <div class="flex items-center gap-3 text-sm text-gray-500">
        <span class="hidden sm:inline"><?= Helpers::e(Session::get('user')['name'] ?? '') ?></span>
      </div>
    </header>

    <?php if ($msg = Session::getFlash('success')): ?>
      <div class="mx-4 md:mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm"><?= Helpers::e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = Session::getFlash('error')): ?>
      <div class="mx-4 md:mx-6 mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm"><?= Helpers::e($msg) ?></div>
    <?php endif; ?>

    <main class="flex-1 p-4 md:p-6">
      <?php require VIEWS_PATH . '/' . $content_view . '.php'; ?>
    </main>
  </div>
</div>

<script>
  function openSidebar() {
    document.getElementById('admin-sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('hidden');
  }
  function closeSidebar() {
    document.getElementById('admin-sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('hidden');
  }
</script>
</body>
</html>
