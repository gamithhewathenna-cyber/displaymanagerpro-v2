<div class="max-w-3xl space-y-6">

  <?php
    $g = $settings;
    function settingVal($s, $key) { foreach ($s as $group) { if (isset($group[$key])) return $group[$key]; } return ''; }
  ?>


  <!-- Maintenance Mode -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="maintenance">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="font-semibold text-gray-900">Maintenance Mode</h2>
        <p class="text-sm text-gray-400 mt-0.5">When enabled, only admins can access the site.</p>
      </div>
      <?php $maintOn = ($g['maintenance']['maintenance_mode'] ?? '0') === '1'; ?>
      <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full <?= $maintOn ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
        <span class="w-2 h-2 rounded-full <?= $maintOn ? 'bg-red-500' : 'bg-green-500' ?>"></span>
        <?= $maintOn ? 'Maintenance ON' : 'Site Live' ?>
      </span>
    </div>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="maintenance">
      <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
        <div>
          <div class="font-medium text-sm text-gray-800">Enable Maintenance Mode</div>
          <div class="text-xs text-gray-400 mt-0.5">Visitors will see the maintenance page. Admins bypass it.</div>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" name="maintenance_mode" value="1" <?= $maintOn ? 'checked' : '' ?> class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
        </label>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Maintenance Message</label>
        <textarea name="maintenance_message" rows="3" placeholder="We are currently performing scheduled maintenance. We'll be back shortly."
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars($g['maintenance']['maintenance_message'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save Maintenance Settings</button>
    </form>
  </div>

  <!-- General -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="general">
    <h2 class="font-semibold text-gray-900 mb-4">General</h2>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="general">
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
          <input type="text" name="company_name" value="<?= Helpers::e($g['general']['company_name'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Company URL</label>
          <input type="text" name="company_url" value="<?= Helpers::e($g['general']['company_url'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save General</button>
    </form>
  </div>

  <!-- Stripe -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="stripe">
    <h2 class="font-semibold text-gray-900 mb-1">Stripe</h2>
    <p class="text-sm text-gray-400 mb-4">Toggle between test and live mode without touching code.</p>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="stripe">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
        <select name="stripe_mode" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option value="test" <?= ($g['stripe']['stripe_mode']??'test')==='test'?'selected':'' ?>>Test Mode</option>
          <option value="live" <?= ($g['stripe']['stripe_mode']??'test')==='live'?'selected':'' ?>>Live Mode</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Test Publishable Key</label>
          <input type="text" name="stripe_test_pk" value="<?= Helpers::e($g['stripe']['stripe_test_pk'] ?? '') ?>" placeholder="pk_test_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Test Secret Key</label>
          <input type="text" name="stripe_test_sk" value="<?= Helpers::e($g['stripe']['stripe_test_sk'] ?? '') ?>" placeholder="sk_test_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Live Publishable Key</label>
          <input type="text" name="stripe_live_pk" value="<?= Helpers::e($g['stripe']['stripe_live_pk'] ?? '') ?>" placeholder="pk_live_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Live Secret Key</label>
          <input type="text" name="stripe_live_sk" value="<?= Helpers::e($g['stripe']['stripe_live_sk'] ?? '') ?>" placeholder="sk_live_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Webhook Signing Secret</label>
          <input type="text" name="stripe_webhook_secret" value="<?= Helpers::e($g['stripe']['stripe_webhook_secret'] ?? '') ?>" placeholder="whsec_…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">
        Stripe webhook URL: <code class="font-mono font-bold"><?= Helpers::baseUrl('billing/webhook') ?></code>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save Stripe</button>
    </form>
  </div>

  <!-- SMTP -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="mail">
    <h2 class="font-semibold text-gray-900 mb-4">Email (SMTP)</h2>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="mail">
      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">SMTP Host</label>
          <input type="text" name="smtp_host" value="<?= Helpers::e($g['mail']['smtp_host'] ?? '') ?>" placeholder="smtp.mailgun.org" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Port</label>
          <input type="number" name="smtp_port" value="<?= Helpers::e($g['mail']['smtp_port'] ?? '587') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Username</label>
          <input type="text" name="smtp_user" value="<?= Helpers::e($g['mail']['smtp_user'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Password</label>
          <input type="password" name="smtp_pass" value="<?= Helpers::e($g['mail']['smtp_pass'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">From Email</label>
          <input type="email" name="smtp_from_email" value="<?= Helpers::e($g['mail']['smtp_from_email'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">From Name</label>
          <input type="text" name="smtp_from_name" value="<?= Helpers::e($g['mail']['smtp_from_name'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Encryption</label>
          <select name="smtp_encryption" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="tls" <?= ($g['mail']['smtp_encryption']??'tls')==='tls'?'selected':'' ?>>TLS (port 587)</option>
            <option value="ssl" <?= ($g['mail']['smtp_encryption']??'')==='ssl'?'selected':'' ?>>SSL (port 465)</option>
            <option value="none" <?= ($g['mail']['smtp_encryption']??'')==='none'?'selected':'' ?>>None</option>
          </select>
        </div>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save SMTP</button>
    </form>
  </div>

  <!-- Storage -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="storage">
    <h2 class="font-semibold text-gray-900 mb-4">File Storage</h2>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="storage">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Storage Driver</label>
        <select name="storage_driver" id="storage_driver" onchange="showStorageFields(this.value)" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option value="local" <?= ($g['storage']['storage_driver']??'local')==='local'?'selected':'' ?>>Local Disk</option>
          <option value="s3" <?= ($g['storage']['storage_driver']??'')==='s3'?'selected':'' ?>>AWS S3</option>
          <option value="r2" <?= ($g['storage']['storage_driver']??'')==='r2'?'selected':'' ?>>Cloudflare R2</option>
        </select>
      </div>
      <div id="s3-fields" class="grid grid-cols-2 gap-4 <?= ($g['storage']['storage_driver']??'local')!=='s3'?'hidden':'' ?>">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Bucket</label><input type="text" name="s3_bucket" value="<?= Helpers::e($g['storage']['s3_bucket']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Region</label><input type="text" name="s3_region" value="<?= Helpers::e($g['storage']['s3_region']??'') ?>" placeholder="us-east-1" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Access Key</label><input type="text" name="s3_access_key" value="<?= Helpers::e($g['storage']['s3_access_key']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Secret Key</label><input type="password" name="s3_secret_key" value="<?= Helpers::e($g['storage']['s3_secret_key']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Public CDN URL</label><input type="text" name="s3_url" value="<?= Helpers::e($g['storage']['s3_url']??'') ?>" placeholder="https://your-bucket.s3.amazonaws.com" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div id="r2-fields" class="grid grid-cols-2 gap-4 <?= ($g['storage']['storage_driver']??'local')!=='r2'?'hidden':'' ?>">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Bucket</label><input type="text" name="r2_bucket" value="<?= Helpers::e($g['storage']['r2_bucket']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Account ID</label><input type="text" name="r2_account_id" value="<?= Helpers::e($g['storage']['r2_account_id']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Access Key</label><input type="text" name="r2_access_key" value="<?= Helpers::e($g['storage']['r2_access_key']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Secret Key</label><input type="password" name="r2_secret_key" value="<?= Helpers::e($g['storage']['r2_secret_key']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Public R2 URL</label><input type="text" name="r2_url" value="<?= Helpers::e($g['storage']['r2_url']??'') ?>" placeholder="https://pub-xxxxx.r2.dev" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save Storage</button>
    </form>
  </div>
</div>

<script>
function showStorageFields(driver) {
  document.getElementById('s3-fields').classList.toggle('hidden', driver !== 's3');
  document.getElementById('r2-fields').classList.toggle('hidden', driver !== 'r2');
}
</script>
