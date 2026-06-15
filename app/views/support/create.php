<div class="max-w-2xl">
  <a href="/support" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Back to tickets</a>
  <div class="bg-white rounded-2xl border border-gray-100 p-7">
    <h2 class="text-lg font-bold text-gray-900 mb-5">Open a Support Ticket</h2>
    <form method="POST" action="/support" class="space-y-5">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
        <input type="text" name="subject" required placeholder="Brief description of your issue"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Priority</label>
        <select name="priority" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option value="low">Low</option>
          <option value="medium" selected>Medium</option>
          <option value="high">High</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
        <textarea name="message" required rows="6" placeholder="Please describe your issue in detail…"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"></textarea>
      </div>
      <div class="flex gap-3">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Submit Ticket</button>
        <a href="/support" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Cancel</a>
      </div>
    </form>
  </div>
</div>
