<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
  <?php
    $pages = [
      ['home',       '🏠', 'Homepage',    'Hero, stats, features, how-it-works, testimonials, CTA'],
      ['features',   '⚡', 'Features',    '12 feature cards, page title, CTA section'],
      ['industries', '🏢', 'Industries',  '6 industry sections with descriptions'],
      ['faq',        '❓', 'FAQ',         '10 questions & answers, section titles'],
      ['contact',    '📬', 'Contact',     'Contact info, hours, form title'],
      ['footer',     '🔗', 'Footer',      'Tagline, navigation columns, copyright'],
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
