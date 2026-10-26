<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/helpers/view.php';
require_once __DIR__ . '/../app/helpers/theme.php';

$site  = require __DIR__ . '/../app/config/site.php';
$theme = require __DIR__ . '/../app/config/theme.php';

$pageTitle = 'Oxyn Studios';

require __DIR__ . '/../app/partials/head.php';
require __DIR__ . '/../app/partials/header.php';
?>

<main id="main-content">
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero__inner">

            <h1 class="hero__title" id="hero-title">
                <span>We make</span>
                <span class="hero__title-highlight">
                    <span class="hero__format-target" data-format-target>Visuals+Pro Edits.</span>
                </span>
            </h1>

            <div class="editor-toolbar" data-editor-toolbar aria-label="Text formatting controls">
                <label class="editor-toolbar__heading-wrap">
                    <span class="sr-only">Heading style</span>
                    <select class="editor-toolbar__heading-select" data-heading-select aria-label="Heading style">
                        <option value="1">Heading 1</option>
                        <option value="2">Heading 2</option>
                        <option value="3">Heading 3</option>
                    </select>
                </label>

                <span class="editor-toolbar__divider" aria-hidden="true"></span>

                <button type="button" class="editor-toolbar__button editor-toolbar__bold" data-format="bold" aria-label="Bold" aria-pressed="false">B</button>
                <button type="button" class="editor-toolbar__button editor-toolbar__italic" data-format="italic" aria-label="Italic" aria-pressed="false">I</button>
                <button type="button" class="editor-toolbar__button editor-toolbar__underline" data-format="underline" aria-label="Underline" aria-pressed="false">U</button>

                <span class="editor-toolbar__divider" aria-hidden="true"></span>

                <div class="editor-toolbar__color">
                    <button type="button" class="editor-toolbar__color-toggle" data-color-toggle aria-label="Text color" aria-haspopup="true" aria-expanded="false">
                        <span class="editor-toolbar__color-letter" data-color-letter>A</span>
                        <span class="editor-toolbar__chevron" aria-hidden="true"></span>
                    </button>

                    <div class="editor-toolbar__palette" data-color-palette role="group" aria-label="Text colors">
                        <button type="button" class="editor-toolbar__swatch is-active" style="--swatch-color:#111111" data-text-color="#111111" aria-label="Black text">A</button>
                        <button type="button" class="editor-toolbar__swatch" style="--swatch-color:#E00000" data-text-color="#E00000" aria-label="Red text">A</button>
                        <button type="button" class="editor-toolbar__swatch" style="--swatch-color:#8B4A45" data-text-color="#8B4A45" aria-label="Brown text">A</button>
                        <button type="button" class="editor-toolbar__swatch" style="--swatch-color:#2447B6" data-text-color="#2447B6" aria-label="Blue text">A</button>
                    </div>
                </div>
            </div>

            <svg class="hero-liquid-defs" width="0" height="0" aria-hidden="true" focusable="false">
                <defs>
                    <filter id="hero-liquid-refraction" x="-18%" y="-28%" width="136%" height="156%" color-interpolation-filters="sRGB">
                        <feTurbulence id="hero-liquid-noise" type="fractalNoise" baseFrequency="0.009 0.021" numOctaves="2" seed="7" result="liquidNoise" />
                        <feGaussianBlur in="liquidNoise" stdDeviation="0.7" result="softNoise" />
                        <feDisplacementMap id="hero-liquid-displacement" in="SourceGraphic" in2="softNoise" scale="22" xChannelSelector="R" yChannelSelector="B" />
                    </filter>
                </defs>
            </svg>

            <div class="hero-art" data-liquid-hover>
                <img class="hero-art__base" src="<?= e(asset('images/hero-art-soft.png')) ?>" alt="" draggable="false">
                <img class="hero-art__lens-image" src="<?= e(asset('images/hero-art-soft.png')) ?>" alt="" aria-hidden="true" draggable="false">
                <img class="hero-art__ripple-image hero-art__ripple-image--2" src="<?= e(asset('images/hero-art-soft.png')) ?>" alt="" aria-hidden="true" draggable="false">
            </div>

            <div class="hero__meta">
                <div class="hero__customers">
                    <div class="hero__avatars" aria-hidden="true">
                        <img src="<?= e(asset('images/customer-1.png')) ?>" alt="">
                        <img src="<?= e(asset('images/customer-2.png')) ?>" alt="">
                        <img src="<?= e(asset('images/customer-3.png')) ?>" alt="">
                    </div>
                    <span>600+ Happy Customers</span>
                </div>

                <p class="hero__description">
                    Effortlessly elevate your visuals with our<br>
                    Pro+ editing template, designed for creators and agencies
                </p>

                <a class="hero__email" href="mailto:Hello@oxynstudio.com">Hello@oxynstudio.com</a>
            </div>
        </div>
    </section>


    <section class="showreel" aria-label="Showreel" data-showreel>
        <div class="showreel__marquee" aria-hidden="true">
            <div class="showreel__marquee-track">
                <div class="showreel__marquee-group">
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                </div>

                <div class="showreel__marquee-group">
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                    <span>Showreel</span>
                    <span class="showreel__dot">•</span>
                </div>
            </div>
        </div>

        <div class="showreel__media">
            <video
                class="showreel__video"
                autoplay
                muted
                loop
                playsinline
                preload="metadata"
                aria-label="Oxyn Studios showreel"
            >
                <source src="<?= e(asset('video/showreel.mp4')) ?>" type="video/mp4">
            </video>
        </div>
    </section>

    <?php require __DIR__ . '/../app/partials/featured-work.php'; ?>
    <?php require __DIR__ . '/../app/partials/services.php'; ?>
    <?php require __DIR__ . '/../app/partials/pricing.php'; ?>
    <?php require __DIR__ . '/../app/partials/testimonials.php'; ?>
    <?php require __DIR__ . '/../app/partials/contact.php'; ?>
    <?php require __DIR__ . '/../app/partials/blog.php'; ?>
    <?php require __DIR__ . '/../app/partials/closing-cta.php'; ?>
</main>

<script src="<?= e(asset('js/hero-liquid-hover.js')) ?>" defer></script>
<script src="<?= e(asset('js/rich-text-editor.js')) ?>" defer></script>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/Flip.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js" defer></script>
<script src="<?= e(asset('js/featured-work.js')) ?>?v=17" defer></script>
<script src="<?= e(asset('js/services-scroll.js')) ?>?v=33" defer></script>
<script src="<?= e(asset('js/pricing.js')) ?>?v=1" defer></script>
<script src="<?= e(asset('js/testimonials.js')) ?>?v=1" defer></script>
<script src="<?= e(asset('js/contact-form.js')) ?>?v=1" defer></script>
<script src="<?= e(asset('js/mobile-v2.js')) ?>?v=5" defer></script>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
