<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';

auth_boot_session();
require_admin();

$pdo = db_or_null();
$counts = ['posts' => 0, 'messages' => 0, 'unread' => 0];
$recentMessages = [];
$dbError = false;

if ($pdo !== null) {
    try {
        db_ensure_schema($pdo);
        $counts['posts'] = (int) $pdo->query('SELECT COUNT(*) c FROM blog_posts')->fetch()['c'];
        $counts['messages'] = (int) $pdo->query('SELECT COUNT(*) c FROM contact_messages')->fetch()['c'];
        $counts['unread'] = (int) $pdo->query('SELECT COUNT(*) c FROM contact_messages WHERE is_read = 0')->fetch()['c'];
        $recentMessages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
    } catch (Throwable $e) {
        $dbError = true;
    }
} else {
    $dbError = true;
}

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';
?>
<div class="admin-page-head">
  <div>
    <h1>Welcome back<?= isset($_SESSION['admin_email']) ? ', ' . e(explode('@', $_SESSION['admin_email'])[0]) : '' ?></h1>
    <p>Here's what's happening on your site.</p>
  </div>
</div>

<?php if ($dbError): ?>
  <div class="admin-alert admin-alert--error">The database isn't reachable right now, so counts below may be empty. See <strong>SETUP.md</strong> to connect it.</div>
<?php endif; ?>

<div class="admin-grid admin-grid--stats">
  <div class="admin-card admin-stat"><strong><?= e((string) $counts['posts']) ?></strong><span>Blog posts</span></div>
  <div class="admin-card admin-stat"><strong><?= e((string) $counts['messages']) ?></strong><span>Contact messages</span></div>
  <div class="admin-card admin-stat"><strong><?= e((string) $counts['unread']) ?></strong><span>Unread messages</span></div>
</div>

<div class="admin-card" style="margin-top:20px">
  <div class="admin-list-actions">
    <h2 style="margin:0;font-size:17px">Recent messages</h2>
    <a class="admin-btn admin-btn--sm" href="messages.php">View all</a>
  </div>
  <?php if ($recentMessages === []): ?>
    <div class="admin-empty">No messages yet.</div>
  <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>From</th><th>Service</th><th>Received</th></tr></thead>
      <tbody>
        <?php foreach ($recentMessages as $m): ?>
        <tr>
          <td><strong><?= e($m['name']) ?></strong><br><span style="color:var(--admin-muted)"><?= e($m['email']) ?></span></td>
          <td><?= e($m['service']) ?></td>
          <td><?= e(date('M j, Y', strtotime((string) $m['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="admin-row" style="margin-top:20px">
  <div class="admin-card">
    <h2 style="margin:0 0 8px;font-size:17px">Manage blog</h2>
    <p style="color:var(--admin-muted);margin:0 0 16px;font-size:14px">Add, edit, or remove articles shown on your site's Insights section and blog pages.</p>
    <a class="admin-btn admin-btn--primary" href="blog.php">Open blog posts</a>
  </div>
  <div class="admin-card">
    <h2 style="margin:0 0 8px;font-size:17px">Manage videos</h2>
    <p style="color:var(--admin-muted);margin:0 0 16px;font-size:14px">Swap the showreel, portfolio clips, and testimonial videos.</p>
    <a class="admin-btn admin-btn--primary" href="videos.php">Open videos</a>
  </div>
</div>
<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
