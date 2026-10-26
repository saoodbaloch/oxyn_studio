<?php
declare(strict_types=1);
require __DIR__ . '/../../app/helpers/view.php';
require __DIR__ . '/../../app/helpers/auth.php';
require __DIR__ . '/../../app/helpers/uploads.php';

auth_boot_session();
require_admin();

$publicRoot = dirname(__DIR__);
$dir = $publicRoot . '/assets/video/testimonials';
$slots = [1 => 'testimonial-01.mp4', 2 => 'testimonial-02.mp4', 3 => 'testimonial-03.mp4'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Your session expired, please try again.';
    } else {
        $slot = (int) ($_POST['slot'] ?? 0);
        if (isset($slots[$slot]) && !empty($_FILES['file']['name'])) {
            try {
                $tmpDir = $dir . '/.tmp-upload';
                $filename = upload_file($_FILES['file'], $tmpDir, ['mp4' => 'video/mp4'], 200 * 1024 * 1024);
                $destination = $dir . '/' . $slots[$slot];
                if (is_file($destination)) {
                    unlink($destination);
                }
                rename($tmpDir . '/' . $filename, $destination);
                @rmdir($tmpDir);
                header('Location: testimonials.php?ok=1');
                exit;
            } catch (UploadException $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$pageTitle = 'Testimonials';
$activeNav = 'testimonials';
require __DIR__ . '/../../app/partials/admin/chrome-start.php';
?>
<div class="admin-page-head">
  <div><h1>Testimonials</h1><p>Three client video slots shown in a fanned layout on the homepage. Upload an MP4 to replace any slot.</p></div>
</div>

<?php if ($error): ?><div class="admin-alert admin-alert--error"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['ok'])): ?><div class="admin-alert admin-alert--success">Video updated.</div><?php endif; ?>

<div class="admin-media-grid">
  <?php foreach ($slots as $num => $filename): $path = $dir . '/' . $filename; ?>
    <div class="admin-media-slot">
      <h3>Slot <?= $num ?></h3>
      <?php if (is_file($path)): ?>
        <video src="../assets/video/testimonials/<?= e($filename) ?>?v=<?= filemtime($path) ?>" muted loop playsinline preload="metadata" style="width:100%;aspect-ratio:9/16;object-fit:cover;border-radius:10px;background:#000;margin-bottom:12px"></video>
      <?php else: ?>
        <div class="admin-empty" style="padding:24px 10px">No video yet</div>
      <?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="slot" value="<?= $num ?>">
        <label class="admin-upload-drop" data-upload-drop>
          <input type="file" name="file" accept=".mp4" onchange="this.form.requestSubmit()">
          <span>🎬 Replace</span>
          <span data-upload-filename style="color:var(--admin-muted);font-size:12.5px">MP4 only</span>
        </label>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../../app/partials/admin/chrome-end.php'; ?>
