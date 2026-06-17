<?php
  $currentLogo       = Settings::get('website_logo', '');
  $currentMobileLogo = Settings::get('website_logo_mobile', '');
?>

<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>

<div class="max-w-2xl space-y-6">

  <!-- Website Logo -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-1">Website Logo</h2>
    <p class="text-sm text-gray-400 mb-5">
      Displayed in the public website navigation bar and footer. JPG, PNG, or WebP, max 2MB.
      If not set, the logo from <a href="/admin/settings#branding" class="text-primary-600 hover:underline">Settings → Branding</a> is used as a fallback.
    </p>

    <!-- Preview -->
    <div class="mb-5">
      <?php if ($currentLogo): ?>
        <div class="flex items-center gap-4">
          <div class="border border-gray-200 rounded-xl bg-gray-50 flex items-center justify-center px-5 py-3" style="min-width:140px;height:68px;">
            <img src="<?= Helpers::e($currentLogo) ?>?v=<?= time() ?>" alt="Website logo" class="max-h-10 max-w-[200px] object-contain">
          </div>
          <div>
            <div class="text-sm font-medium text-gray-700">Current website logo</div>
            <div class="text-xs text-gray-400 mt-0.5">Shown on nav bar and footer</div>
          </div>
        </div>
      <?php else: ?>
        <div class="flex items-center gap-3 px-5 py-4 bg-gray-50 border border-dashed border-gray-300 rounded-xl">
          <div class="w-10 h-10 rounded-lg bg-primary-500 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-600">No website logo uploaded</div>
            <div class="text-xs text-gray-400 mt-0.5">The site will use your Settings logo or the default icon + company name</div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Upload -->
    <form method="POST" action="/admin/content/branding/logo" enctype="multipart/form-data" class="space-y-3">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <div class="flex items-center gap-3 flex-wrap">
        <label class="block flex-1 min-w-0">
          <span class="sr-only">Choose website logo</span>
          <input type="file" name="website_logo" accept="image/jpeg,image/png,image/webp"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
        </label>
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors whitespace-nowrap">
          Upload Logo
        </button>
      </div>
      <p class="text-xs text-gray-400">Recommended: transparent background, min 200px wide, any landscape ratio.</p>
    </form>

    <!-- Remove -->
    <?php if ($currentLogo): ?>
      <form method="POST" action="/admin/content/branding/logo/remove" class="mt-3">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit"
          onclick="return confirm('Remove the website logo?')"
          class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
          Remove website logo
        </button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Mobile Logo -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-1">Mobile Logo</h2>
    <p class="text-sm text-gray-400 mb-5">
      Shown on screens smaller than 768px (hamburger menu view). Use a square icon or compact version of your logo for best results.
      Falls back to the Website Logo if not set.
    </p>

    <!-- Preview -->
    <div class="mb-5">
      <?php if ($currentMobileLogo): ?>
        <div class="flex items-center gap-4">
          <div class="border border-gray-200 rounded-xl bg-gray-50 flex items-center justify-center px-4 py-3" style="width:80px;height:68px;">
            <img src="<?= Helpers::e($currentMobileLogo) ?>?v=<?= time() ?>" alt="Mobile logo" class="max-h-10 max-w-[56px] object-contain">
          </div>
          <div>
            <div class="text-sm font-medium text-gray-700">Current mobile logo</div>
            <div class="text-xs text-gray-400 mt-0.5">Shown on mobile nav (below 768px)</div>
          </div>
        </div>
      <?php else: ?>
        <div class="flex items-center gap-3 px-5 py-4 bg-gray-50 border border-dashed border-gray-300 rounded-xl">
          <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-600">No mobile logo uploaded</div>
            <div class="text-xs text-gray-400 mt-0.5">The Website Logo will be used as fallback</div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Upload -->
    <form method="POST" action="/admin/content/branding/mobile-logo" enctype="multipart/form-data" class="space-y-3">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <div class="flex items-center gap-3 flex-wrap">
        <label class="block flex-1 min-w-0">
          <span class="sr-only">Choose mobile logo</span>
          <input type="file" name="mobile_logo" accept="image/jpeg,image/png,image/webp"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
        </label>
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors whitespace-nowrap">
          Upload Mobile Logo
        </button>
      </div>
      <p class="text-xs text-gray-400">Recommended: square icon/symbol, at least 80×80px, transparent background PNG.</p>
    </form>

    <!-- Remove -->
    <?php if ($currentMobileLogo): ?>
      <form method="POST" action="/admin/content/branding/mobile-logo/remove" class="mt-3">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <button type="submit"
          onclick="return confirm('Remove the mobile logo?')"
          class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
          Remove mobile logo
        </button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Info box -->
  <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
    <strong>Logo priority:</strong> Website Logo (desktop) + Mobile Logo (mobile) → Settings Logo → Default icon + company name.
    Use <a href="/admin/settings#branding" class="underline font-medium">Settings → Branding</a> to set a logo for the admin panel and emails.
  </div>

</div>
