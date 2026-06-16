<?php $c = function($k,$d=''){return htmlspecialchars(ContentController::get('industries',$k,$d));}; ?>
<section class="bg-gradient-to-b from-gray-50 to-white py-24 px-4 text-center">
  <div class="max-w-3xl mx-auto">
    <h1 class="text-5xl font-extrabold text-gray-900 mb-5"><?= $c('title','Built for your industry') ?></h1>
    <p class="text-xl text-gray-500"><?= $c('subtitle') ?></p>
  </div>
</section>
<section class="py-20 px-4">
  <div class="max-w-6xl mx-auto">
    <?php for ($i = 1; $i <= 6; $i++): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12 items-center mb-24 last:mb-0">
      <div>
        <div class="text-5xl mb-4"><?= $c("i{$i}_icon") ?></div>
        <h2 class="text-3xl font-bold text-gray-900 mb-4"><?= $c("i{$i}_name") ?></h2>
        <p class="text-gray-500 leading-relaxed mb-6"><?= $c("i{$i}_desc") ?></p>
        <a href="/register" class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl transition-colors text-sm">Start Free Trial →</a>
      </div>
      <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl p-10 flex items-center justify-center">
        <div class="text-center">
          <div class="text-5xl sm:text-8xl mb-4"><?= $c("i{$i}_icon") ?></div>
          <div class="font-bold text-gray-700 text-lg"><?= $c("i{$i}_name") ?></div>
        </div>
      </div>
    </div>
    <?php endfor; ?>
  </div>
</section>
<section class="bg-indigo-600 py-20 px-4 text-center text-white">
  <h2 class="text-3xl font-bold mb-3"><?= $c('cta_title','Ready to modernise your screens?') ?></h2>
  <a href="/register" class="inline-block mt-6 bg-white text-indigo-600 font-bold px-10 py-4 rounded-xl hover:bg-gray-50 transition-all shadow-lg text-sm"><?= $c('cta_button','Start Your 14-Day Free Trial') ?></a>
</section>
