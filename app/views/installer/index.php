<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Install SignageCloud</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['Poppins','sans-serif']},colors:{primary:{500:'#6366f1',600:'#4f46e5'}}}}}</script>
<style>body{font-family:'Poppins',sans-serif;}</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-2xl">
  <div class="text-center mb-8">
    <div class="w-14 h-14 bg-primary-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/30">
      <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-900">SignageCloud</h1>
    <p class="text-gray-400 mt-1">Web-based installer · v<?= APP_VERSION ?></p>
  </div>

  <!-- Step indicators -->
  <div class="flex items-center justify-center gap-2 mb-8">
    <?php $steps = ['Database','Tables','Admin','SMTP','Stripe','Finish']; ?>
    <?php foreach ($steps as $i => $s): ?>
    <div class="flex items-center gap-2">
      <div class="step-dot w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all bg-gray-200 text-gray-500" id="step-<?= $i+1 ?>">
        <?= $i+1 ?>
      </div>
      <?php if ($i < count($steps)-1): ?><div class="w-8 h-px bg-gray-200"></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <!-- Step 1: Database -->
    <div class="step-panel p-8" id="panel-1">
      <h2 class="text-xl font-bold text-gray-900 mb-1">Database Connection</h2>
      <p class="text-gray-400 text-sm mb-6">Enter your MySQL database credentials.</p>
      <div class="space-y-4">
        <div class="grid grid-cols-3 gap-3">
          <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Host</label><input id="db_host" type="text" value="localhost" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Port</label><input id="db_port" type="text" value="3306" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        </div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Database Name</label><input id="db_name" type="text" placeholder="signagecloud" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Username</label><input id="db_user" type="text" placeholder="root" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Password</label><input id="db_pass" type="password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        </div>
      </div>
      <div id="step1-msg" class="hidden mt-4 p-3 rounded-xl text-sm"></div>
      <button onclick="testDb()" class="mt-6 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Test Connection →</button>
    </div>

    <!-- Step 2: Create Tables -->
    <div class="step-panel hidden p-8" id="panel-2">
      <h2 class="text-xl font-bold text-gray-900 mb-1">Create Database Tables</h2>
      <p class="text-gray-400 text-sm mb-6">This will create all required tables and seed default data.</p>
      <div id="step2-msg" class="hidden p-3 rounded-xl text-sm mb-4"></div>
      <button onclick="runMigration()" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Create Tables →</button>
    </div>

    <!-- Step 3: Admin -->
    <div class="step-panel hidden p-8" id="panel-3">
      <h2 class="text-xl font-bold text-gray-900 mb-1">Create Admin Account</h2>
      <p class="text-gray-400 text-sm mb-6">This will be the super administrator login.</p>
      <div class="space-y-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Full Name</label><input id="admin_name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Email Address</label><input id="admin_email" type="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Password (min 8 chars)</label><input id="admin_pass" type="password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div id="step3-msg" class="hidden mt-4 p-3 rounded-xl text-sm"></div>
      <button onclick="createAdmin()" class="mt-6 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Create Admin →</button>
    </div>

    <!-- Step 4: SMTP -->
    <div class="step-panel hidden p-8" id="panel-4">
      <h2 class="text-xl font-bold text-gray-900 mb-1">Email Setup (SMTP)</h2>
      <p class="text-gray-400 text-sm mb-6">For verification emails and password resets. Skip if not ready.</p>
      <div class="space-y-4">
        <div class="grid grid-cols-3 gap-3">
          <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">SMTP Host</label><input id="smtp_host" type="text" placeholder="smtp.mailgun.org" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Port</label><input id="smtp_port" type="text" value="587" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Username</label><input id="smtp_user" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
          <div><label class="block text-xs font-medium text-gray-500 mb-1">Password</label><input id="smtp_pass" type="password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-medium text-gray-500 mb-1">From Email</label><input id="smtp_from_email" type="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
          <div><label class="block text-xs font-medium text-gray-500 mb-1">From Name</label><input id="smtp_from_name" type="text" value="SignageCloud" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        </div>
      </div>
      <div id="step4-msg" class="hidden mt-4 p-3 rounded-xl text-sm"></div>
      <div class="flex gap-3 mt-6">
        <button onclick="saveSmtp()" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Save & Continue →</button>
        <button onclick="nextStep(5)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-3 rounded-xl text-sm transition-colors">Skip for now</button>
      </div>
    </div>

    <!-- Step 5: Stripe -->
    <div class="step-panel hidden p-8" id="panel-5">
      <h2 class="text-xl font-bold text-gray-900 mb-1">Stripe Setup</h2>
      <p class="text-gray-400 text-sm mb-6">Add your Stripe API keys for subscription billing. You can update these later in Admin → Settings.</p>
      <div class="space-y-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Test Publishable Key</label><input id="stripe_test_pk" type="text" placeholder="pk_test_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Test Secret Key</label><input id="stripe_test_sk" type="text" placeholder="sk_test_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Webhook Signing Secret <span class="font-normal text-gray-400">(optional)</span></label><input id="webhook_secret" type="text" placeholder="whsec_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div id="step5-msg" class="hidden mt-4 p-3 rounded-xl text-sm"></div>
      <div class="flex gap-3 mt-6">
        <button onclick="saveStripe()" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl text-sm transition-colors">Save & Continue →</button>
        <button onclick="nextStep(6)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-5 py-3 rounded-xl text-sm transition-colors">Skip for now</button>
      </div>
    </div>

    <!-- Step 6: Finish -->
    <div class="step-panel hidden p-8" id="panel-6">
      <h2 class="text-xl font-bold text-gray-900 mb-1">Finalise Installation</h2>
      <p class="text-gray-400 text-sm mb-6">Set your company name, then complete the installation.</p>
      <div class="space-y-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Company Name</label><input id="company_name" type="text" value="SignageCloud" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Site URL</label><input id="company_url" type="text" placeholder="https://yourdomain.com" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div id="step6-msg" class="hidden mt-4 p-3 rounded-xl text-sm"></div>
      <button onclick="finish()" class="mt-6 bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-3 rounded-xl text-sm transition-colors">🚀 Complete Installation</button>
    </div>

  </div>
