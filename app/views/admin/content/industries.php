<a href="/admin/content" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-5">← Content Manager</a>
<form method="POST" action="/admin/content/industries/save" class="space-y-6">
  <input type="hidden" name="_csrf_token" value="<?= Csrf::token() ?>">

  <!-- HERO -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Hero Section</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Hero Title</label>
        <input type="text" name="hero_title" value="<?= htmlspecialchars($content['hero_title'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Hero Body</label>
        <textarea name="hero_body" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['hero_body'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <!-- SECTION 1 – MULTI-LOCATION -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Section 1 — Multi-Location Businesses</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="s1_title" value="<?= htmlspecialchars($content['s1_title'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Body Paragraph 1</label>
          <textarea name="s1_body1" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s1_body1'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Body Paragraph 2</label>
          <textarea name="s1_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s1_body2'] ?? '') ?></textarea>
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Intro Line <span class="text-gray-400 font-normal">(shown before bullet list)</span></label>
        <input type="text" name="s1_intro" value="<?= htmlspecialchars($content['s1_intro'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Bullet Points <span class="text-gray-400 font-normal">(one per line)</span></label>
        <textarea name="s1_bullets" rows="6" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s1_bullets'] ?? '') ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Footer Note</label>
        <textarea name="s1_footer" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s1_footer'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <!-- SECTION 2 – INCREASE SALES -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Section 2 — Increase Sales</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="s2_title" value="<?= htmlspecialchars($content['s2_title'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Body Paragraph 1</label>
          <textarea name="s2_body1" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s2_body1'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Body Paragraph 2</label>
          <textarea name="s2_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s2_body2'] ?? '') ?></textarea>
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Intro Line <span class="text-gray-400 font-normal">(shown before bullet list)</span></label>
        <input type="text" name="s2_intro" value="<?= htmlspecialchars($content['s2_intro'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Bullet Points <span class="text-gray-400 font-normal">(one per line)</span></label>
        <textarea name="s2_bullets" rows="6" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s2_bullets'] ?? '') ?></textarea>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Footer Note</label>
        <textarea name="s2_footer" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s2_footer'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <!-- SECTION 3 – WHY ESSENTIAL -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Section 3 — Why Digital Signage Is Essential</h2>
    <div class="space-y-4 mb-6">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
        <input type="text" name="s3_title" value="<?= htmlspecialchars($content['s3_title'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Intro Paragraph 1</label>
          <textarea name="s3_body1" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s3_body1'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Intro Paragraph 2</label>
          <textarea name="s3_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s3_body2'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Benefit Cards</h3>
    <div class="space-y-4">
      <?php
        $s3defs = [
          ['b1','🎯','Improve Customer Experience','Display menus, services, promotions, and important information clearly and professionally.'],
          ['b2','⏱️','Save Time','Update all screens remotely without manually replacing printed materials.'],
          ['b3','💰','Reduce Costs','Eliminate ongoing printing and distribution expenses.'],
          ['b4','✅','Maintain Brand Consistency','Ensure every location displays the same branding, messaging, and promotions.'],
          ['b5','📈','Scale Easily','Add new locations and screens without changing your workflow.'],
        ];
        foreach ($s3defs as [$key, $defIcon, $defTitle, $defDesc]):
      ?>
      <div class="bg-gray-50 rounded-xl p-4">
        <div class="flex items-center gap-3 mb-2">
          <input type="text" name="s3_<?= $key ?>_icon" value="<?= htmlspecialchars($content["s3_{$key}_icon"] ?? $defIcon) ?>" class="w-12 border border-gray-200 rounded-lg px-2 py-2 text-center text-xl focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="emoji">
          <input type="text" name="s3_<?= $key ?>_title" value="<?= htmlspecialchars($content["s3_{$key}_title"] ?? $defTitle) ?>" class="flex-1 border border-gray-200 rounded-lg px-4 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Benefit title">
        </div>
        <textarea name="s3_<?= $key ?>_desc" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Description"><?= htmlspecialchars($content["s3_{$key}_desc"] ?? $defDesc) ?></textarea>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SECTION 4 – REGIONS -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-5">Section 4 — Regions / Global Coverage</h2>
    <div class="mb-4">
      <label class="block text-xs font-medium text-gray-500 mb-1">Section Title</label>
      <input type="text" name="s4_title" value="<?= htmlspecialchars($content['s4_title'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <?php
        $regionDefs = [
          ['au','Australia','Sydney, Melbourne, Brisbane, Perth, Adelaide, Gold Coast and regional locations.'],
          ['nz','New Zealand','Auckland, Wellington, Christchurch, Hamilton, Tauranga and beyond.'],
          ['uk','United Kingdom','London, Manchester, Birmingham, Liverpool, Leeds, Bristol and nationwide.'],
          ['us','United States','New York, Los Angeles, Chicago, Houston, Dallas, Miami, Seattle and across all states.'],
        ];
        foreach ($regionDefs as [$rkey, $defName, $defCities]):
      ?>
      <div class="bg-gray-50 rounded-xl p-4">
        <div class="mb-2">
          <label class="block text-xs font-medium text-gray-500 mb-1">Country Name</label>
          <input type="text" name="s4_<?= $rkey ?>_title" value="<?= htmlspecialchars($content["s4_{$rkey}_title"] ?? $defName) ?>" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Cities / Locations</label>
          <textarea name="s4_<?= $rkey ?>_cities" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content["s4_{$rkey}_cities"] ?? $defCities) ?></textarea>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div>
      <label class="block text-xs font-medium text-gray-500 mb-1">Footer Note</label>
      <textarea name="s4_footer" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['s4_footer'] ?? '') ?></textarea>
    </div>
  </div>

  <!-- CTA -->
  <div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h2 class="font-semibold text-gray-900 mb-4">Bottom CTA</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
        <input type="text" name="cta_title" value="<?= htmlspecialchars($content['cta_title'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Body Paragraph 1</label>
          <textarea name="cta_body1" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['cta_body1'] ?? '') ?></textarea>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-500 mb-1">Body Paragraph 2</label>
          <textarea name="cta_body2" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500"><?= htmlspecialchars($content['cta_body2'] ?? '') ?></textarea>
        </div>
      </div>
      <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Button Text</label>
        <input type="text" name="cta_button" value="<?= htmlspecialchars($content['cta_button'] ?? '') ?>" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
    </div>
  </div>

  <?php require VIEWS_PATH . '/admin/content/_seo_fields.php'; ?>

  <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-3 rounded-xl transition-colors">Save Industries Content</button>
</form>
