<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';

auth_boot_session();
require_admin();

$pdo = db_or_null();
$messages = [];
$dbError = $pdo === null;

if ($pdo !== null) {
    try {
        db_ensure_schema($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
            $id = (int) ($_POST['id'] ?? 0);
            if (($_POST['do'] ?? '') === 'delete' && $id) {
                $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
            } elseif (($_POST['do'] ?? '') === 'mark_read' && $id) {
                $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([$id]);
            }
            header('Location: messages.php');
            exit;
        }

        $messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    } catch (Throwable $e) {
        $dbError = true;
    }
}

$pageTitle = 'Messages';
$activeNav = 'messages';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';
?>
<div class="admin-page-head">
  <div><h1>Messages</h1><p>Everyone who has submitted your contact form.</p></div>
</div>

<?php if ($dbError): ?>
  <div class="admin-alert admin-alert--error">Could not load messages — the database isn't reachable. See SETUP.md.</div>
<?php elseif ($messages === []): ?>
  <div class="admin-card"><div class="admin-empty"><strong>No messages yet</strong>Submissions from your contact form will show up here.</div></div>
<?php else: ?>
  <?php foreach ($messages as $m): ?>
    <div class="admin-card" style="margin-bottom:14px">
      <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:10px">
        <div>
          <strong><?= e($m['name']) ?></strong>
          <?php if (!(int) $m['is_read']): ?><span class="admin-badge" style="margin-left:8px">New</span><?php endif; ?>
          <div style="color:var(--admin-muted);font-size:13.5px"><?= e($m['email']) ?> · <?= e($m['service']) ?></div>
        </div>
        <div style="color:var(--admin-muted);font-size:13px;white-space:nowrap"><?= e(date('M j, Y g:ia', strtotime((string) $m['created_at']))) ?></div>
      </div>
      <p style="white-space:pre-wrap;margin:0 0 14px"><?= e($m['message']) ?></p>
      <div class="admin-table__actions">
        <a class="admin-btn admin-btn--sm" href="mailto:<?= e($m['email']) ?>">Reply by email</a>
        <?php if (!(int) $m['is_read']): ?>
        <form method="post"><?= csrf_field() ?><input type="hidden" name="do" value="mark_read"><input type="hidden" name="id" value="<?= e((string) $m['id']) ?>"><button class="admin-btn admin-btn--sm" type="submit">Mark read</button></form>
        <?php endif; ?>
        <form method="post" data-confirm="Delete this message?"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= e((string) $m['id']) ?>"><button class="admin-btn admin-btn--sm admin-btn--danger" type="submit">Delete</button></form>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
