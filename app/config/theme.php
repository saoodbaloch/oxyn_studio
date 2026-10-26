<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| OXYN DESIGN SYSTEM — SINGLE SOURCE OF TRUTH
|--------------------------------------------------------------------------
| Change global brand colors, fonts, sizing and layout here.
| All pages/components consume these values through CSS variables.
|
| Example:
|   'page_bg' => '#111111'
| changes the website background everywhere that uses --color-page.
|
| If you want ONE font across the whole website, give body/ui/display
| the same family and change the Google Fonts URL in head.php if needed.
|--------------------------------------------------------------------------
*/

return [
    'colors' => [
        // Exact Figma values visible in supplied screenshots
        'page_bg'        => '#F7F7F5',
        'surface'        => '#FFFFFF',
        'text_primary'   => '#000000',
        'text_inverse'   => '#FFFFFF',

        // CTA gradient from Figma
        'cta_start'      => '#565656',
        'cta_end'        => '#C9C9C9',

        // Semantic/support tokens
        'border'         => 'rgba(0, 0, 0, 0.10)',

        // Figma hero lower text color
        'muted'          => '#7E7E7E',

        'hero_highlight' => 'rgba(0, 0, 0, 0.055)',
    ],

    'fonts' => [
        // Figma:
        // nav = Poppins Regular
        // CTA = Inter Medium
        // hero = Fraunces
        // email = Atkinson Hyperlegible Mono

        'body'    => 'Poppins, sans-serif',
        'ui'      => 'Poppins, sans-serif',
        'cta'     => 'Inter, sans-serif',
        'display' => 'Fraunces, serif',

        // NEW — hero email font
        'mono'    => 'Atkinson Hyperlegible Mono, monospace',
    ],

    'layout' => [
        // Figma desktop frame width
        'design_width' => '1920px',

        // Header / nav values extracted from Figma
        'header_top_offset' => '52px',
        'header_height'     => '51px',
        'nav_gap'           => '66.18px',

        // Central responsive gutter used site-wide
        'gutter' => 'clamp(20px, 4vw, 96px)',

        // Figma header/content width
        'content_max' => '1479.76px',
    ],

    'type' => [
        'nav_size'            => '18.11px',
        'nav_weight'          => '400',

        'cta_size'            => '19px',
        'cta_weight'          => '500',

        'hero_size'           => '73px',
        'hero_weight'         => '368',
        'hero_letter_spacing' => '-0.05em',
    ],

    'radius' => [
        'cta'            => '27px',
        'hero_highlight' => '6px',
    ],

    'sizes' => [
        'logo_width'  => '110px',
        'logo_height' => '41px',

        'cta_width'   => '145px',
        'cta_height'  => '46px',
    ],

    /*
    |--------------------------------------------------------------------------
    | HERO INTERACTION
    |--------------------------------------------------------------------------
    | Cursor-following blur / liquid glass effect.
    | Change these values here only.
    |--------------------------------------------------------------------------
    */

    'motion' => [
        'hero_blur'       => '8px',
        'hero_lens_size'  => '220px',
        'hero_lens_scale' => '1.018',
    ],
];