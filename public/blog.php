<?php
declare(strict_types=1);
require __DIR__ . '/../app/helpers/view.php';
require_once __DIR__ . '/../app/helpers/theme.php';
require __DIR__ . '/../app/helpers/db.php';

$site  = require __DIR__ . '/../app/config/site.php';
$theme = require __DIR__ . '/../app/config/theme.php';
$pageTitle = 'Blog · ' . $site['name'];
$navBase = 'index.php';

$posts = [];
$pdo = db_or_null();
if ($pdo !== null) {
    try {
        db_ensure_schema($pdo);
        $posts = $pdo->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC, created_at DESC")->fetchAll();
    } catch (Throwable $e) {
        $posts = [];
    }
}
?>
<?php require __DIR__ . '/../app/partials/head.php'; ?>
<?php require __DIR__ . '/../app/partials/header.php'; ?>

<main>
<section class="blog-index">
  <div class="blog-index__inner">
    <div class="blog-index__eyebrow"><span aria-hidden="true">•</span> BLOG</div>
    <h1 class="blog-index__title">Ideas, Insights &amp; Perspectives</h1>
    <p class="blog-index__lead">Practical notes on video editing, motion design, and building content that performs.</p>

    <?php if ($posts === []): ?>
      <div class="blog-index__empty">No posts published yet — check back soon.</div>
    <?php else: ?>
      <div class="blog-index__grid">
        <?php foreach ($posts as $post): ?>
          <a class="insight-card" href="blog-post.php?slug=<?= e(urlencode($post['slug'])) ?>">
            <div class="insight-card__body">
              <time class="insight-card__date" datetime="<?= e(date('Y-m-d', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?>"><?= e(date('F j Y', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?></time>
              <h3 class="insight-card__title"><?= e($post['title']) ?></h3>
              <p class="insight-card__excerpt"><?= e($post['excerpt']) ?></p>
              <span class="insight-card__tag"><?= e($post['category']) ?></span>
            </div>
            <div class="insight-card__image-frame"><img class="insight-card__image" src="<?= e(asset($post['cover_image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy"></div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/../app/partials/closing-cta.php'; ?>
</main>

<script src="<?= e(asset('js/mobile-v2.js')) ?>?v=5" defer></script>
<?php require __DIR__ . '/../app/partials/footer.php'; ?>
