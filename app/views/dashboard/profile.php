<div class="max-w-xl">
  <div class="bg-white rounded-2xl border border-gray-100 p-7">
    <h2 class="text-lg font-bold text-gray-900 mb-6">Account Profile</h2>
    <form method="POST" action="/profile" class="space-y-5">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full name</label>
        <input type="text" name="name" required value="<?= Helpers::e($user['name']) ?>"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
        <input type="email" value="<?= Helpers::e($user['email']) ?>" disabled
          class="w-full border border-gray-100 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
        <p class="text-xs text-gray-400 mt-1">Email cannot be changed. Contact support if needed.</p>
      </div>

      <div class="border-t border-gray-100 pt-5">
        <h3 class="font-semibold text-gray-900 mb-4 text-sm">Change Password</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Current password</label>
            <input type="password" name="current_password" autocomplete="current-password"
              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Leave blank to keep current">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">New password</label>
              <input type="password" name="new_password" autocomplete="new-password" minlength="8"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Min. 8 characters">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm new password</label>
              <input type="password" name="confirm_password" autocomplete="new-password"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Repeat password">
            </div>
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Save Changes</button>
        <a href="/dashboard" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Cancel</a>
      </div>
    </form>
  </div>
</div>
