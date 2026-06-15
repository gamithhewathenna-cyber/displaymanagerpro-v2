<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Under Maintenance</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 flex items-center justify-center p-4">
  <div class="text-center max-w-lg">
    <!-- Animated icon -->
    <div class="w-24 h-24 bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-8 animate-pulse">
      <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </div>

    <!-- Logo -->
    <div class="flex items-center justify-center gap-2 mb-8">
      <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
      </div>
      <span class="text-white font-bold text-lg"><?= htmlspecialchars($siteName) ?></span>
    </div>

    <h1 class="text-4xl font-bold text-white mb-4">Under Maintenance</h1>
    <p class="text-slate-400 text-lg mb-8 leading-relaxed">
      <?= htmlspecialchars($maintenanceMessage) ?>
    </p>

    <!-- Divider -->
    <div class="w-16 h-1 bg-indigo-500 rounded-full mx-auto mb-8"></div>

    <p class="text-slate-500 text-sm">
      Are you an admin?
      <a href="/login" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">Sign in here</a>
    </p>
  </div>
</body>
</html>
