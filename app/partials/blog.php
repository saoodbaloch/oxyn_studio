<?php
require_once __DIR__ . '/../helpers/db.php';

$navBase = $navBase ?? '';
$posts = [];

$pdo = db_or_null();
if ($pdo !== null) {
    try {
        db_ensure_schema($pdo);
        $rows = $pdo->query("SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC, created_at DESC LIMIT 3")->fetchAll();
        foreach ($rows as $row) {
            $posts[] = [
                'slug' => $row['slug'],
                'image' => $row['cover_image'],
                'title' => $row['title'],
                'excerpt' => $row['excerpt'],
                'tag' => $row['category'],
                'date' => $row['published_at'] ?: $row['created_at'],
            ];
        }
    } catch (Throwable $e) {
        $posts = [];
    }
}

if ($posts === []) {
    // Database not configured yet, or empty — keep the section looking the
    // same as it always has.
    $fallback = [
        ['images/blog/blog-01.gif', 'Best Way to Reduce Sharpness'],
        ['images/blog/blog-02.gif', 'Best Way to Reduce Sharpness'],
        ['images/blog/blog-03.jpg', 'Best Way to Reduce Sharpness'],
    ];
    foreach ($fallback as $f) {
        $posts[] = [
            'slug' => null,
            'image' => $f[0],
            'title' => $f[1],
            'excerpt' => 'In this video tutorial, learn tips and tricks for reducing aliasing that is common on action camera footage! In this quick-tip video tutorial, we are going to ...',
            'tag' => 'Video Editing',
            'date' => '2026-06-17',
        ];
    }
}
?>
<section class="insights-section" id="insights" aria-labelledby="insights-title"><div class="insights-section__inner"><div class="insights-section__eyebrow"><span aria-hidden="true">•</span> INSIGHTS</div><h2 class="insights-section__title" id="insights-title">Ideas, Insights<br>&amp; Perspectives</h2><div class="insights-grid"><?php foreach ($posts as $post): $href = $post['slug'] ? $navBase . 'blog-post.php?slug=' . urlencode($post['slug']) : ($navBase . 'blog.php'); ?><a class="insight-card" href="<?= e($href) ?>"><div class="insight-card__body"><time class="insight-card__date" datetime="<?= e(date('Y-m-d', strtotime((string) $post['date']))) ?>"><?= e(date('F j Y', strtotime((string) $post['date']))) ?></time><h3 class="insight-card__title"><?= e($post['title']) ?></h3><p class="insight-card__excerpt"><?= e($post['excerpt']) ?></p><span class="insight-card__tag"><?= e($post['tag']) ?></span></div><div class="insight-card__image-frame"><img class="insight-card__image" src="<?= e(asset($post['image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy"></div></a><?php endforeach; ?></div></div></section>
