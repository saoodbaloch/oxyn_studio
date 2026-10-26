<?php
declare(strict_types=1);
require __DIR__ . '/../app/helpers/view.php';
require_once __DIR__ . '/../app/helpers/theme.php';
require __DIR__ . '/../app/helpers/db.php';
require __DIR__ . '/../app/helpers/content.php';

$site  = require __DIR__ . '/../app/config/site.php';
$theme = require __DIR__ . '/../app/config/theme.php';
$navBase = 'index.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$post = null;
$related = [];

$pdo = db_or_null();
if ($pdo !== null && $slug !== '') {
    try {
        db_ensure_schema($pdo);
        $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1 LIMIT 1');
        $stmt->execute([$slug]);
        $post = $stmt->fetch() ?: null;

        if ($post) {
            $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE is_published = 1 AND id != ? ORDER BY published_at DESC LIMIT 3');
            $stmt->execute([$post['id']]);
            $related = $stmt->fetchAll();
        }
    } catch (Throwable $e) {
        $post = null;
    }
}

if (!$post) {
    http_response_code(404);
}

$pageTitle = $post ? $post['title'] . ' · ' . $site['name'] : 'Post not found · ' . $site['name'];
?>
<?php require __DIR__ . '/../app/partials/head.php'; ?>
<?php require __DIR__ . '/../app/partials/header.php'; ?>

<main>
<?php if (!$post): ?>
  <section class="blog-post blog-post--missing">
    <div class="blog-post__inner">
      <h1>Post not found</h1>
      <p>This article may have been moved or unpublished.</p>
      <a class="admin-btn" href="blog.php">← Back to all posts</a>
    </div>
  </section>
<?php else: ?>
  <article class="blog-post">
    <div class="blog-post__inner">
      <div class="blog-post__breadcrumb"><span aria-hidden="true">•</span> BLOG / <?= e(strtoupper($post['category'])) ?></div>
      <h1 class="blog-post__title"><?= e($post['title']) ?></h1>

      <?php if ($post['cover_image']): ?>
        <div class="blog-post__cover"><img src="<?= e(asset($post['cover_image'])) ?>" alt="<?= e($post['title']) ?>"></div>
      <?php endif; ?>

      <div class="blog-post__meta">
        <?php if ($post['author_avatar']): ?><img class="blog-post__avatar" src="<?= e(asset($post['author_avatar'])) ?>" alt=""><?php else: ?><span class="blog-post__avatar blog-post__avatar--placeholder"><?= e(mb_substr($post['author_name'], 0, 1)) ?></span><?php endif; ?>
        <span class="blog-post__author"><?= e($post['author_name']) ?></span>
        <span aria-hidden="true">·</span>
        <time datetime="<?= e(date('Y-m-d', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?>"><?= e(date('M j, Y', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?></time>
      </div>

      <div class="blog-post__content"><?= render_post_body($post['content']) ?></div>

      <div class="blog-post__share">
        <span>Liked it? Share it with your people</span>
        <div class="blog-post__share-links">
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode('https://' . ($_SERVER['HTTP_HOST'] ?? '') . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener" aria-label="Share on X">𝕏</a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('https://' . ($_SERVER['HTTP_HOST'] ?? '') . $_SERVER['REQUEST_URI']) ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn">in</a>
        </div>
      </div>
    </div>
  </article>

  <?php if ($related !== []): ?>
  <section class="blog-related">
    <div class="blog-related__inner">
      <div class="blog-related__head">
        <div><div class="insights-section__eyebrow"><span aria-hidden="true">•</span> RECENT ARTICLES</div><h2>You might also like</h2></div>
        <a class="admin-btn" href="blog.php">View all articles</a>
      </div>
      <div class="blog-index__grid">
        <?php foreach ($related as $r): ?>
          <a class="insight-card" href="blog-post.php?slug=<?= e(urlencode($r['slug'])) ?>">
            <div class="insight-card__body">
              <time class="insight-card__date"><?= e(date('M j, Y', strtotime((string) ($r['published_at'] ?: $r['created_at'])))) ?></time>
              <h3 class="insight-card__title"><?= e($r['title']) ?></h3>
              <span class="insight-card__tag"><?= e($r['category']) ?></span>
            </div>
            <div class="insight-card__image-frame"><img class="insight-card__image" src="<?= e(asset($r['cover_image'])) ?>" alt="<?= e($r['title']) ?>" loading="lazy"></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../app/partials/closing-cta.php'; ?>
</main>

<script src="<?= e(asset('js/mobile-v2.js')) ?>?v=5" defer></script>
<?php require __DIR__ . '/../app/partials/footer.php'; ?>
