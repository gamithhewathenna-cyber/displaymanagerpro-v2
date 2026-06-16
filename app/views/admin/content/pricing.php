<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>

<form method="POST" action="/admin/content/pricing/save" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

  <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-sm text-blue-700">
    The Pricing page content (plan names, prices, features) is managed via <a href="/admin/plans" class="font-semibold underline">Admin → Plans</a>. Use the fields below to control how this page appears in search results.
  </div>

  <?php require VIEWS_PATH . '/admin/content/_seo_fields.php'; ?>

  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Pricing SEO</button>
</form>
