<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$user = current_user();
$flash = get_flash();
$moods = mood_options();
$recentSongs = fetch_songs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | <?= esc(app_name()); ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="shell container">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">♪</div>
        <div>
          Welcome, <?= esc($user['username']); ?>
          <small>Pick a mood and start listening</small>
        </div>
      </div>
      <nav class="nav-links">
        <?php if (!empty($user['is_admin'])): ?>
          <a class="nav-link" href="/admin/admin.php">Admin Panel</a>
        <?php endif; ?>
        <a class="nav-link" href="/logout.php">Logout</a>
      </nav>
    </header>

    <?php if ($flash): ?>
      <div class="flash <?= esc($flash['type']); ?>"><?= esc($flash['message']); ?></div>
    <?php endif; ?>

    <section class="hero">
      <div class="hero-card">
        <div class="kicker">Mood selector</div>
        <h1>Choose how you feel right now.</h1>
        <p class="lead">
          The app will query the database, return songs that match your mood, and render them without reloading the page.
        </p>
        <div class="mood-grid">
          <?php foreach ($moods as $mood => $meta): ?>
            <button class="mood-button <?= $mood === 'Happy' ? 'active' : ''; ?>" data-mood="<?= esc($mood); ?>" type="button">
              <span class="mood-emoji"><?= esc($meta['emoji']); ?></span>
              <span>
                <strong><?= esc($meta['label']); ?></strong>
                <small class="muted"><?= esc($meta['hint']); ?></small>
              </span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="panel">
        <div class="section-head">
          <div>
            <h2>Library snapshot</h2>
            <p>Recently added tracks already in the database.</p>
          </div>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <strong><?= count($recentSongs); ?></strong>
            <span>Total songs stored</span>
          </div>
          <div class="stat-card">
            <strong>AJAX</strong>
            <span>Recommendations load dynamically from recommend.php.</span>
          </div>
          <div class="stat-card">
            <strong>HTML5 audio</strong>
            <span>Use built-in playback controls or the custom next button.</span>
          </div>
          <div class="stat-card">
            <strong>Responsive</strong>
            <span>Designed to work cleanly on mobile and desktop.</span>
          </div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="section-head">
        <div>
          <h2>Recommended songs</h2>
          <p data-status>Choose a mood to discover songs</p>
        </div>
      </div>

      <div class="song-grid" data-results></div>
      <div class="empty-state" data-empty>
        Select a mood to load songs that match your energy.
      </div>

      <div class="player-card">
        <div class="player-top">
          <div class="player-meta">
            <span class="song-badge">Now playing</span>
            <h3 data-player-title>Pick a mood to begin</h3>
            <span data-player-artist>Your recommendations will appear here</span>
          </div>
          <div class="player-art">♫</div>
        </div>

        <div class="player-controls">
          <div class="nav-links">
            <button class="button-secondary" type="button" data-action="previous">Previous</button>
            <button class="button-secondary" type="button" data-action="play">Play</button>
            <button class="button-secondary" type="button" data-action="pause">Pause</button>
            <button class="button-secondary" type="button" data-action="next">Next</button>
          </div>
        </div>

        <audio id="music-player" controls preload="none"></audio>
      </div>
    </section>
  </div>

  <script src="/assets/js/app.js"></script>
</body>
</html>
