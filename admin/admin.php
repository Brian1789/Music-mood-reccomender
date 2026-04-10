<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$sessionUser = current_user();

if ($sessionUser && empty($sessionUser['is_admin'])) {
  set_flash('error', 'Admin access required.');
  redirect_to('/dashboard.php');
}

$user = null;
$flash = get_flash();
$songs = [];
$moods = mood_options();
$errors = [];

if ($sessionUser && !empty($sessionUser['is_admin'])) {
  $user = $sessionUser;
  $songs = fetch_songs();
}

if (!$user && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        $errors[] = 'Enter your admin credentials.';
    } else {
        $statement = db()->prepare('SELECT id, username, email, password_hash, is_admin FROM users WHERE (username = :identifier OR email = :identifier) LIMIT 1');
        $statement->execute(['identifier' => $identifier]);
        $adminUser = $statement->fetch();

        if ($adminUser && password_verify($password, $adminUser['password_hash']) && !empty($adminUser['is_admin'])) {
            login_user($adminUser);
            set_flash('success', 'Admin access granted.');
            redirect_to('/admin/admin.php');
        }

        $errors[] = 'Admin access denied.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | <?= esc(app_name()); ?></title>
  <link rel="stylesheet" href="/admin/css/admin.css">
</head>
<body class="admin-page">
  <div class="shell container">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">★</div>
        <div>
          Admin Panel
          <small>Manage the song library</small>
        </div>
      </div>
      <nav class="nav-links">
        <?php if ($user && !empty($user['is_admin'])): ?>
          <a class="nav-link" href="/dashboard.php">User Dashboard</a>
          <a class="nav-link" href="/admin/logout.php">Logout</a>
        <?php else: ?>
          <a class="nav-link" href="/login.php">User Login</a>
        <?php endif; ?>
      </nav>
    </header>

    <?php if ($flash): ?>
      <div class="flash <?= esc($flash['type']); ?>"><?= esc($flash['message']); ?></div>
    <?php endif; ?>

    <?php if (!$user || empty($user['is_admin'])): ?>
      <section class="auth-card">
        <div class="brand" style="margin-bottom: 18px;">
          <div class="brand-mark">★</div>
          <div>
            Admin Login
            <small>Restricted access for the music library team</small>
          </div>
        </div>

        <p class="muted">Use an account with <strong>is_admin = 1</strong>.</p>

        <?php if ($errors): ?>
          <div class="flash error"><?= esc(implode(' ', $errors)); ?></div>
        <?php endif; ?>

        <form method="post" action="">
          <div class="field-group">
            <label for="identifier">Username or Email</label>
            <input type="text" id="identifier" name="identifier" required>
          </div>
          <div class="field-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div class="form-actions">
            <button class="button" type="submit">Sign In</button>
            <a class="button-secondary" href="/index.php">Back to Home</a>
          </div>
        </form>
      </section>
    <?php else: ?>
      <section class="admin-banner">
        <div class="hero-card">
          <div class="kicker">Library management</div>
          <h1>Control the catalog from one place.</h1>
          <p class="lead">Add new tracks, remove old ones, and keep the mood library fresh for users.</p>
          <div class="admin-note">Logged in as <?= esc($user['username']); ?></div>
        </div>
      </section>

      <section class="panel section">
        <div class="section-head">
          <div>
            <h2>Add Song</h2>
            <p>Upload an audio file or paste a streaming URL.</p>
          </div>
        </div>

        <form class="admin-form-grid" method="post" action="/admin/add_song.php" enctype="multipart/form-data">
          <div class="field-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
          </div>
          <div class="field-group">
            <label for="artist">Artist</label>
            <input type="text" id="artist" name="artist" required>
          </div>
          <div class="field-group">
            <label for="mood">Mood</label>
            <select id="mood" name="mood" required>
              <?php foreach ($moods as $mood => $meta): ?>
                <option value="<?= esc($mood); ?>"><?= esc($meta['emoji'] . ' ' . $meta['label']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field-group">
            <label for="audio_url">Audio URL</label>
            <input type="url" id="audio_url" name="audio_url" data-audio-url placeholder="https://example.com/song.mp3">
          </div>
          <div class="field-group">
            <label for="audio_file">Audio File</label>
            <input type="file" id="audio_file" name="audio_file" data-audio-file accept="audio/*">
          </div>
          <div class="field-group">
            <span class="admin-note" data-audio-hint>Provide either an audio URL or upload a file.</span>
          </div>
          <div class="field-group">
            <button class="button" type="submit">Save Song</button>
          </div>
        </form>
      </section>

      <section class="section">
        <div class="admin-library-header">
          <div>
            <h2>All Songs</h2>
            <p class="muted">Delete tracks that should no longer appear in recommendations.</p>
          </div>
          <div class="admin-note"><?= count($songs); ?> tracks in the library</div>
        </div>

        <div class="admin-song-grid">
          <?php foreach ($songs as $song): ?>
            <article class="song-card admin-song-card">
              <div class="admin-song-meta">
                <span class="song-badge"><?= esc($song['mood']); ?></span>
                <h3><?= esc($song['title']); ?></h3>
                <span><?= esc($song['artist']); ?></span>
              </div>
              <div class="admin-song-actions">
                <a class="button-secondary" href="<?= esc(normalize_audio_url($song['audio_url'])); ?>" target="_blank" rel="noopener">Open Audio</a>
                <form method="post" action="/admin/delete_song.php" data-delete-form>
                  <input type="hidden" name="song_id" value="<?= (int) $song['id']; ?>">
                  <button class="button-danger" type="submit">Delete</button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>

  <script src="/admin/js/admin.js"></script>
</body>
</html>
