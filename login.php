<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect_to('/dashboard.php');
}

$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($identifier === '') {
        $errors[] = 'Enter your username or email.';
    }

    if ($password === '') {
        $errors[] = 'Enter your password.';
    }

    if (!$errors) {
        $statement = db()->prepare('SELECT id, username, email, password_hash, is_admin FROM users WHERE username = :identifier OR email = :identifier LIMIT 1');
        $statement->execute(['identifier' => $identifier]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            login_user($user);

            if (!empty($user['is_admin'])) {
                redirect_to('/admin/admin.php');
            }

            set_flash('success', 'Welcome back, ' . $user['username'] . '!');
            redirect_to('/dashboard.php');
        }

        $errors[] = 'Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | <?= esc(app_name()); ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="auth-layout container">
    <section class="auth-card">
      <div class="brand" style="margin-bottom: 18px;">
        <div class="brand-mark">♪</div>
        <div>
          Login
          <small>Access your playlist dashboard</small>
        </div>
      </div>

      <p class="muted">Use your username or email to sign in.</p>

      <?php if ($errors): ?>
        <div class="flash error">
          <?= esc(implode(' ', $errors)); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="field-group">
          <label for="identifier">Username or Email</label>
          <input type="text" id="identifier" name="identifier" value="<?= esc($identifier); ?>" required>
        </div>

        <div class="field-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="form-actions">
          <button class="button" type="submit">Login</button>
          <a class="button-secondary" href="/register.php">Create Account</a>
        </div>
      </form>
    </section>
  </div>
</body>
</html>
