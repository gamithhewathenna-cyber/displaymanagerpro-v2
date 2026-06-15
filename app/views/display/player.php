<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helpers::e($channel['name']) ?></title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
html, body { width:100%; height:100%; overflow:hidden; background:<?= Helpers::e($channel['background_color']) ?>; }
#player { position:fixed; inset:0; }
.slide {
  position:absolute; inset:0; opacity:0; transition:none;
  display:flex; align-items:center; justify-content:center;
  background:<?= Helpers::e($channel['background_color']) ?>;
}
.slide img { max-width:100%; max-height:100%; object-fit:contain; display:block; }
.slide.active { opacity:1; z-index:1; }

/* Transitions */
.fx-fade .slide { transition:opacity 0.8s ease; }
.fx-fade .slide.active { opacity:1; }

.fx-slide-left .slide { transition:transform 0.7s cubic-bezier(.4,0,.2,1), opacity 0.7s; transform:translateX(100%); opacity:0; }
.fx-slide-left .slide.active { transform:translateX(0); opacity:1; }
.fx-slide-left .slide.prev { transform:translateX(-100%); opacity:0; }

.fx-slide-right .slide { transition:transform 0.7s cubic-bezier(.4,0,.2,1), opacity 0.7s; transform:translateX(-100%); opacity:0; }
.fx-slide-right .slide.active { transform:translateX(0); opacity:1; }
.fx-slide-right .slide.prev { transform:translateX(100%); opacity:0; }

.fx-zoom .slide { transition:transform 0.9s ease, opacity 0.9s ease; transform:scale(1.08); }
.fx-zoom .slide.active { transform:scale(1); opacity:1; }

.fx-crossfade .slide { transition:opacity 1.2s ease; }
.fx-crossfade .slide.active { opacity:1; }

#dots { position:fixed; bottom:16px; left:50%; transform:translateX(-50%); display:flex; gap:6px; z-index:10; }
#dots span { width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,.4); transition:background .3s; }
#dots span.active { background:rgba(255,255,255,.9); }
</style>
</head>
<body>
<div id="player" class="fx-<?= Helpers::e($channel['transition_effect']) ?>">
  <?php foreach ($slides as $i => $slide): ?>
  <?php $rot = (int)($slide['rotation'] ?? 0); ?>
  <div class="slide <?= $i === 0 ? 'active' : '' ?>" data-duration="<?= $slide['duration_override'] ?? $channel['slide_duration'] ?>">
    <img src="<?= Helpers::e($slide['public_url']) ?>" alt="" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
         <?= $rot ? 'style="transform:rotate(' . $rot . 'deg);max-width:' . ($rot===90||$rot===270?'100vh':'100%') . ';max-height:' . ($rot===90||$rot===270?'100vw':'100%') . '"' : '' ?>>
  </div>
  <?php endforeach; ?>
</div>

<?php if (count($slides) > 1): ?>
<div id="dots">
  <?php foreach ($slides as $i => $s): ?>
  <span class="<?= $i===0?'active':'' ?>"></span>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
(function() {
  const slides    = document.querySelectorAll('.slide');
  const dots      = document.querySelectorAll('#dots span');
  const refreshMs = <?= (int)$channel['auto_refresh_minutes'] ?> * 60 * 1000;
  let current = 0;
  let timer;

  function goTo(idx) {
    slides[current].classList.remove('active');
    slides[current].classList.add('prev');
    dots[current]?.classList.remove('active');

    current = (idx + slides.length) % slides.length;
    slides[current].classList.remove('prev');
    slides[current].classList.add('active');
    dots[current]?.classList.add('active');

    // Remove 'prev' after transition
    setTimeout(() => slides.forEach(s => s.classList.remove('prev')), 1200);
    schedulNext();
  }

  function schedulNext() {
    clearTimeout(timer);
    const dur = parseInt(slides[current].dataset.duration || '8') * 1000;
    timer = setTimeout(() => goTo(current + 1), dur);
  }

  schedulNext();

  // Auto-refresh to pick up new content
  if (refreshMs > 0) {
    setTimeout(() => location.reload(), refreshMs);
  }

  // Fullscreen on click (for TV setups)
  document.body.addEventListener('click', () => {
    if (!document.fullscreenElement) document.documentElement.requestFullscreen?.();
  });

  // Preload next slide
  slides.forEach((s, i) => {
    const img = s.querySelector('img');
    if (img && i > 0) {
      const preload = new Image();
      preload.src = img.src;
    }
  });
})();
</script>
</body>
</html>
