<div class="max-w-2xl">
  <a href="/channels" class="text-sm text-gray-400 hover:text-gray-600 mb-6 inline-flex items-center gap-1">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to channels
  </a>

  <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-7 mt-3">
    <h2 class="text-lg font-bold text-gray-900 mb-1">Create a new channel</h2>
    <p class="text-sm text-gray-400 mb-7">Each channel displays on one TV screen using a unique URL.</p>

    <form method="POST" action="/channels" class="space-y-6">
      <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Channel name <span class="text-red-400">*</span></label>
          <input type="text" name="name" required placeholder="e.g. Main Menu Board, Bar Screen, Specials"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Description <span class="text-gray-400 font-normal">(optional)</span></label>
          <input type="text" name="description" placeholder="e.g. Displays next to the front counter"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Orientation</label>
          <select name="orientation" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="landscape">Landscape (16:9) – Standard TV</option>
            <option value="portrait">Portrait (9:16) – Vertical display</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Slide duration (seconds)</label>
          <input type="number" name="slide_duration" value="8" min="3" max="60"
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Transition effect</label>
          <select name="transition" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="fade">Fade</option>
            <option value="slide-left">Slide Left</option>
            <option value="slide-right">Slide Right</option>
            <option value="zoom">Zoom</option>
            <option value="crossfade">Crossfade</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Auto refresh every</label>
          <select name="auto_refresh" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <option value="5">5 minutes</option>
            <option value="15" selected>15 minutes</option>
            <option value="30">30 minutes</option>
            <option value="60">60 minutes</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Background colour</label>
          <div class="flex gap-2 items-center">
            <input type="color" name="bg_color" value="#000000" class="w-12 h-11 border border-gray-200 rounded-xl cursor-pointer p-1">
            <span class="text-sm text-gray-400">Shown between slides</span>
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-7 py-3 rounded-xl transition-colors text-sm">
          Create Channel
        </button>
        <a href="/channels" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-7 py-3 rounded-xl transition-colors text-sm">Cancel</a>
      </div>
    </form>
  </div>
</div>
