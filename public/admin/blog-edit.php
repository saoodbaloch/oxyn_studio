<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/db.php';
require __DIR__ . '/../../app/helpers/auth.php';
require __DIR__ . '/../../app/helpers/content.php';
require __DIR__ . '/../../app/helpers/uploads.php';

auth_boot_session();
require_admin();

$pdo = db_or_null();
if ($pdo === null) {
    header('Location: blog.php');
    exit;
}
try {
    db_ensure_schema($pdo);
} catch (Throwable $e) {
    header('Location: blog.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$post = [
    'id' => null, 'slug' => '', 'title' => '', 'excerpt' => '', 'content' => '',
    'cover_image' => '', 'category' => 'Video Editing', 'author_name' => 'OXYN Studios',
    'author_avatar' => '', 'is_published' => 1, 'published_at' => date('Y-m-d'),
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        header('Location: blog.php');
        exit;
    }
    $post = $found;
    $post['published_at'] = $post['published_at'] ? date('Y-m-d', strtotime((string) $post['published_at'])) : date('Y-m-d');
}

$error = '';
$uploadDir = dirname(__DIR__) . '/assets/uploads/blog';
$avatarDir = dirname(__DIR__) . '/assets/uploads/avatars';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired, please try again.';
    } else {
        $title = trim((string) ($_POST['title'] ?? ''));
        $slugInput = trim((string) ($_POST['slug'] ?? ''));
        $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
        $content = (string) ($_POST['content'] ?? '');
        $category = trim((string) ($_POST['category'] ?? '')) ?: 'Video Editing';
        $authorName = trim((string) ($_POST['author_name'] ?? '')) ?: 'OXYN Studios';
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $publishedAt = trim((string) ($_POST['published_at'] ?? '')) ?: date('Y-m-d');

        // Re-populate the form with what was typed in case of a validation error.
        $post = array_merge($post, compact('title', 'excerpt', 'content', 'category', 'authorName', 'isPublished', 'publishedAt'));
        $post['author_name'] = $authorName;
        $post['is_published'] = $isPublished;
        $post['published_at'] = $publishedAt;

        if ($title === '') {
            $error = 'Please enter a title.';
        } elseif (trim($content) === '') {
            $error = 'Please write the post content.';
        } else {
            $coverImage = $post['cover_image'];
            $authorAvatar = $post['author_avatar'];

            try {
                if (!empty($_FILES['cover_image']['name'])) {
                    $filename = upload_image($_FILES['cover_image'], $uploadDir);
                    $coverImage = 'uploads/blog/' . $filename;
                }
                if (!empty($_FILES['author_avatar']['name'])) {
                    $filename = upload_image($_FILES['author_avatar'], $avatarDir);
                    $authorAvatar = 'uploads/avatars/' . $filename;
                }
            } catch (UploadException $e) {
                $error = $e->getMessage();
            }

            if ($error === '' && $coverImage === '') {
                $error = 'Please upload a cover image.';
            }

            if ($error === '') {
                $slugBase = $slugInput !== '' ? $slugInput : $title;
                $slug = unique_slug($pdo, $slugBase, $id);
                $excerptFinal = $excerpt !== '' ? $excerpt : make_excerpt($content);

                if ($id) {
                    $stmt = $pdo->prepare('UPDATE blog_posts SET slug=?, title=?, excerpt=?, content=?, cover_image=?, category=?, author_name=?, author_avatar=?, is_published=?, published_at=? WHERE id=?');
                    $stmt->execute([$slug, $title, $excerptFinal, $content, $coverImage, $category, $authorName, $authorAvatar, $isPublished, $publishedAt, $id]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO blog_posts (slug, title, excerpt, content, cover_image, category, author_name, author_avatar, is_published, published_at) VALUES (?,?,?,?,?,?,?,?,?,?)');
                    $stmt->execute([$slug, $title, $excerptFinal, $content, $coverImage, $category, $authorName, $authorAvatar, $isPublished, $publishedAt]);
                }
                header('Location: blog.php');
                exit;
            }

            $post['cover_image'] = $coverImage;
            $post['author_avatar'] = $authorAvatar;
        }
    }
}

$pageTitle = $id ? 'Edit Post' : 'New Post';
$activeNav = 'blog';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';
?>
<div class="admin-page-head">
  <div><h1><?= $id ? 'Edit post' : 'New post' ?></h1><p>Formatting cheatsheet: start a line with <code>## </code> for a heading, <code>- </code> for a bullet, and wrap text in <code>**bold**</code> for bold. Leave a blank line between paragraphs.</p></div>
