<?php
/** @var string $pageTitle */
/** @var array $theme */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Oxyn Studios — creative studio website.">
    <meta name="theme-color" content="#F7F7F5">

    <title><?= e($pageTitle) ?></title>

    <!-- Centralized Figma fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible+Mono:wght@400;700&family=Fraunces:opsz,wght@9..144,100..900&family=Inter:wght@400;500;600&family=Poppins:wght@400;500&display=swap"
        rel="stylesheet"
    >


    <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">

    <?php require __DIR__ . '/theme-vars.php'; ?>

    <!-- Base -->
    <link rel="stylesheet" href="<?= e(asset('css/base.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/layout.css')) ?>">

    <!-- Components -->
    <link rel="stylesheet" href="<?= e(asset('css/components/header.css')) ?>?v=25">
    <link rel="stylesheet" href="<?= e(asset('css/components/buttons.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/components/editor-toolbar.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/components/footer.css')) ?>">

    <!-- Page specific -->
    <link rel="stylesheet" href="<?= e(asset('css/pages/home.css')) ?>?v=5">
    <link rel="stylesheet" href="<?= e(asset('css/pages/showreel.css')) ?>?v=6">
    <link rel="stylesheet" href="<?= e(asset('css/pages/featured-work.css')) ?>?v=17">
    <link rel="stylesheet" href="<?= e(asset('css/pages/services.css')) ?>?v=26">
    <link rel="stylesheet" href="<?= e(asset('css/pages/pricing.css')) ?>?v=1">
    <link rel="stylesheet" href="<?= e(asset('css/pages/testimonials.css')) ?>?v=1">
    <link rel="stylesheet" href="<?= e(asset('css/pages/contact.css')) ?>?v=1">
    <link rel="stylesheet" href="<?= e(asset('css/pages/insights.css')) ?>?v=3">
    <link rel="stylesheet" href="<?= e(asset('css/pages/closing-cta.css')) ?>?v=1">
    <link rel="stylesheet" href="<?= e(asset('css/pages/mobile-polish.css')) ?>?v=1">
    <link rel="stylesheet" href="<?= e(asset('css/pages/mobile-v2.css')) ?>?v=16">
    <link rel="stylesheet" href="<?= e(asset('css/pages/blog-page.css')) ?>?v=1">
</head>

<body>
<a class="skip-link" href="#main-content">Skip to content</a>