# Oxyn Studios — Industry-Style PHP Frontend

## The key rule

**Do not hard-code brand colors/fonts inside components.**

The entire project's global design system lives here:

```text
app/config/theme.php
```

Change the website background once:

```php
'page_bg' => '#F7F7F5',
```

Change it to:

```php
'page_bg' => '#111111',
```

and every component using `--color-page` changes automatically.

Likewise, fonts are centralized:

```php
'fonts' => [
    'body'    => '"Poppins", sans-serif',
    'ui'      => '"Poppins", sans-serif',
    'cta'     => '"Inter", sans-serif',
    'display' => '"Fraunces", serif',
],
```

If the entire site must use one font, set all four to the same family.

## Figma values confirmed from the supplied screenshots

- Desktop Website Frame: **1920 px**
- Website frame fill: **#F7F7F5**
- Navigation font: **Poppins Regular**
- Navigation size: **18.11 px**
- Navigation gap: **66.18 px**
- Navigation/text: **#000000**
- Logo: **110 × 41 px**
- CTA: **145 × 46 px**
- CTA radius: **27 px**
- CTA gradient: **#565656 → #C9C9C9**
- CTA text: **Inter Medium, 19 px, #FFFFFF**
- Hero: **Fraunces, 73 px, -5% letter spacing**
- Hero text: **#000000**

## Structure

```text
oxyn-php-professional/
├── app/
│   ├── config/
│   │   ├── site.php          # menus/content config
│   │   └── theme.php         # GLOBAL colors/fonts/layout/tokens
│   ├── helpers/
│   │   ├── view.php
│   │   └── theme.php
│   └── partials/
│       ├── head.php
│       ├── theme-vars.php    # converts PHP theme into CSS variables
│       ├── header.php
│       └── footer.php
│
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       │   ├── base.css
│       │   ├── layout.css
│       │   ├── components/
│       │   │   ├── header.css
│       │   │   ├── buttons.css
│       │   │   └── footer.css
│       │   └── pages/
│       │       └── home.css
│       ├── js/
│       │   └── main.js
│       ├── images/
│       │   └── oxyn-logo.png
│       └── fonts/
└── .gitignore
```

## Why this is professional

- One source of truth for theme values.
- Semantic design tokens instead of random hex values.
- Shared header/footer partials.
- Central navigation config.
- Component-based CSS.
- Page-specific CSS separated from reusable components.
- Responsive mobile navigation.
- Accessibility attributes included.
- Escaped PHP output.
- Reusable spacing scale.
- Central motion tokens.
- No Figma X/Y coordinates copied blindly into CSS.

## Figma -> code checklist for every section

Inspect these before coding:

1. Frame width/height.
2. Parent padding.
3. Auto Layout direction.
4. Gap.
5. Alignment / constraints.
6. Fill/opacity.
7. Stroke.
8. Border radius.
9. Shadow/effects.
10. Font family.
11. Font weight.
12. Font size.
13. Line height.
14. Letter spacing.
15. Asset dimensions.
16. Desktop/tablet/mobile behavior.
17. Prototype hover/click/scroll animation.
18. Animation duration and easing.

## Local run

Quick XAMPP route:

```text
C:\xampp\htdocs\oxyn-php-professional
http://localhost/oxyn-php-professional/public/
```

For production, configure the domain document root to:

```text
oxyn-php-professional/public
```

so `/app` is not directly web-accessible.

## Mobile + SMTP update
A final mobile-only stylesheet is loaded after all existing page CSS: `public/assets/css/pages/mobile-polish.css`. It does not change desktop rules.

The contact form now posts to `public/contact-submit.php` and uses SMTP. Configure these environment variables on hosting:
`SMTP_HOST`, `SMTP_PORT` (usually 587), `SMTP_USERNAME`, `SMTP_PASSWORD`, `SMTP_ENCRYPTION` (`tls` or `ssl`), `SMTP_FROM_EMAIL`, `SMTP_FROM_NAME`, and optional `CONTACT_TO_EMAIL` (defaults to saoodbaloch88@gmail.com).
Do not place the SMTP password in public PHP/CSS/JS files.
