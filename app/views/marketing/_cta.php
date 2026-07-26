<?php
$_ctaBg      = Settings::get('content_home_cta_bg', '');
$_ctaTitle   = htmlspecialchars(ContentController::get('home', 'cta_title',   'Ready to modernise your screens?'));
$_ctaSub     = htmlspecialchars(ContentController::get('home', 'cta_subtitle', 'Start your free 14-day trial. No credit card required. Cancel anytime.'));
$_ctaButton  = htmlspecialchars(ContentController::get('home', 'cta_button',  'Get Started Free →'));
?>
<!-- CTA -->
<section class="hero-gradient text-white py-24 px-4 relative overflow-hidden bg-cover bg-center"<?= $_ctaBg ? ' style="background-image:url(' . Helpers::e($_ctaBg) . ')"' : '' ?>>
  <?php if (!$_ctaBg): ?>
  <div class="absolute inset-0 pointer-events-none opacity-10" style="background-image:radial-gradient(circle at 20% 50%,#ec4899 0%,transparent 50%),radial-gradient(circle at 80% 20%,#f97316 0%,transparent 40%)"></div>
  <?php endif; ?>
  <div class="max-w-[1250px] mx-auto text-center relative">
    <p class="text-xs font-bold tracking-widest uppercase text-primary-300 mb-4">Get Started Today</p>
    <h2 class="text-3xl sm:text-4xl font-bold mb-5"><?= $_ctaTitle ?></h2>
    <p class="text-gray-300 mb-10 text-lg"><?= $_ctaSub ?></p>
    <a href="/register" class="inline-block bg-white text-primary-600 font-bold text-base sm:text-lg px-10 py-4 rounded-xl hover:bg-primary-50 transition-all shadow-xl">
      <?= $_ctaButton ?>
    </a>
    <p class="mt-6 text-sm text-gray-400">Have questions? <a href="/faq" class="text-white/80 hover:text-white underline underline-offset-2 transition-colors">Read our FAQ</a> or <a href="/contact" class="text-white/80 hover:text-white underline underline-offset-2 transition-colors">contact us</a>.</p>
  </div>
</section>

<!-- EXPLORE -->
<section class="bg-gray-900 py-10 px-4">
  <div class="max-w-[1200px] mx-auto">
    <p class="text-center text-xs font-semibold text-white/40 uppercase tracking-widest mb-6">Explore DisplayNex</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">

      <a href="/features" class="border border-white/10 rounded-2xl p-6 group block transition-all hover:bg-white/5 flex flex-col items-center justify-center gap-3 min-h-[140px] text-center">
        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span class="text-sm font-semibold text-white">All Features</span>
      </a>

      <a href="/pricing" class="border border-white/10 rounded-2xl p-6 group block transition-all hover:bg-white/5 flex flex-col items-center justify-center gap-3 min-h-[140px] text-center">
        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <span class="text-sm font-semibold text-white">Pricing Plans</span>
      </a>

      <a href="/industries" class="border border-white/10 rounded-2xl p-6 group block transition-all hover:bg-white/5 flex flex-col items-center justify-center gap-3 min-h-[140px] text-center">
        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <span class="text-sm font-semibold text-white">Industries</span>
      </a>

      <a href="/faq" class="border border-white/10 rounded-2xl p-6 group block transition-all hover:bg-white/5 flex flex-col items-center justify-center gap-3 min-h-[140px] text-center">
        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center group-hover:bg-white/20 transition-colors">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <span class="text-sm font-semibold text-white">FAQ</span>
      </a>

    </div>
  </div>
</section>
