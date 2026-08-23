<h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
<p class="text-gray-500 text-sm mb-7">Sign in to your account</p>

<form method="POST" action="/login" class="space-y-5">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
    <input type="email" name="email" required autocomplete="email"
      value="<?= Helpers::e(Session::getFlash('old')['email'] ?? '') ?>"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all placeholder-gray-400"
      placeholder="you@example.com">
  </div>
  <div>
    <div class="flex items-center justify-between mb-1.5">
      <label class="block text-sm font-medium text-gray-700">Password</label>
      <a href="/forgot-password" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Forgot password?</a>
    </div>
    <input type="password" name="password" required autocomplete="current-password"
      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
      placeholder="••••••••">
  </div>
  <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors shadow-sm text-sm">
    Sign In
  </button>
</form>

<p class="text-center text-sm text-gray-500 mt-6">
  Don't have an account?
  <a href="/register" class="text-primary-600 font-semibold hover:text-primary-700">Start Free Today!</a>
</p>
