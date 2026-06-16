<div class="space-y-6">

  <!-- Branding highlight -->
  <a href="/admin/content/branding" class="flex items-center gap-5 bg-white rounded-2xl border border-primary-200 hover:border-primary-400 hover:shadow-md p-6 transition-all group">
    <?php $__wl = Settings::get('website_logo', ''); ?>
    <div class="w-16 h-16 rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden <?= $__wl ? 'bg-gray-50 border border-gray-200' : 'bg-primary-50' ?>">
      <?php if ($__wl): ?>
        <img src="<?= Helpers::e($__wl) ?>" alt="Website logo" class="max-h-12 max-w-[56px] object-contain">
      <?php else: ?>
        <svg class="w-7 h-7 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      <?php endif; ?>
    </div>
    <div class="flex-1 min-w-0">
      <div class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">Website Logo</div>
      <div class="text-sm text-gray-400 mt-0.5">Logo shown in the public nav bar and footer</div>
      <div class="mt-2 text-xs <?= $__wl ? 'text-green-600 font-medium' : 'text-gray-400' ?>">
        <?= $__wl ? '✓ Logo uploaded' : 'No logo — using fallback' ?>
      </div>
    </div>
    <div class="text-xs text-primary-600 font-medium group-hover:underline whitespace-nowrap">Edit logo →</div>
  </a>

  <!-- Page cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php
      $pages = [
        ['home',       '🏠', 'Homepage',    'Hero, stats, features, how-it-works, testimonials, CTA'],
        ['features',   '⚡', 'Features',    '12 feature cards, page title, CTA section'],
        ['industries', '🏢', 'Industries',  '6 industry sections with descriptions'],
        ['pricing',    '💳', 'Pricing',     'SEO title, meta description, focus keyphrase'],
        ['faq',        '❓', 'FAQ',         '10 questions & answers, section titles'],
        ['contact',    '📬', 'Contact',     'Contact info, hours, form title'],
        ['footer',     '🔗', 'Footer',      'Tagline, navigation columns, copyright'],
        ['privacy',    '📄', 'Privacy Policy',     'Full privacy policy content, title, last updated date'],
        ['terms',      '📋', 'Terms & Conditions', 'Full terms content, title, last updated date'],
        ['refund',     '💰', 'Refund Policy',      'Full refund policy content, title, last updated date'],
      ];
      foreach ($pages as [$slug, $icon, $name, $desc]):
    ?>
    <a href="/admin/content/<?= $slug ?>" class="bg-white rounded-2xl border border-gray-100 hover:border-primary-300 hover:shadow-md p-6 transition-all group">
      <div class="text-3xl mb-3"><?= $icon ?></div>
      <div class="font-semibold text-gray-900 mb-1 group-hover:text-primary-600 transition-colors"><?= $name ?> Page</div>
      <div class="text-sm text-gray-400"><?= $desc ?></div>
      <div class="mt-4 text-xs text-primary-600 font-medium group-hover:underline">Edit content →</div>
    </a>
    <?php endforeach; ?>
  </div>

</div>
