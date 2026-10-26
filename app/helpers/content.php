<?php

declare(strict_types=1);

function make_slug(string $text): string
{
    $slug = strtolower(trim($text));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
    return trim($slug, '-') ?: 'post';
}

function unique_slug(PDO $pdo, string $base, ?int $excludeId = null): string
{
    $slug = make_slug($base);
    $candidate = $slug;
    $i = 2;
    while (true) {
        $sql = 'SELECT COUNT(*) AS c FROM blog_posts WHERE slug = ?' . ($excludeId ? ' AND id != ?' : '');
        $stmt = $pdo->prepare($sql);
        $params = [$candidate];
        if ($excludeId) {
            $params[] = $excludeId;
        }
        $stmt->execute($params);
        if ((int) $stmt->fetch()['c'] === 0) {
            return $candidate;
        }
        $candidate = $slug . '-' . $i;
        $i++;
    }
}

/**
 * A small, deliberately limited markup language for post bodies so admins
 * get real formatting (headings, paragraphs, bullet lists, bold text)
 * without a JS rich-text-editor dependency — and without ever allowing
 * raw HTML from the dashboard to land unescaped on the public site.
 *
 * Supported per line/block:
 *   ## Heading            -> <h2>
 *   - bullet               -> <ul><li>
 *   **bold**                -> <strong>
 *   blank-line separated    -> <p>
 */
function render_post_body(string $raw): string
{
    $raw = str_replace("\r\n", "\n", $raw);
    $blocks = preg_split('/\n\s*\n/', trim($raw)) ?: [];
    $html = '';

    foreach ($blocks as $block) {
        $lines = array_values(array_filter(explode("\n", trim($block)), fn ($l) => trim($l) !== ''));
        if ($lines === []) {
            continue;
        }

        if (str_starts_with(trim($lines[0]), '## ')) {
            $html .= '<h2>' . render_inline(substr(trim($lines[0]), 3)) . '</h2>';
            continue;
        }

        $isList = true;
        foreach ($lines as $line) {
            if (!str_starts_with(trim($line), '- ')) {
                $isList = false;
                break;
            }
        }

        if ($isList) {
            $html .= '<ul>';
            foreach ($lines as $line) {
                $html .= '<li>' . render_inline(substr(trim($line), 2)) . '</li>';
            }
            $html .= '</ul>';
            continue;
        }

        $html .= '<p>' . implode('<br>', array_map('render_inline', $lines)) . '</p>';
    }

    return $html;
}

function render_inline(string $text): string
{
    $escaped = e($text);
    return (string) preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $escaped);
}

/** Plain-text excerpt used for cards/meta descriptions when the admin didn't write a custom one. */
function make_excerpt(string $raw, int $length = 180): string
{
    $text = preg_replace('/\s+/', ' ', strip_tags(str_replace(['#', '-', '*'], '', $raw))) ?? '';
    $text = trim($text);
    $len = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
    if ($len <= $length) {
        return $text;
    }
    $cut = function_exists('mb_substr') ? mb_substr($text, 0, $length) : substr($text, 0, $length);
    return rtrim($cut) . '…';
}
