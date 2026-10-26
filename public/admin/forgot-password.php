<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';
require __DIR__ . '/../../app/helpers/mailer.php';

auth_boot_session();

$pdo = db_or_null();
if ($pdo === null) {
    header('Location: login.php');
    exit;
}
try {
    db_ensure_schema($pdo);
} catch (Throwable $e) {
    header('Location: login.php');
    exit;
}

$step = $_SESSION['reset_admin_id'] ?? null ? 'code' : 'email';
$error = '';
$info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    if (isset($_POST['action']) && $_POST['action'] === 'request_code') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $admin = admin_find_by_email($pdo, $email);

        // Always show the same message whether or not the email exists,
        // so this form can't be used to discover the admin's address.
        $info = 'If that email matches our admin account, a 6-digit code has been sent. Check your inbox.';

        if ($admin) {
            $code = admin_issue_reset_code($pdo, (int) $admin['id']);
            $_SESSION['reset_admin_id'] = (int) $admin['id'];
            $step = 'code';
            try {
                mailer_send(
                    $admin['email'],
                    'Your OXYN admin password reset code',
                    "Your password reset code is: {$code}\n\nThis code expires in 15 minutes. If you didn't request this, you can ignore this email.\n"
                );
            } catch (Throwable $e) {
                $info = '';
                $error = 'Could not send the reset email. Make sure SMTP is configured (see SETUP.md), then try again.';
                unset($_SESSION['reset_admin_id']);
                $step = 'email';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset' && !empty($_SESSION['reset_admin_id'])) {
        $step = 'code';
        $code = trim((string) ($_POST['code'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');

        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = ? LIMIT 1');
        $stmt->execute([$_SESSION['reset_admin_id']]);
        $admin = $stmt->fetch();

        if (!$admin || !admin_verify_reset_code($admin, $code)) {
            $error = 'That code is incorrect or has expired.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            admin_set_password($pdo, (int) $admin['id'], $password);
            unset($_SESSION['reset_admin_id']);
            header('Location: login.php?reset=1');
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
<title>Reset password · OXYN Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css?v=1">
</head>
<body class="admin">
<div class="admin-auth">
  <div class="admin-auth__card">
    <div class="admin-auth__logo"><img src="../assets/images/oxyn-logo.png" alt=""> OXYN Admin</div>

    <?php if ($step === 'email'): ?>
      <h1>Forgot your password?</h1>
      <p class="lead">Enter your admin email and we'll send a 6-digit code to reset it.</p>
      <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
      <?php if ($info): ?><div class="admin-alert admin-alert--success"><?= e($info) ?></div><?php endif; ?>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="request_code">
        <div class="admin-field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required autocomplete="email">
        </div>
        <button class="admin-btn admin-btn--primary admin-btn--block" type="submit">Send reset code</button>
      </form>
    <?php else: ?>
      <h1>Enter your code</h1>
      <p class="lead">We emailed a 6-digit code. It expires in 15 minutes.</p>
      <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="reset">
        <div class="admin-field">
          <label for="code">6-digit code</label>
          <input type="text" id="code" name="code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code">
        </div>
        <div class="admin-field">
          <label for="password">New password</label>
          <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
        </div>
        <div class="admin-field">
          <label for="confirm">Confirm new password</label>
          <input type="password" id="confirm" name="confirm" required minlength="8" autocomplete="new-password">
        </div>
        <button class="admin-btn admin-btn--primary admin-btn--block" type="submit">Reset password</button>
      </form>
      <div class="admin-auth__foot"><a href="forgot-password.php">Start over</a></div>
    <?php endif; ?>

    <div class="admin-auth__foot"><a href="login.php">Back to log in</a></div>
  </div>
</div>
</body>
</html>
