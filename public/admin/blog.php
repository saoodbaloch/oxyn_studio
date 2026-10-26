<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';

auth_boot_session();
require_admin();

$pdo = db_or_null();
$posts = [];
$dbError = $pdo === null;

if ($pdo !== null) {
    try {
        db_ensure_schema($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
            $id = (int) ($_POST['id'] ?? 0);
            if (($_POST['do'] ?? '') === 'delete' && $id) {
                $pdo->prepare('DELETE FROM blog_posts WHERE id = ?')->execute([$id]);
            } elseif (($_POST['do'] ?? '') === 'toggle' && $id) {
                $pdo->prepare('UPDATE blog_posts SET is_published = 1 - is_published WHERE id = ?')->execute([$id]);
            }
            header('Location: blog.php');
            exit;
        }

        $posts = $pdo->query('SELECT * FROM blog_posts ORDER BY published_at DESC, created_at DESC')->fetchAll();
    } catch (Throwable $e) {
        $dbError = true;
    }
}

$pageTitle = 'Blog Posts';
$activeNav = 'blog';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';
?>
<div class="admin-page-head">
  <div><h1>Blog Posts</h1><p>These show on your homepage's Insights section and each get their own page on the site.</p></div>
</div>

<div class="admin-list-actions">
  <span></span>
  <a class="admin-btn admin-btn--primary" href="blog-edit.php">+ New post</a>
</div>

<?php if ($dbError): ?>
  <div class="admin-card"><div class="admin-alert admin-alert--error" style="margin:0">Could not load posts — the database isn't reachable. See SETUP.md.</div></div>
<?php elseif ($posts === []): ?>
  <div class="admin-card"><div class="admin-empty"><strong>No posts yet</strong>Create your first blog post to replace the sample content.</div></div>
<?php else: ?>
  <div class="admin-card" style="padding:8px 8px 0">
    <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th></th><th>Title</th><th>Category</th><th>Status</th><th>Published</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
        <tr>
          <td><img class="admin-table__thumb" src="<?= e('../assets/' . ltrim((string) $p['cover_image'], '/')) ?>" alt=""></td>
          <td><strong><?= e($p['title']) ?></strong><br><span style="color:var(--admin-muted);font-size:12.5px">/<?= e($p['slug']) ?></span></td>
          <td><?= e($p['category']) ?></td>
          <td><?php if ((int) $p['is_published']): ?><span class="admin-badge admin-badge--success">Published</span><?php else: ?><span class="admin-badge admin-badge--muted">Draft</span><?php endif; ?></td>
          <td><?= e($p['published_at'] ? date('M j, Y', strtotime((string) $p['published_at'])) : '—') ?></td>
          <td>
            <div class="admin-table__actions">
              <a class="admin-btn admin-btn--sm" href="../blog-post.php?slug=<?= e($p['slug']) ?>" target="_blank" rel="noopener">View</a>
              <a class="admin-btn admin-btn--sm" href="blog-edit.php?id=<?= e((string) $p['id']) ?>">Edit</a>
              <form method="post"><?= csrf_field() ?><input type="hidden" name="do" value="toggle"><input type="hidden" name="id" value="<?= e((string) $p['id']) ?>"><button class="admin-btn admin-btn--sm" type="submit"><?= (int) $p['is_published'] ? 'Unpublish' : 'Publish' ?></button></form>
              <form method="post" data-confirm="Delete this post? This can't be undone."><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= e((string) $p['id']) ?>"><button class="admin-btn admin-btn--sm admin-btn--danger" type="submit">Delete</button></form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
