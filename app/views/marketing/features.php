<?php $c = function($k,$d=''){return htmlspecialchars(ContentController::get('features',$k,$d));}; ?>
<section class="py-24 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-16">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-4"><?= $c('title','Everything your screens need') ?></h1>
      <p class="text-xl text-gray-500 max-w-2xl mx-auto"><?= $c('subtitle') ?></p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <?php for ($i = 1; $i <= 12; $i++): ?>
      <div class="bg-white border border-gray-100 rounded-2xl p-6 flex gap-4 hover:shadow-md hover:border-indigo-100 transition-all">
        <div class="text-3xl flex-shrink-0"><?= $c("f{$i}_icon") ?></div>
        <div>
          <h3 class="font-semibold text-gray-900 mb-1"><?= $c("f{$i}_title") ?></h3>
          <p class="text-gray-500 text-sm leading-relaxed"><?= $c("f{$i}_desc") ?></p>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>
<section class="bg-indigo-600 text-white py-20 px-4 text-center">
  <h2 class="text-3xl font-bold mb-4"><?= $c('cta_title','Ready to get started?') ?></h2>
  <p class="text-indigo-200 mb-8"><?= $c('cta_subtitle') ?></p>
  <a href="/register" class="inline-block bg-white text-indigo-600 font-bold px-10 py-4 rounded-xl hover:bg-gray-50 transition-all shadow-lg"><?= $c('cta_button','Start Free Trial →') ?></a>
</section>
