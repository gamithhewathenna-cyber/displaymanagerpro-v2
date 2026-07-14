<div class="max-w-xl w-full">
  <a href="/admin/customers" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← All customers</a>
  <div class="bg-white rounded-xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Create Account</h2>
    <form method="POST" action="/admin/customers" class="space-y-4">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

      <div><label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input type="text" name="name" required minlength="2" placeholder="Jane Doe" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>

      <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required placeholder="jane@example.com" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>

      <div><label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" required minlength="<?= PASSWORD_MIN_LENGTH ?>" placeholder="Min <?= PASSWORD_MIN_LENGTH ?> characters" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"></div>

      <div class="grid grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select name="role" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="customer" selected>Customer</option>
            <option value="admin">Admin</option>
          </select></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="active" selected>Active</option>
            <option value="pending">Pending</option>
            <option value="suspended">Suspended</option>
          </select>
          <p class="text-xs text-gray-400 mt-1">Active accounts can sign in immediately.</p></div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Create Account</button>
        <a href="/admin/customers" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">Cancel</a>
      </div>
    </form>
  </div>
</div>
