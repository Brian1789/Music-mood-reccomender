<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect_to('/dashboard.php');
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc(app_name()); ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="shell container">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">♪</div>
        <div>
          Music Mood Recommender
          <small>Pick a mood. Get the right song.</small>
        </div>
      </div>
      <nav class="nav-links">
        <a class="nav-link" href="/login.php">Login</a>
        <a class="button" href="/register.php">Create Account</a>
      </nav>
    </header>

    <?php if ($flash): ?>
      <div class="flash <?= esc($flash['type']); ?>"><?= esc($flash['message']); ?></div>
    <?php endif; ?>

    <section class="hero">
      <div class="hero-card">
        <div class="kicker">Dark mode music discovery</div>
        <h1>Turn a mood into a playlist in one tap.</h1>
        <p class="lead">
          This full-stack PHP application lets users register, log in, choose an emotion, and instantly receive matching songs from a MySQL-backed library with a built-in HTML5 player.
        </p>
        <div class="hero-actions">
          <a class="button" href="/register.php">Get Started</a>
          <a class="button-secondary" href="/login.php">I already have an account</a>
        </div>

        <div class="feature-grid">
          <div class="feature-item">
            <strong>User accounts</strong>
            <span class="muted">Secure registration, session login, and password hashing.</span>
          </div>
          <div class="feature-item">
            <strong>Smart recommendations</strong>
            <span class="muted">Mood-based fetching via PHP and AJAX.</span>
          </div>
          <div class="feature-item">
            <strong>Music player</strong>
            <span class="muted">Play, pause, and next controls built into the UI.</span>
          </div>
          <div class="feature-item">
            <strong>Admin tools</strong>
            <span class="muted">Manage tracks from a dedicated admin panel.</span>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="section-head">
          <div>
            <h2>Built for fast discovery</h2>
            <p>Responsive, mobile-friendly, and tuned for a dark music theme.</p>
          </div>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <strong>6 moods</strong>
            <span>Happy, Sad, Energetic, Relaxed, Romantic, and Angry.</span>
          </div>
          <div class="stat-card">
            <strong>MySQL storage</strong>
            <span>Tracks are stored centrally and served dynamically.</span>
          </div>
          <div class="stat-card">
            <strong>Vanilla JavaScript</strong>
            <span>Fetch-based recommendations without frameworks.</span>
          </div>
          <div class="stat-card">
            <strong>Secure auth</strong>
            <span>Passwords are hashed before saving.</span>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>
</html>