</div>

<?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="admin-card">
    <div class="admin-row">
      <div class="admin-field" style="margin-bottom:0">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required value="<?= e($post['title']) ?>" data-slug-source>
      </div>
      <div class="admin-field" style="margin-bottom:0">
        <label for="category">Category badge</label>
        <input type="text" id="category" name="category" value="<?= e($post['category']) ?>" placeholder="e.g. Development, Design">
      </div>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-row">
      <div class="admin-field" style="margin-bottom:0">
        <label for="slug">URL slug</label>
        <input type="text" id="slug" name="slug" value="<?= e($post['slug']) ?>" placeholder="auto-generated from title" data-slug-preview<?= $post['slug'] ? ' data-locked="1"' : '' ?>>
        <small>Leave blank to auto-generate from the title.</small>
      </div>
      <div class="admin-field" style="margin-bottom:0">
        <label for="published_at">Published date</label>
        <input type="date" id="published_at" name="published_at" value="<?= e($post['published_at']) ?>">
      </div>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-field" style="margin-bottom:0">
      <label for="excerpt">Excerpt (optional)</label>
      <textarea id="excerpt" name="excerpt" style="min-height:70px" placeholder="Shown on the blog cards. Leave blank to auto-generate from the content."><?= e($post['excerpt']) ?></textarea>
    </div>
  </div>

  <div class="admin-card">
    <div class="admin-field" style="margin-bottom:0">
      <label for="content">Content</label>
      <textarea id="content" name="content" style="min-height:320px;font-family:ui-monospace,Menlo,monospace;font-size:13.5px" required><?= e($post['content']) ?></textarea>
    </div>
  </div>

  <div class="admin-row">
    <div class="admin-card">
      <label style="font-size:13.5px;font-weight:600;display:block;margin-bottom:10px">Cover image<?= $post['cover_image'] ? '' : ' (required)' ?></label>
      <?php if ($post['cover_image']): ?><img src="../assets/<?= e(ltrim((string) $post['cover_image'], '/')) ?>" style="width:100%;max-width:260px;border-radius:10px;margin-bottom:12px;object-fit:cover;aspect-ratio:16/10"><?php endif; ?>
      <label class="admin-upload-drop" data-upload-drop>
        <input type="file" name="cover_image" accept=".jpg,.jpeg,.png,.gif,.webp">
        <span>📷 Click to <?= $post['cover_image'] ? 'replace' : 'upload' ?></span>
        <span data-upload-filename style="color:var(--admin-muted);font-size:12.5px">JPG, PNG, GIF or WEBP — up to 8MB</span>
      </label>
    </div>
    <div class="admin-card">
      <label style="font-size:13.5px;font-weight:600;display:block;margin-bottom:10px">Author</label>
      <div class="admin-field"><input type="text" name="author_name" value="<?= e($post['author_name']) ?>" placeholder="Author name"></div>
      <?php if ($post['author_avatar']): ?><img src="../assets/<?= e(ltrim((string) $post['author_avatar'], '/')) ?>" style="width:48px;height:48px;border-radius:50%;margin-bottom:10px;object-fit:cover"><?php endif; ?>
      <label class="admin-upload-drop" data-upload-drop>
        <input type="file" name="author_avatar" accept=".jpg,.jpeg,.png,.gif,.webp">
        <span>🙂 <?= $post['author_avatar'] ? 'Replace avatar' : 'Upload avatar (optional)' ?></span>
        <span data-upload-filename style="color:var(--admin-muted);font-size:12.5px"></span>
      </label>
    </div>
  </div>

  <div class="admin-card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px">
    <label style="display:flex;align-items:center;gap:10px;font-size:14.5px;font-weight:500">
      <input type="checkbox" name="is_published" value="1" <?= (int) $post['is_published'] ? 'checked' : '' ?> style="width:18px;height:18px">
      Published (visible on the site)
    </label>
    <div style="display:flex;gap:10px">
      <a class="admin-btn" href="blog.php">Cancel</a>
      <button class="admin-btn admin-btn--primary" type="submit"><?= $id ? 'Save changes' : 'Create post' ?></button>
    </div>
  </div>
</form>
<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
