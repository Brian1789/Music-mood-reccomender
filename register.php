<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    redirect_to('/dashboard.php');
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $statement = db()->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
        $statement->execute([
            'username' => $username,
            'email' => $email,
        ]);

        if ($statement->fetch()) {
            $errors[] = 'Username or email already exists.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = db()->prepare('INSERT INTO users (username, email, password_hash, is_admin, created_at) VALUES (:username, :email, :password_hash, 0, NOW())');
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
            ]);

            set_flash('success', 'Account created. You can now sign in.');
            redirect_to('/login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | <?= esc(app_name()); ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="auth-layout container">
    <section class="auth-card">
      <div class="brand" style="margin-bottom: 18px;">
        <div class="brand-mark">♪</div>
        <div>
          Create account
          <small>Start building mood-based playlists</small>
        </div>
      </div>

      <p class="muted">Your password is stored securely using hashing.</p>

      <?php if ($errors): ?>
        <div class="flash error"><?= esc(implode(' ', $errors)); ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="field-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= esc($username); ?>" required>
        </div>

        <div class="field-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= esc($email); ?>" required>
        </div>

        <div class="field-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="field-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="form-actions">
          <button class="button" type="submit">Create Account</button>
          <a class="button-secondary" href="/login.php">Back to Login</a>
        </div>
      </form>
    </section>
  </div>
</body>
</html>
