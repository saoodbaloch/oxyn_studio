<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/auth.php';
require __DIR__ . '/../../app/helpers/uploads.php';
require __DIR__ . '/../../app/helpers/media_folder.php';

auth_boot_session();
require_admin();

$publicRoot = dirname(__DIR__);
$dirs = [
    'short' => $publicRoot . '/assets/video/featured/short',
    'long'  => $publicRoot . '/assets/video/featured/long',
];
$showreelPath = $publicRoot . '/assets/video/showreel.mp4';
$contactVideoPath = $publicRoot . '/assets/videos/contact-video.mp4';

$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired, please try again.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        try {
            if ($action === 'upload_showreel' && !empty($_FILES['file']['name'])) {
                upload_video_to_fixed_path($_FILES['file'], $showreelPath, ['mp4' => 'video/mp4']);
                $notice = 'Showreel updated.';
            } elseif ($action === 'upload_contact' && !empty($_FILES['file']['name'])) {
                upload_video_to_fixed_path($_FILES['file'], $contactVideoPath, ['mp4' => 'video/mp4']);
                $notice = 'Contact section video updated.';
            } elseif ($action === 'add' && isset($dirs[$_POST['gallery'] ?? '']) && !empty($_FILES['file']['name'])) {
                media_add($_FILES['file'], $dirs[$_POST['gallery']]);
                $notice = 'Video added.';
            } elseif ($action === 'delete' && isset($dirs[$_POST['gallery'] ?? ''])) {
                media_delete($dirs[$_POST['gallery']], (string) ($_POST['file'] ?? ''));
                $notice = 'Video removed.';
            } elseif ($action === 'move' && isset($dirs[$_POST['gallery'] ?? ''])) {
                media_move($dirs[$_POST['gallery']], (string) ($_POST['file'] ?? ''), (int) ($_POST['direction'] ?? 0));
                $notice = 'Order updated.';
            }
        } catch (UploadException $e) {
            $error = $e->getMessage();
        }
        if ($error === '') {
            header('Location: videos.php?ok=1');
            exit;
        }
    }
}

function upload_video_to_fixed_path(array $file, string $destination, array $allowed): void
{
    $tmpDir = dirname($destination) . '/.tmp-upload';
    $filename = upload_file($file, $tmpDir, $allowed, 200 * 1024 * 1024);
    if (is_file($destination)) {
        unlink($destination);
    }
    if (!is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0775, true);
    }
    rename($tmpDir . '/' . $filename, $destination);
    @rmdir($tmpDir);
}

$shortList = media_scan($dirs['short']);
$longList = media_scan($dirs['long']);

$pageTitle = 'Videos';
$activeNav = 'videos';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';

$renderGallery = function (string $key, string $label, array $list) use ($dirs) {
    ?>
    <div class="admin-card">
      <div class="admin-list-actions">
        <h2 style="margin:0;font-size:17px"><?= e($label) ?></h2>
      </div>
      <div class="admin-media-grid">
        <?php foreach ($list as $i => $file): ?>
          <div class="admin-media-card">
            <video muted loop playsinline preload="metadata"><source src="../assets/video/featured/<?= e($key) ?>/<?= e($file) ?>"></video>
            <div class="admin-media-card__body">
              <div class="admin-media-card__name">#<?= $i + 1 ?> — <?= e($file) ?></div>
              <div class="admin-media-card__actions">
                <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="gallery" value="<?= e($key) ?>"><input type="hidden" name="file" value="<?= e($file) ?>"><input type="hidden" name="direction" value="-1"><button class="admin-btn admin-btn--sm" type="submit" <?= $i === 0 ? 'disabled' : '' ?>>↑</button></form>
                <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="move"><input type="hidden" name="gallery" value="<?= e($key) ?>"><input type="hidden" name="file" value="<?= e($file) ?>"><input type="hidden" name="direction" value="1"><button class="admin-btn admin-btn--sm" type="submit" <?= $i === count($list) - 1 ? 'disabled' : '' ?>>↓</button></form>
                <form method="post" data-confirm="Remove this video?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="gallery" value="<?= e($key) ?>"><input type="hidden" name="file" value="<?= e($file) ?>"><button class="admin-btn admin-btn--sm admin-btn--danger" type="submit">✕</button></form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <form method="post" enctype="multipart/form-data" style="margin-top:16px">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="gallery" value="<?= e($key) ?>">
        <label class="admin-upload-drop" data-upload-drop>
          <input type="file" name="file" accept=".mp4,.webm,.mov" onchange="this.form.requestSubmit()">
          <span>＋ Add a video to <?= e($label) ?></span>
          <span data-upload-filename style="color:var(--admin-muted);font-size:12.5px">MP4, WEBM or MOV — up to 200MB</span>
        </label>
      </form>
    </div>
    <?php
};
?>
<div class="admin-page-head">
  <div><h1>Videos</h1><p>Swap the showreel, portfolio clips, and contact section video. Changes go live immediately — no code edits needed.</p></div>
</div>

<?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['ok'])): ?><div class="admin-alert admin-alert--success">Saved.</div><?php endif; ?>

<div class="admin-row">
  <div class="admin-card">
    <h2 style="margin:0 0 12px;font-size:17px">Showreel</h2>
    <video src="../assets/video/showreel.mp4" muted loop playsinline preload="metadata" style="width:100%;border-radius:10px;margin-bottom:14px;aspect-ratio:16/9;object-fit:cover;background:#000"></video>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="upload_showreel">
      <label class="admin-upload-drop" data-upload-drop>
        <input type="file" name="file" accept=".mp4">
        <span>🎬 Replace showreel</span>
        <span data-upload-filename style="color:var(--admin-muted);font-size:12.5px">MP4 only</span>
      </label>
      <button class="admin-btn admin-btn--primary admin-btn--block" style="margin-top:10px" type="submit">Upload</button>
    </form>
  </div>

  <div class="admin-card">
    <h2 style="margin:0 0 12px;font-size:17px">Contact section video</h2>
    <video src="../assets/videos/contact-video.mp4" muted loop playsinline preload="metadata" style="width:100%;border-radius:10px;margin-bottom:14px;aspect-ratio:9/14;object-fit:cover;background:#000"></video>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="upload_contact">
      <label class="admin-upload-drop" data-upload-drop>
        <input type="file" name="file" accept=".mp4">
        <span>🎬 Replace contact video</span>
        <span data-upload-filename style="color:var(--admin-muted);font-size:12.5px">MP4 only. Hidden on mobile by design.</span>
      </label>
      <button class="admin-btn admin-btn--primary admin-btn--block" style="margin-top:10px" type="submit">Upload</button>
    </form>
  </div>
</div>

<div style="margin-top:20px"><?php $renderGallery('short', 'Featured Work — Short Form Reels', $shortList); ?></div>
<div style="margin-top:20px"><?php $renderGallery('long', 'Featured Work — Long Form Videos', $longList); ?></div>

<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
