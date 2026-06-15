<h2 class="text-2xl font-bold text-gray-900 mb-1">Set a new password</h2>
<p class="text-gray-500 text-sm mb-7">Must be at least 8 characters.</p>
<form method="POST" action="/reset-password" class="space-y-5">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
  <input type="hidden" name="token" value="<?= Helpers::e($token) ?>">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">New password</label>
    <input type="password" name="password" required minlength="8" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="••••••••">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm new password</label>
    <input type="password" name="password_confirm" required minlength="8" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="••••••••">
  </div>
  <button type="submit" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 rounded-xl transition-colors text-sm">Update Password</button>
</form>
