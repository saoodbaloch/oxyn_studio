<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';

auth_boot_session();
require_admin();

$pdo = db_or_null();
$error = '';
$success = '';

if ($pdo !== null && $_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    try {
        db_ensure_schema($pdo);
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = ?');
        $stmt->execute([current_admin_id()]);
        $admin = $stmt->fetch();

        $currentPassword = (string) ($_POST['current_password'] ?? '');
        if (!$admin || !password_verify($currentPassword, $admin['password_hash'])) {
            $error = 'Current password is incorrect.';
        } else {
            $newEmail = trim((string) ($_POST['email'] ?? $admin['email']));
            $newPassword = (string) ($_POST['new_password'] ?? '');

            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } elseif ($newPassword !== '' && strlen($newPassword) < 8) {
                $error = 'New password must be at least 8 characters.';
            } else {
                $upd = $pdo->prepare('UPDATE admin_users SET email = ? WHERE id = ?');
                $upd->execute([$newEmail, $admin['id']]);
                if ($newPassword !== '') {
                    admin_set_password($pdo, (int) $admin['id'], $newPassword);
                }
                $_SESSION['admin_email'] = $newEmail;
                $success = 'Account updated.';
            }
        }
    } catch (Throwable $e) {
        $error = 'Something went wrong. Please try again.';
    }
}

$currentEmail = $_SESSION['admin_email'] ?? '';
$pageTitle = 'Account';
$activeNav = 'account';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';
?>
<div class="admin-page-head">
  <div><h1>Account</h1><p>Update your login email or password.</p></div>
</div>

<div class="admin-card" style="max-width:480px">
  <?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="admin-alert admin-alert--success"><?= e($success) ?></div><?php endif; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="admin-field">
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" required value="<?= e($currentEmail) ?>">
    </div>
    <div class="admin-field">
      <label for="new_password">New password</label>
      <input type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8">
      <small>Leave blank to keep your current password.</small>
    </div>
    <div class="admin-field">
      <label for="current_password">Current password</label>
      <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
      <small>Required to confirm any change.</small>
    </div>
    <button class="admin-btn admin-btn--primary" type="submit">Save changes</button>
  </form>
</div>
<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
