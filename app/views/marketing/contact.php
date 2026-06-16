<?php $c = function($k,$d=''){return htmlspecialchars(ContentController::get('contact',$k,$d));}; ?>
<section class="py-24 px-4">
  <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-16 items-start">
    <div>
      <h1 class="text-5xl font-extrabold text-gray-900 mb-5"><?= $c('title','Get in touch') ?></h1>
      <p class="text-gray-500 text-lg mb-10"><?= $c('subtitle') ?></p>
      <div class="space-y-6">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-0.5"><?= $c('email_label','Email Support') ?></div>
            <div class="text-gray-500 text-sm"><?= $c('email_desc') ?></div>
          </div>
        </div>
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <div class="font-semibold text-gray-900 mb-0.5"><?= $c('hours_label','Business Hours') ?></div>
            <div class="text-gray-500 text-sm"><?= $c('hours_value') ?></div>
          </div>
        </div>
      </div>
      <div class="mt-12 bg-indigo-600 rounded-2xl p-6 text-white">
        <div class="font-bold text-lg mb-2"><?= $c('cta_title','Not a customer yet?') ?></div>
        <p class="text-indigo-200 text-sm mb-4"><?= $c('cta_desc') ?></p>
        <a href="/register" class="inline-block bg-white text-indigo-600 font-bold px-5 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-colors"><?= $c('cta_button','Start Free Trial →') ?></a>
      </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
      <h2 class="text-lg font-bold text-gray-900 mb-6"><?= $c('form_title','Send us a message') ?></h2>
      <form method="POST" action="/contact" class="space-y-5">
        <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Your name</label>
          <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Jane Smith"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
          <input type="email" name="email" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="jane@example.com"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
          <textarea name="message" required rows="5" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" placeholder="Tell us what you need…"></textarea></div>
        <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm">Send Message</button>
      </form>
    </div>
  </div>
</section>
