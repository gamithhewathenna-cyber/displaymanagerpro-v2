<?php $c = function($k,$d=''){return htmlspecialchars(ContentController::get('faq',$k,$d));}; ?>
<section class="py-24 px-4">
  <div class="max-w-3xl mx-auto">
    <div class="text-center mb-14">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-4"><?= $c('title','Frequently Asked Questions') ?></h1>
      <p class="text-gray-500 text-lg"><?= $c('subtitle') ?></p>
    </div>
    <div class="space-y-3 mb-12">
      <?php for ($i = 1; $i <= 10; $i++):
        $q = ContentController::get('faq', "q{$i}");
        $a = ContentController::get('faq', "a{$i}");
        if (!$q) continue;
      ?>
      <details class="group bg-white border border-gray-100 rounded-2xl overflow-hidden hover:border-indigo-100 transition-colors">
        <summary class="flex items-center justify-between px-6 py-5 cursor-pointer list-none font-semibold text-gray-900 text-sm">
          <?= htmlspecialchars($q) ?>
          <svg class="w-5 h-5 text-gray-400 flex-shrink-0 ml-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </summary>
        <div class="px-6 pb-5 text-gray-500 text-sm leading-relaxed border-t border-gray-50 pt-4"><?= htmlspecialchars($a) ?></div>
      </details>
      <?php endfor; ?>
    </div>
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 sm:p-8 text-center">
      <div class="text-3xl mb-3">💬</div>
      <h3 class="font-bold text-gray-900 text-lg mb-2"><?= $c('still_title','Still have questions?') ?></h3>
      <p class="text-gray-500 text-sm mb-5"><?= $c('still_subtitle') ?></p>
      <a href="/contact" class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl text-sm transition-colors"><?= $c('still_button','Contact Us') ?></a>
    </div>
  </div>
</section>
