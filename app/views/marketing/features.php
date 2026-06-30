<?php $c = function($k,$d=''){return htmlspecialchars(ContentController::get('features',$k,$d));}; ?>
<section class="py-24 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-16">
      <h1 class="text-3xl sm:text-5xl font-extrabold text-gray-900 mb-4"><?= $c('title','Everything your screens need') ?></h1>
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
<!-- RELATED PAGES -->
<section class="bg-gray-50 border-y border-gray-100 py-10 px-4">
  <div class="max-w-5xl mx-auto">
    <p class="text-center text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">You May Also Be Interested In</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <a href="/pricing" class="bg-white border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-4 transition-all group hover:shadow-md">
        <span class="text-3xl flex-shrink-0">💳</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Pricing & Plans</div>
          <div class="text-xs text-gray-400 mt-0.5">Find the right plan for your business</div>
        </div>
      </a>
      <a href="/industries" class="bg-white border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-4 transition-all group hover:shadow-md">
        <span class="text-3xl flex-shrink-0">🏢</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Industries We Serve</div>
          <div class="text-xs text-gray-400 mt-0.5">See how businesses in your industry use digital signage</div>
        </div>
      </a>
      <a href="/faq" class="bg-white border border-gray-100 hover:border-primary-300 rounded-2xl p-5 flex items-center gap-4 transition-all group hover:shadow-md">
        <span class="text-3xl flex-shrink-0">❓</span>
        <div>
          <div class="text-sm font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">Frequently Asked Questions</div>
          <div class="text-xs text-gray-400 mt-0.5">Answers to common questions about our platform</div>
        </div>
      </a>
    </div>
  </div>
</section>
<?php require VIEWS_PATH . '/marketing/_cta.php'; ?>
