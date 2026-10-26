<?php
/** @var array $site */
/** @var string $navBase set to 'index.php' on non-homepage pages so #anchor links point back home */
$navBase = $navBase ?? '';
$navHref = static fn (string $href): string => str_starts_with($href, '#') ? $navBase . $href : $href;
?>
<header class="site-header" data-site-header>
    <div class="site-header__inner">
        <a class="site-logo" href="./" aria-label="<?= e($site['name']) ?> home">
            <img
                src="<?= e(asset('images/oxyn-logo.png')) ?>"
                width="110"
                height="41"
                alt="Oxyn Studios"
            >
        </a>

        <button
            class="nav-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="primary-navigation"
            data-nav-toggle
        >
            <span class="nav-toggle__line"></span>
            <span class="nav-toggle__line"></span>
            <span class="sr-only">Toggle navigation</span>
        </button>

        <nav class="primary-nav" id="primary-navigation" aria-label="Primary navigation" data-primary-nav>
            <ul class="primary-nav__list">
                <?php foreach ($site['navigation'] as $item): ?>
                    <li>
                        <a class="primary-nav__link" href="<?= e($navHref($item['href'])) ?>">
                            <?= e($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <a class="header-cta button button--header" href="<?= e($navHref($site['cta']['href'])) ?>">
            <?= e($site['cta']['label']) ?>
        </a>
    </div>
</header>
