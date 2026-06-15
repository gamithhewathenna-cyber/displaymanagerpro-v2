<section class="py-24 px-4">
  <div class="max-w-4xl mx-auto text-center mb-16">
    <h1 class="text-5xl font-extrabold text-gray-900 mb-4">Simple, honest pricing</h1>
    <p class="text-xl text-gray-500">Start free for 14 days. No credit card required. Cancel anytime.</p>
  </div>
  <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($plans as $i => $plan): ?>
    <div class="bg-white border-2 <?= $i===1?'border-indigo-500 shadow-xl shadow-indigo-100':'border-gray-100' ?> rounded-2xl p-8 relative">
      <?php if ($i===1): ?><div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-500 text-white text-xs font-bold px-4 py-1.5 rounded-full">MOST POPULAR</div><?php endif; ?>
      <div class="text-sm font-semibold text-gray-400 mb-2"><?= Helpers::e($plan['name']) ?></div>
      <div class="text-4xl font-extrabold text-gray-900 mb-1">$<?= number_format($plan['price_monthly'],0) ?><span class="text-lg font-normal text-gray-400"> USD/mo</span></div>
      <div class="text-sm text-gray-400 mb-6">Up to <?= $plan['max_screens'] ?> TV screen<?= $plan['max_screens']>1?'s':'' ?></div>
      <?php $feats = json_decode($plan['features']??'[]',true); ?>
      <ul class="space-y-2.5 mb-8">
        <?php foreach ($feats as $f): ?>
        <li class="flex items-center gap-2 text-sm text-gray-600">
          <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
          <?= Helpers::e($f) ?>
        </li>
        <?php endforeach; ?>
      </ul>
      <a href="/register?plan=<?= Helpers::e($plan['slug']) ?>" class="block text-center <?= $i===1?'bg-indigo-500 hover:bg-indigo-600 text-white':'bg-gray-50 hover:bg-gray-100 text-gray-900' ?> font-semibold py-3 rounded-xl transition-colors">Start Free Trial</a>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="mt-12 text-center text-sm text-gray-400">
    All prices in USD. <a href="/contact" class="text-indigo-600 hover:text-indigo-700 font-medium">Need custom pricing?</a>
  </div>
</section>
