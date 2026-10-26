<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';

auth_boot_session();

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
$pdo = db_or_null();
$dbNotConfigured = $pdo === null;

if (!$dbNotConfigured) {
    try {
        db_ensure_schema($pdo);
        if (admin_count($pdo) === 0) {
            header('Location: setup.php');
            exit;
        }
    } catch (Throwable $e) {
        $dbNotConfigured = true;
    }
}

if (!$dbNotConfigured && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired, please try again.';
    } elseif (admin_login_is_locked()) {
        $error = 'Too many attempts. Please wait a minute and try again.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $admin = admin_find_by_email($pdo, $email);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            admin_login_clear_failures();
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: index.php');
            exit;
        }

        admin_login_register_failure();
        $error = 'Incorrect email or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Log in · OXYN Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css?v=1">
</head>
<body class="admin">
<div class="admin-auth">
  <div class="admin-auth__card">
    <div class="admin-auth__logo"><img src="../assets/images/oxyn-logo.png" alt=""> OXYN Admin</div>

    <?php if ($dbNotConfigured): ?>
      <h1>Database not connected yet</h1>
      <p class="lead">See <strong>SETUP.md</strong> for how to create the database and connect it — the dashboard needs that first.</p>
    <?php else: ?>
      <h1>Welcome back</h1>
      <p class="lead">Log in to manage your site.</p>
      <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
      <?php if (isset($_GET['reset']) && $_GET['reset'] === '1'): ?><div class="admin-alert admin-alert--success">Password updated — log in with your new password.</div><?php endif; ?>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="admin-field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>">
        </div>
        <div class="admin-field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button class="admin-btn admin-btn--primary admin-btn--block" type="submit">Log in</button>
      </form>
      <div class="admin-auth__foot"><a href="forgot-password.php">Forgot your password?</a></div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
