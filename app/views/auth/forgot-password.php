<h2 class="text-2xl font-bold text-gray-900 mb-1">Forgot your password?</h2>
<p class="text-gray-500 text-sm mb-7">Enter your email and we'll send a reset link.</p>
<form method="POST" action="/forgot-password" class="space-y-5">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
    <input type="email" name="email" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all" placeholder="you@example.com">
  </div>
  <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm">Send Reset Link</button>
</form>
<p class="text-center text-sm text-gray-500 mt-6"><a href="/login" class="text-primary-600 font-semibold">← Back to sign in</a></p>
