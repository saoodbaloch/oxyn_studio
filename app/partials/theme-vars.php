<?php
/** @var array $theme */
?>
<style id="theme-tokens">
    :root {
        /* COLORS */
        --color-page: <?= e(theme_var($theme, 'colors', 'page_bg')) ?>;
        --color-surface: <?= e(theme_var($theme, 'colors', 'surface')) ?>;
        --color-text: <?= e(theme_var($theme, 'colors', 'text_primary')) ?>;
        --color-text-inverse: <?= e(theme_var($theme, 'colors', 'text_inverse')) ?>;
        --color-cta-start: <?= e(theme_var($theme, 'colors', 'cta_start')) ?>;
        --color-cta-end: <?= e(theme_var($theme, 'colors', 'cta_end')) ?>;
        --color-border: <?= e(theme_var($theme, 'colors', 'border')) ?>;
        --color-muted: <?= e(theme_var($theme, 'colors', 'muted')) ?>;
        --color-hero-highlight: <?= e(theme_var($theme, 'colors', 'hero_highlight')) ?>;

        /* TYPOGRAPHY */
        --font-body: <?= e(theme_var($theme, 'fonts', 'body')) ?>;
        --font-ui: <?= e(theme_var($theme, 'fonts', 'ui')) ?>;
        --font-cta: <?= e(theme_var($theme, 'fonts', 'cta')) ?>;
        --font-display: <?= e(theme_var($theme, 'fonts', 'display')) ?>;

        /* NEW - Hero email / mono font */
        --font-mono: <?= e(theme_var($theme, 'fonts', 'mono')) ?>;

        --nav-font-size: <?= e(theme_var($theme, 'type', 'nav_size')) ?>;
        --nav-font-weight: <?= e(theme_var($theme, 'type', 'nav_weight')) ?>;
        --cta-font-size: <?= e(theme_var($theme, 'type', 'cta_size')) ?>;
        --cta-font-weight: <?= e(theme_var($theme, 'type', 'cta_weight')) ?>;
        --hero-font-size: <?= e(theme_var($theme, 'type', 'hero_size')) ?>;
        --hero-font-weight: <?= e(theme_var($theme, 'type', 'hero_weight')) ?>;
        --hero-letter-spacing: <?= e(theme_var($theme, 'type', 'hero_letter_spacing')) ?>;

        /* LAYOUT */
        --design-width: <?= e(theme_var($theme, 'layout', 'design_width')) ?>;
        --content-max: <?= e(theme_var($theme, 'layout', 'content_max')) ?>;
        --page-gutter: <?= e(theme_var($theme, 'layout', 'gutter')) ?>;

        --header-top-offset: <?= e(theme_var($theme, 'layout', 'header_top_offset')) ?>;
        --header-height: <?= e(theme_var($theme, 'layout', 'header_height')) ?>;
        --nav-gap: <?= e(theme_var($theme, 'layout', 'nav_gap')) ?>;

        /* RADII / SIZES */
        --radius-cta: <?= e(theme_var($theme, 'radius', 'cta')) ?>;
        --radius-hero-highlight: <?= e(theme_var($theme, 'radius', 'hero_highlight')) ?>;

        --logo-width: <?= e(theme_var($theme, 'sizes', 'logo_width')) ?>;
        --logo-height: <?= e(theme_var($theme, 'sizes', 'logo_height')) ?>;

        --cta-width: <?= e(theme_var($theme, 'sizes', 'cta_width')) ?>;
        --cta-height: <?= e(theme_var($theme, 'sizes', 'cta_height')) ?>;

        /* HERO CURSOR / LIQUID GLASS EFFECT */
        --hero-hover-blur: <?= e(theme_var($theme, 'motion', 'hero_blur')) ?>;
        --hero-lens-size: <?= e(theme_var($theme, 'motion', 'hero_lens_size')) ?>;
        --hero-lens-scale: <?= e(theme_var($theme, 'motion', 'hero_lens_scale')) ?>;

        /* SYSTEM SPACING SCALE */
        --space-1: 4px;
        --space-2: 8px;
        --space-3: 12px;
        --space-4: 16px;
        --space-5: 24px;
        --space-6: 32px;
        --space-7: 48px;
        --space-8: 64px;
        --space-9: 96px;
        --space-10: 128px;

        /* MOTION */
        --ease-standard: cubic-bezier(.2, .8, .2, 1);
        --duration-fast: 160ms;
        --duration-normal: 260ms;
    }
</style>