<h2 class="text-2xl font-bold text-gray-900 mb-1">Start your free trial</h2>
<p class="text-gray-500 text-sm mb-7">14 days free · No credit card required</p>

<form method="POST" action="/register" class="space-y-4">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <?php $old = Session::getFlash('old') ?? []; ?>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
    <input type="text" name="name" required value="<?= Helpers::e($old['name'] ?? '') ?>"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="Jane Smith">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
    <input type="email" name="email" required value="<?= Helpers::e($old['email'] ?? '') ?>"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="you@example.com">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
    <input type="password" name="password" required minlength="8"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="At least 8 characters">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Choose your plan</label>
    <div class="space-y-2">
      <?php foreach ($plans as $plan): ?>
      <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3.5 cursor-pointer hover:border-primary-300 transition-colors has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
        <input type="radio" name="plan" value="<?= Helpers::e($plan['slug']) ?>"
          <?= (($_GET['plan'] ?? 'starter') === $plan['slug'] || ($plan['slug'] === 'starter' && !isset($_GET['plan']))) ? 'checked' : '' ?>
          class="text-primary-500 focus:ring-primary-500">
        <div class="flex-1 flex items-center justify-between">
          <div>
            <div class="font-semibold text-sm text-gray-900"><?= Helpers::e($plan['name']) ?></div>
            <div class="text-xs text-gray-400">Up to <?= $plan['max_screens'] ?> screen<?= $plan['max_screens'] > 1 ? 's' : '' ?></div>
          </div>
          <div class="font-bold text-gray-900 text-sm">$<?= number_format($plan['price_monthly'], 0) ?>/mo</div>
        </div>
      </label>
      <?php endforeach; ?>
    </div>
  </div>
  <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors text-sm mt-2">
    Create Account – Free for 14 Days
  </button>
  <p class="text-xs text-gray-400 text-center">By creating an account you agree to our Terms of Service and Privacy Policy.</p>
</form>

<p class="text-center text-sm text-gray-500 mt-5">
  Already have an account?
  <a href="/login" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
</p>
