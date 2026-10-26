<?php

declare(strict_types=1);

/**
 * Manages a folder of video files that the front-end already displays by
 * scanning the directory (see app/partials/featured-work.php) — natural
 * sort order. These helpers let the admin add, remove, and reorder files
 * without ever touching PHP/JS, exactly as the folder's own doc comment
 * promises.
 */

const MEDIA_VIDEO_EXTENSIONS = ['mp4', 'webm', 'mov'];

function media_scan(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    foreach (MEDIA_VIDEO_EXTENSIONS as $ext) {
        $matches = glob($dir . '/*.' . $ext);
        if (is_array($matches)) {
            foreach ($matches as $m) {
                $files[] = basename($m);
            }
        }
    }
    natsort($files);
    return array_values($files);
}

/** Renames every file in $orderedBasenames to a clean, zero-padded numeric prefix reflecting the given order. Returns the new basenames in the same order. */
function media_renumber(string $dir, array $orderedBasenames): array
{
    $result = [];
    $tempMap = [];

    // First pass: move everything to a temp name so we never collide when
    // swapping two files that would otherwise want each other's name.
    foreach ($orderedBasenames as $name) {
        $temp = '__tmp_' . bin2hex(random_bytes(4)) . '_' . $name;
        rename($dir . '/' . $name, $dir . '/' . $temp);
        $tempMap[] = $temp;
    }

    foreach ($tempMap as $i => $temp) {
        $ext = strtolower(pathinfo($temp, PATHINFO_EXTENSION));
        $core = preg_replace('/^__tmp_[0-9a-f]+_/', '', $temp);
        $core = preg_replace('/^\d+--/', '', (string) $core);
        $final = sprintf('%02d--%s', $i + 1, $core);
        rename($dir . '/' . $temp, $dir . '/' . $final);
        $result[] = $final;
    }

    return $result;
}

function media_move(string $dir, string $filename, int $direction): void
{
    $list = media_scan($dir);
    $index = array_search($filename, $list, true);
    if ($index === false) {
        return;
    }
    $swapWith = $index + $direction;
    if ($swapWith < 0 || $swapWith >= count($list)) {
        return;
    }
    [$list[$index], $list[$swapWith]] = [$list[$swapWith], $list[$index]];
    media_renumber($dir, $list);
}

function media_delete(string $dir, string $filename): void
{
    $safe = basename($filename);
    $path = $dir . '/' . $safe;
    if (is_file($path)) {
        unlink($path);
    }
}

/** Adds an uploaded video to the end of the folder's order. Returns the stored filename. */
function media_add(array $file, string $dir): string
{
    $existing = media_scan($dir);
    $nextPosition = count($existing) + 1;
    $filename = upload_video($file, $dir);

    // upload_video() already wrote a randomized, safe filename — prefix it
    // so it naturally sorts after everything else.
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $newName = sprintf('%02d--%s.%s', $nextPosition, pathinfo($filename, PATHINFO_FILENAME), $ext);
    rename($dir . '/' . $filename, $dir . '/' . $newName);

    return $newName;
}
