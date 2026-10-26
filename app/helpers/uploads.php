<?php

declare(strict_types=1);

class UploadException extends RuntimeException {}

/**
 * Validates and moves an uploaded image into $targetDir (absolute path),
 * returning the new filename (not full path). Throws UploadException with
 * a human-readable message on any problem.
 */
function upload_image(array $file, string $targetDir, int $maxBytes = 8 * 1024 * 1024): string
{
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
    return upload_file($file, $targetDir, $allowed, $maxBytes);
}

/**
 * Same idea for video files.
 */
function upload_video(array $file, string $targetDir, int $maxBytes = 200 * 1024 * 1024): string
{
    $allowed = ['mp4' => 'video/mp4', 'webm' => 'video/webm', 'mov' => 'video/quicktime'];
    return upload_file($file, $targetDir, $allowed, $maxBytes);
}

function upload_file(array $file, string $targetDir, array $allowedExtensions, int $maxBytes): string
{
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new UploadException('Invalid upload.');
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        throw new UploadException('No file was chosen.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new UploadException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > $maxBytes) {
        throw new UploadException('File is too large (max ' . round($maxBytes / 1024 / 1024) . 'MB).');
    }

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!isset($allowedExtensions[$extension])) {
        throw new UploadException('That file type is not allowed. Allowed: ' . implode(', ', array_keys($allowedExtensions)));
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $expectedMime = $allowedExtensions[$extension];
    $mimeOk = $mime === $expectedMime
        || ($extension === 'jpg' && $mime === 'image/jpeg')
        || ($extension === 'mov' && in_array($mime, ['video/quicktime', 'video/mp4'], true));
    if (!$mimeOk) {
        throw new UploadException('That file does not look like a valid ' . $extension . ' file.');
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $filename = bin2hex(random_bytes(8)) . '-' . time() . '.' . $extension;
    $destination = rtrim($targetDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new UploadException('Could not save the uploaded file.');
    }

    return $filename;
}
