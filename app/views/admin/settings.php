<div class="max-w-3xl space-y-6">

  <?php
    $g = $settings;
    function settingVal($s, $key) { foreach ($s as $group) { if (isset($group[$key])) return $group[$key]; } return ''; }
    $currentLogo    = Settings::get('company_logo', '');
    $currentFavicon = Settings::get('site_favicon', '');
    $companyName    = Settings::get('company_name', APP_NAME);
  ?>

  <!-- Branding -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="branding">
    <h2 class="font-semibold text-gray-900 mb-1">Branding</h2>
    <p class="text-sm text-gray-400 mb-6">Manage your logo and site favicon.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

      <!-- Logo -->
      <div class="border border-gray-100 rounded-xl p-4 space-y-4">
        <div>
          <div class="font-medium text-sm text-gray-800 mb-0.5">Site Logo</div>
          <div class="text-xs text-gray-400">Shown in the sidebar &amp; navigation. JPG, PNG or WebP · max 2MB.</div>
        </div>

        <!-- Preview -->
        <div class="flex items-center justify-center bg-gray-50 border border-dashed border-gray-200 rounded-xl" style="height:72px;">
          <?php if ($currentLogo): ?>
            <img src="<?= Helpers::e($currentLogo) ?>?v=<?= time() ?>" alt="Logo" class="max-h-10 max-w-[180px] object-contain">
          <?php else: ?>
            <span class="text-xs text-gray-400">No logo — app name shown</span>
          <?php endif; ?>
        </div>

        <form method="POST" action="/admin/settings/logo" enctype="multipart/form-data">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <div class="flex items-center gap-2">
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp"
              class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
            <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-lg text-xs transition-colors whitespace-nowrap">Upload</button>
          </div>
        </form>
        <?php if ($currentLogo): ?>
        <form method="POST" action="/admin/settings/logo/remove">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <button type="submit" onclick="return confirm('Remove the current logo?')" class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Remove logo</button>
        </form>
        <?php endif; ?>
      </div>

      <!-- Favicon -->
      <div class="border border-gray-100 rounded-xl p-4 space-y-4">
        <div>
          <div class="font-medium text-sm text-gray-800 mb-0.5">Site Favicon</div>
          <div class="text-xs text-gray-400">Shown in browser tabs &amp; bookmarks. ICO, PNG or SVG · max 1MB.</div>
        </div>

        <!-- Browser tab preview -->
        <div>
          <div class="text-xs text-gray-400 mb-2">Preview</div>
          <div class="inline-flex flex-col">
            <!-- Tab strip -->
            <div class="flex items-center gap-1.5 bg-gray-200 rounded-t-lg px-3 py-2 border border-b-0 border-gray-300 max-w-[200px]">
              <?php if ($currentFavicon): ?>
                <img src="<?= Helpers::e($currentFavicon) ?>?v=<?= time() ?>" alt="Favicon" class="w-4 h-4 object-contain flex-shrink-0">
              <?php else: ?>
                <div class="w-4 h-4 bg-primary-400 rounded-sm flex-shrink-0"></div>
              <?php endif; ?>
              <span class="text-xs text-gray-700 font-medium truncate flex-1"><?= Helpers::e($companyName) ?></span>
              <span class="text-gray-400 text-xs flex-shrink-0">✕</span>
            </div>
            <!-- Address bar -->
            <div class="bg-white border border-gray-300 rounded-b-lg rounded-tr-lg px-3 py-1.5 flex items-center gap-1.5 max-w-[260px]">
              <svg class="w-3 h-3 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
              <span class="text-xs text-gray-400 truncate">yourdomain.com</span>
            </div>
          </div>
        </div>

        <form method="POST" action="/admin/settings/favicon" enctype="multipart/form-data">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <div class="flex items-center gap-2">
            <input type="file" name="favicon" accept="image/x-icon,image/png,image/svg+xml,.ico"
              class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg text-xs transition-colors whitespace-nowrap">Upload</button>
          </div>
        </form>
        <?php if ($currentFavicon): ?>
        <form method="POST" action="/admin/settings/favicon/remove">
          <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
          <button type="submit" onclick="return confirm('Remove the current favicon?')" class="text-xs text-red-500 hover:text-red-700 font-medium transition-colors">Remove favicon</button>
        </form>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- Maintenance Mode -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="maintenance">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="font-semibold text-gray-900">Maintenance Mode</h2>
        <p class="text-sm text-gray-400 mt-0.5">When enabled, only admins can access the site.</p>
      </div>
      <?php $maintOn = Settings::get('maintenance_mode', '0') === '1'; ?>
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
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"><?= htmlspecialchars(Settings::get('maintenance_message', '')) ?></textarea>
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
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
          <input type="text" name="company_name" value="<?= Helpers::e($g['general']['company_name'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Company URL</label>
          <input type="text" name="company_url" value="<?= Helpers::e($g['general']['company_url'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Site URL <span class="text-xs text-gray-400">(used for canonical tags &amp; sitemap — must be exact production URL with https://)</span></label>
        <input type="url" name="app_url" value="<?= Helpers::e($g['general']['app_url'] ?? '') ?>" placeholder="https://app.displaymanagerpro.com" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save General</button>
    </form>
  </div>

  <!-- PayPal -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="paypal">
    <h2 class="font-semibold text-gray-900 mb-1">PayPal</h2>
    <p class="text-sm text-gray-400 mb-4">Connect PayPal to accept subscription payments. Create billing plans at <a href="https://developer.paypal.com" target="_blank" class="text-primary-600">developer.paypal.com</a>, then paste the Plan IDs into each plan's edit page.</p>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="paypal">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
        <select name="paypal_mode" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option value="sandbox" <?= (Settings::get('paypal_mode','sandbox'))==='sandbox'?'selected':'' ?>>Sandbox (Testing)</option>
          <option value="live" <?= (Settings::get('paypal_mode','sandbox'))==='live'?'selected':'' ?>>Live (Production)</option>
        </select>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Client ID</label>
          <input type="text" name="paypal_client_id" value="<?= Helpers::e(Settings::get('paypal_client_id','')) ?>" placeholder="AXxx…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Secret Key</label>
          <input type="password" name="paypal_secret" value="<?= Helpers::e(Settings::get('paypal_secret','')) ?>" placeholder="EXxx…" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="sm:col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Webhook ID <span class="text-gray-400 font-normal">(from PayPal developer dashboard)</span></label>
          <input type="text" name="paypal_webhook_id" value="<?= Helpers::e(Settings::get('paypal_webhook_id','')) ?>" placeholder="1AB23456CD789012E" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700 space-y-1 overflow-x-auto">
        <div>Webhook URL: <code class="font-mono font-bold break-all"><?= Helpers::baseUrl('billing/paypal/webhook') ?></code></div>
        <div>Return URL: <code class="font-mono font-bold break-all"><?= Helpers::baseUrl('billing/paypal/return') ?></code></div>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save PayPal</button>
    </form>
  </div>

  <!-- SMTP -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="mail">
    <h2 class="font-semibold text-gray-900 mb-4">Email (SMTP)</h2>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="mail">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">SMTP Host</label>
          <input type="text" name="smtp_host" value="<?= Helpers::e($g['mail']['smtp_host'] ?? '') ?>" placeholder="mail.yourdomain.com" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Port</label>
          <input type="number" name="smtp_port" value="<?= Helpers::e($g['mail']['smtp_port'] ?? '465') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Username</label>
          <input type="text" name="smtp_user" value="<?= Helpers::e($g['mail']['smtp_user'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Password</label>
          <input type="password" name="smtp_pass" value="<?= Helpers::e($g['mail']['smtp_pass'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">From Email</label>
          <input type="email" name="smtp_from_email" value="<?= Helpers::e($g['mail']['smtp_from_email'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">From Name</label>
          <input type="text" name="smtp_from_name" value="<?= Helpers::e($g['mail']['smtp_from_name'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Encryption</label>
          <select name="smtp_encryption" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="ssl" <?= ($g['mail']['smtp_encryption']??'ssl')==='ssl'?'selected':'' ?>>SSL (port 465) — recommended for cPanel/shared hosting</option>
            <option value="tls" <?= ($g['mail']['smtp_encryption']??'')==='tls'?'selected':'' ?>>STARTTLS (port 587)</option>
            <option value="none" <?= ($g['mail']['smtp_encryption']??'')==='none'?'selected':'' ?>>None (not recommended)</option>
          </select>
        </div>
      </div>
      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save SMTP</button>
    </form>

    <!-- Test Email -->
    <div class="mt-5 pt-5 border-t border-gray-100">
      <h3 class="text-sm font-semibold text-gray-700 mb-1">Send Test Email</h3>
      <p class="text-xs text-gray-400 mb-3">Save your settings first, then send a test to verify the connection works.</p>
      <form method="POST" action="/admin/settings/test-email" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <input type="email" name="test_email"
          value="<?= Helpers::e(Session::get('user')['email'] ?? '') ?>"
          placeholder="you@example.com" required
          class="w-full sm:flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        <button type="submit" class="border border-primary-300 text-primary-600 hover:bg-primary-50 font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors whitespace-nowrap">
          Send Test
        </button>
      </form>
    </div>
  </div>

  <!-- SEO & Analytics -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="seo">
    <h2 class="font-semibold text-gray-900 mb-1">SEO &amp; Analytics</h2>
    <p class="text-sm text-gray-400 mb-4">Connect Google Analytics and Google Search Console to your public website.</p>
    <form method="POST" action="/admin/settings" class="space-y-5">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="seo">

      <!-- Google Analytics -->
      <div class="border border-gray-100 rounded-xl p-4 space-y-3">
        <div class="flex items-center gap-2 mb-1">
          <svg class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm-1.25 17.292l-4.5-4.364 1.857-1.858 2.643 2.506 5.643-5.784 1.857 1.857-7.5 7.643z"/></svg>
          <span class="text-sm font-semibold text-gray-800">Google Analytics (GA4)</span>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Measurement ID</label>
          <input type="text" name="ga_measurement_id"
            value="<?= Helpers::e($g['seo']['ga_measurement_id'] ?? '') ?>"
            placeholder="G-XXXXXXXXXX"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500">
          <p class="text-xs text-gray-400 mt-1">Found in Google Analytics → Admin → Data Streams → your stream → Measurement ID</p>
        </div>
      </div>

      <!-- Google Search Console -->
      <div class="border border-gray-100 rounded-xl p-4 space-y-3">
        <div class="flex items-center gap-2 mb-1">
          <svg class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-4H7l5-8v4h4l-5 8z"/></svg>
          <span class="text-sm font-semibold text-gray-800">Google Search Console</span>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">HTML Meta Tag Verification Code</label>
          <input type="text" name="gsc_verification"
            value="<?= Helpers::e($g['seo']['gsc_verification'] ?? '') ?>"
            placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500">
          <p class="text-xs text-gray-400 mt-1">
            In Search Console → Add Property → HTML tag method. Paste only the <code class="bg-gray-100 px-1 rounded">content="…"</code> value, not the full tag.
            <br>e.g. if Google gives <code class="bg-gray-100 px-1 rounded">&lt;meta name="google-site-verification" content="<strong>abc123</strong>"&gt;</code>, paste <strong>abc123</strong> only.
          </p>
        </div>
      </div>

      <!-- Meta Pixel -->
      <div class="border border-gray-100 rounded-xl p-4 space-y-3">
        <div class="flex items-center gap-2 mb-1">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
          <span class="text-sm font-semibold text-gray-800">Meta Pixel (Facebook)</span>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Pixel Code</label>
          <textarea name="meta_pixel_code" rows="6"
            placeholder="Paste the full Meta Pixel code snippet here…"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-mono leading-relaxed focus:outline-none focus:ring-2 focus:ring-primary-500 resize-y"><?= htmlspecialchars($g['seo']['meta_pixel_code'] ?? '') ?></textarea>
          <p class="text-xs text-gray-400 mt-1">Go to Meta Business Suite → Events Manager → your Pixel → Set up → Copy the full code snippet and paste it above. It will be injected into the <code class="bg-gray-100 px-1 rounded">&lt;head&gt;</code> of every public page.</p>
        </div>
      </div>

      <?php
        $gaSet    = !empty($g['seo']['ga_measurement_id']);
        $gscSet   = !empty($g['seo']['gsc_verification']);
        $pixelSet = !empty($g['seo']['meta_pixel_code']);
      ?>
      <?php if ($gaSet || $gscSet || $pixelSet): ?>
      <div class="flex flex-wrap gap-3 text-xs">
        <?php if ($gaSet): ?><span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 font-medium px-3 py-1.5 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Google Analytics connected</span><?php endif; ?>
        <?php if ($gscSet): ?><span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 font-medium px-3 py-1.5 rounded-full"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>Search Console verification set</span><?php endif; ?>
        <?php if ($pixelSet): ?><span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 font-medium px-3 py-1.5 rounded-full"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>Meta Pixel active</span><?php endif; ?>
      </div>
      <?php endif; ?>

      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save SEO &amp; Analytics</button>
    </form>
  </div>

  <!-- Social Media -->
  <div class="bg-white rounded-xl border border-gray-100 p-6" id="social">
    <h2 class="font-semibold text-gray-900 mb-1">Social Media</h2>
    <p class="text-sm text-gray-400 mb-5">Links shown as icons in the website footer.</p>
    <form method="POST" action="/admin/settings" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <input type="hidden" name="group" value="social">

      <!-- Facebook -->
      <div class="border border-gray-100 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047v-2.66c0-3.025 1.791-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
          <span class="text-sm font-semibold text-gray-800">Facebook</span>
        </div>
        <input type="url" name="facebook_url"
          value="<?= Helpers::e($g['social']['facebook_url'] ?? '') ?>"
          placeholder="https://facebook.com/yourpage"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>

      <!-- Instagram -->
      <div class="border border-gray-100 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="url(#ig-grad)">
            <defs><linearGradient id="ig-grad" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#f09433"/><stop offset="25%" style="stop-color:#e6683c"/><stop offset="50%" style="stop-color:#dc2743"/><stop offset="75%" style="stop-color:#cc2366"/><stop offset="100%" style="stop-color:#bc1888"/></linearGradient></defs>
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
          </svg>
          <span class="text-sm font-semibold text-gray-800">Instagram</span>
        </div>
        <input type="url" name="instagram_url"
          value="<?= Helpers::e($g['social']['instagram_url'] ?? '') ?>"
          placeholder="https://instagram.com/yourhandle"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>

      <!-- Google Business -->
      <div class="border border-gray-100 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-3">
          <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
          <span class="text-sm font-semibold text-gray-800">Google Business</span>
        </div>
        <input type="url" name="google_business_url"
          value="<?= Helpers::e($g['social']['google_business_url'] ?? '') ?>"
          placeholder="https://maps.app.goo.gl/yourprofile"
          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>

      <?php
        $fbSet = !empty($g['social']['facebook_url']);
        $igSet = !empty($g['social']['instagram_url']);
        $gbSet = !empty($g['social']['google_business_url']);
      ?>
      <?php if ($fbSet || $igSet || $gbSet): ?>
      <div class="flex flex-wrap gap-3 text-xs">
        <?php if ($fbSet): ?><span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 font-medium px-3 py-1.5 rounded-full"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>Facebook linked</span><?php endif; ?>
        <?php if ($igSet): ?><span class="inline-flex items-center gap-1.5 bg-pink-50 text-pink-700 font-medium px-3 py-1.5 rounded-full"><span class="w-1.5 h-1.5 bg-pink-500 rounded-full"></span>Instagram linked</span><?php endif; ?>
        <?php if ($gbSet): ?><span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 font-medium px-3 py-1.5 rounded-full"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Google Business linked</span><?php endif; ?>
      </div>
      <?php endif; ?>

      <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">Save Social Media</button>
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
      <div id="s3-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 <?= ($g['storage']['storage_driver']??'local')!=='s3'?'hidden':'' ?>">
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Bucket</label><input type="text" name="s3_bucket" value="<?= Helpers::e($g['storage']['s3_bucket']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Region</label><input type="text" name="s3_region" value="<?= Helpers::e($g['storage']['s3_region']??'') ?>" placeholder="us-east-1" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Access Key</label><input type="text" name="s3_access_key" value="<?= Helpers::e($g['storage']['s3_access_key']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div><label class="block text-xs font-medium text-gray-500 mb-1">Secret Key</label><input type="password" name="s3_secret_key" value="<?= Helpers::e($g['storage']['s3_secret_key']??'') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
        <div class="col-span-2"><label class="block text-xs font-medium text-gray-500 mb-1">Public CDN URL</label><input type="text" name="s3_url" value="<?= Helpers::e($g['storage']['s3_url']??'') ?>" placeholder="https://your-bucket.s3.amazonaws.com" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>
      </div>
      <div id="r2-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 <?= ($g['storage']['storage_driver']??'local')!=='r2'?'hidden':'' ?>">
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
