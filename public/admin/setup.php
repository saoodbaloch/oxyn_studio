<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';

auth_boot_session();

$error = '';
$pdo = db_or_null();

if ($pdo === null) {
    $dbNotConfigured = true;
} else {
    $dbNotConfigured = false;
    try {
        db_ensure_schema($pdo);
    } catch (Throwable $e) {
        $dbNotConfigured = true;
    }
}

if (!$dbNotConfigured && admin_count($pdo) > 0) {
    header('Location: login.php');
    exit;
}

if (!$dbNotConfigured && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired, please try again.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $id = admin_create($pdo, $email, $password);
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_email'] = $email;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Set up admin account · OXYN Admin</title>
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
      <p class="lead">Before you can create your admin account, the site needs a database. See <strong>SETUP.md</strong> in the project files for the step-by-step guide (create a MySQL database in your hosting panel, import <code>database/schema.sql</code>, then set the DB_HOST / DB_NAME / DB_USER / DB_PASSWORD environment variables).</p>
      <p class="lead">Once that's done, reload this page.</p>
    <?php else: ?>
      <h1>Create your admin account</h1>
      <p class="lead">This is a one-time setup. Whoever creates this account can log in and manage the site.</p>
      <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="admin-field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email">
        </div>
        <div class="admin-field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
          <small>At least 8 characters.</small>
        </div>
        <div class="admin-field">
          <label for="confirm">Confirm password</label>
          <input type="password" id="confirm" name="confirm" required minlength="8" autocomplete="new-password">
        </div>
        <button class="admin-btn admin-btn--primary admin-btn--block" type="submit">Create account &amp; log in</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