</div>

<script>
let currentStep = 1;

function nextStep(n) {
  document.getElementById('panel-' + currentStep).classList.add('hidden');
  document.getElementById('step-' + currentStep).classList.remove('bg-primary-500','text-white');
  document.getElementById('step-' + currentStep).textContent = '✓';
  document.getElementById('step-' + currentStep).classList.add('bg-green-500','text-white');
  currentStep = n;
  document.getElementById('panel-' + n).classList.remove('hidden');
  document.getElementById('step-' + n).classList.add('bg-primary-500','text-white');
  document.getElementById('step-' + n).classList.remove('bg-gray-200','text-gray-500');
}

function showMsg(stepId, msg, ok) {
  const el = document.getElementById('step' + stepId + '-msg');
  el.textContent = msg;
  el.className = `mt-4 p-3 rounded-xl text-sm ${ok ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
  el.classList.remove('hidden');
}

async function post(action, data) {
  const body = new URLSearchParams({ action, ...data });
  const r = await fetch('/install', { method: 'POST', body });
  return await r.json();
}

async function testDb() {
  const d = await post('test_db', {
    db_host: document.getElementById('db_host').value,
    db_port: document.getElementById('db_port').value,
    db_name: document.getElementById('db_name').value,
    db_user: document.getElementById('db_user').value,
    db_pass: document.getElementById('db_pass').value,
  });
  showMsg(1, d.message, d.success);
  if (d.success) setTimeout(() => nextStep(2), 800);
}

async function runMigration() {
  const d = await post('run_migration', {});
  showMsg(2, d.message, d.success);
  if (d.success) setTimeout(() => nextStep(3), 800);
}

async function createAdmin() {
  const d = await post('create_admin', {
    name: document.getElementById('admin_name').value,
    email: document.getElementById('admin_email').value,
    password: document.getElementById('admin_pass').value,
  });
  showMsg(3, d.message, d.success);
  if (d.success) setTimeout(() => nextStep(4), 800);
}

async function saveSmtp() {
  const d = await post('save_smtp', {
    smtp_host: document.getElementById('smtp_host').value,
    smtp_port: document.getElementById('smtp_port').value,
    smtp_user: document.getElementById('smtp_user').value,
    smtp_pass: document.getElementById('smtp_pass').value,
    smtp_from_email: document.getElementById('smtp_from_email').value,
    smtp_from_name: document.getElementById('smtp_from_name').value,
    smtp_encryption: 'tls',
  });
  showMsg(4, d.message, d.success);
  if (d.success) setTimeout(() => nextStep(5), 800);
}

async function saveStripe() {
  const d = await post('save_stripe', {
    stripe_test_pk: document.getElementById('stripe_test_pk').value,
    stripe_test_sk: document.getElementById('stripe_test_sk').value,
    webhook_secret: document.getElementById('webhook_secret').value,
  });
  showMsg(5, d.message, d.success);
  if (d.success) setTimeout(() => nextStep(6), 800);
}

async function finish() {
  const d = await post('finish', {
    company_name: document.getElementById('company_name').value,
    company_url: document.getElementById('company_url').value,
  });
  showMsg(6, d.message, d.success);
  if (d.success && d.redirect) setTimeout(() => window.location.href = d.redirect, 1500);
}

// Activate step 1
document.getElementById('step-1').classList.add('bg-primary-500','text-white');
document.getElementById('step-1').classList.remove('bg-gray-200','text-gray-500');
</script>
</body>
</html>
