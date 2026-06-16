<section class="py-20 bg-white">
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-10">
      <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2"><?= Helpers::e($content['title']) ?></h1>
      <?php if (!empty($content['last_updated'])): ?>
      <p class="text-sm text-gray-400">Last updated: <?= Helpers::e($content['last_updated']) ?></p>
      <?php endif; ?>
    </div>

    <div class="policy-content text-gray-700 leading-relaxed">
      <?= $content['body'] ?>
    </div>

    <div class="mt-14 pt-8 border-t border-gray-100 flex flex-col sm:flex-row gap-3 sm:gap-6 text-sm">
      <a href="/terms" class="text-indigo-600 hover:underline font-medium">Terms &amp; Conditions</a>
      <a href="/refund-policy" class="text-indigo-600 hover:underline font-medium">Refund Policy</a>
      <a href="/contact" class="text-indigo-600 hover:underline font-medium">Contact Us</a>
    </div>

  </div>
</section>

<style>
.policy-content h2 { font-size:1.2rem; font-weight:700; margin:2rem 0 .6rem; color:#111827; }
.policy-content h3 { font-size:1rem; font-weight:600; margin:1.5rem 0 .4rem; color:#374151; }
.policy-content p  { margin:.75rem 0; }
.policy-content ul { list-style:disc; padding-left:1.5rem; margin:.5rem 0; }
.policy-content ol { list-style:decimal; padding-left:1.5rem; margin:.5rem 0; }
.policy-content li { margin:.25rem 0; }
.policy-content strong { font-weight:600; }
.policy-content a  { color:#6366f1; text-decoration:underline; }
</style>
