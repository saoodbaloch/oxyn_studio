<?php
/**
 * @var string $pageTitle
 * @var string $activeNav one of: dashboard, blog, videos, testimonials, messages, account
 */
$activeNav = $activeNav ?? '';
$adminEmail = $_SESSION['admin_email'] ?? '';

$navItems = [
    'dashboard'    => ['label' => 'Dashboard',    'href' => 'index.php'],
    'blog'         => ['label' => 'Blog Posts',   'href' => 'blog.php'],
    'videos'       => ['label' => 'Videos',       'href' => 'videos.php'],
    'testimonials' => ['label' => 'Testimonials', 'href' => 'testimonials.php'],
    'messages'     => ['label' => 'Messages',     'href' => 'messages.php'],
    'account'      => ['label' => 'Account',      'href' => 'account.php'],
];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle) ?> · OXYN Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css?v=1">
</head>
<body class="admin">
<div class="admin-shell">
  <div class="admin-overlay" data-admin-overlay></div>
  <aside class="admin-sidebar" data-admin-sidebar>
    <div class="admin-sidebar__brand">
      <img src="../assets/images/oxyn-logo.png" alt="">
      <span>OXYN Admin</span>
    </div>
    <nav class="admin-nav">
      <?php foreach ($navItems as $key => $item): ?>
        <a class="admin-nav__link<?= $activeNav === $key ? ' is-active' : '' ?>" href="<?= e($item['href']) ?>"><?= e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar__footer">
      <?php if ($adminEmail): ?><div class="admin-sidebar__user">Signed in as<br><?= e($adminEmail) ?></div><?php endif; ?>
      <a class="admin-sidebar__logout" href="logout.php">Log out</a>
    </div>
  </aside>

  <div style="flex:1;min-width:0">
    <div class="admin-topbar">
      <button class="admin-topbar__menu" type="button" data-admin-menu-toggle aria-label="Toggle menu">☰</button>
      <div class="admin-topbar__title"><?= e($pageTitle) ?></div>
    </div>
    <main class="admin-main">
