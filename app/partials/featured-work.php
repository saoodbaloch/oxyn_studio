<?php
/**
 * OXYN Featured Work
 *
 * Drop videos into:
 * public/assets/video/featured/short/
 * public/assets/video/featured/long/
 *
 * No PHP/JS edits are required when adding/replacing videos.
 */

$publicRoot = dirname(__DIR__, 2) . '/public';

$scanVideos = static function (string $relativeDir) use ($publicRoot): array {
    $absoluteDir = $publicRoot . '/' . trim($relativeDir, '/');

    if (!is_dir($absoluteDir)) {
        return [];
    }

    $files = [];

    foreach (['mp4', 'webm', 'mov'] as $extension) {
        $matches = glob($absoluteDir . '/*.' . $extension);

        if (is_array($matches)) {
            $files = array_merge($files, $matches);
        }
    }

    natsort($files);

    return array_values(array_map(
        static function (string $file) use ($relativeDir): string {
            $assetRelativeDir = preg_replace(
                '#^assets/#',
                '',
                trim($relativeDir, '/')
            );

            return $assetRelativeDir . '/' . basename($file);
        },
        $files
    ));
};

$shortVideos = $scanVideos('assets/video/featured/short');
$longVideos  = $scanVideos('assets/video/featured/long');

$renderSlides = static function (array $videos, string $mode): void {
    if ($videos === []) {
        ?>
        <div class="featured-work__empty">
            Add <?= e($mode) ?> videos to the featured-work video folder.
        </div>
        <?php
        return;
    }

    foreach ($videos as $index => $video) {
        $isActive = $index === 0;

        $extension = strtolower(pathinfo($video, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'webm' => 'video/webm',
            'mov'  => 'video/quicktime',
            default => 'video/mp4',
        };
        ?>
        <article
            class="featured-work__slide<?= $isActive ? ' is-active' : '' ?>"
            data-featured-slide
            data-index="<?= e((string) $index) ?>"
        >
            <div class="featured-work__video-shell">
                <video
                    class="featured-work__video"
                    muted
                    loop
                    playsinline
                    preload="auto"
                    autoplay
                    data-featured-video
                >
                    <source src="<?= e(asset($video)) ?>" type="<?= e($mimeType) ?>">
                </video>

                <button
                    class="featured-work__play"
                    type="button"
                    data-featured-play
                    aria-label="Pause video"
                >
                    <span class="featured-work__play-icon" aria-hidden="true"></span>
                    <span data-featured-play-label>Pause</span>
                </button>
            </div>
        </article>
        <?php
    }
};
?>

<section
    class="featured-work"
    id="works"
    aria-labelledby="featured-work-title"
    data-featured-work
>
    <div class="featured-work__inner">

        <div class="featured-work__badge" id="featured-work-title">
            <span class="featured-work__badge-dot" aria-hidden="true"></span>
            <span>FEATURED WORK</span>
        </div>

        <div
            class="featured-work__mode"
            role="group"
            aria-label="Featured work video type"
        >
            <button
                class="featured-work__mode-label is-active"
                type="button"
                data-featured-mode-button="short"
            >
                Short Form Reels
            </button>

            <button
                class="featured-work__toggle"
                type="button"
                role="switch"
                aria-checked="false"
                aria-label="Switch between short form reels and long form videos"
                data-featured-toggle
            >
                <span class="featured-work__toggle-knob"></span>
            </button>

            <button
                class="featured-work__mode-label"
                type="button"
                data-featured-mode-button="long"
            >
                Long Form Videos
            </button>
        </div>

        <div class="featured-work__stage">

            <div
                class="featured-work__carousel featured-work__carousel--short is-active"
                data-featured-carousel="short"
                data-active-index="0"
            >
                <div class="featured-work__track" data-featured-track>
                    <?php $renderSlides($shortVideos, 'short-form'); ?>
                </div>

                <?php if (count($shortVideos) > 1): ?>
                    <button
                        class="featured-work__arrow featured-work__arrow--prev"
                        type="button"
                        data-featured-prev
                        aria-label="Previous short-form video"
                    >←</button>

                    <button
                        class="featured-work__arrow featured-work__arrow--next"
                        type="button"
                        data-featured-next
                        aria-label="Next short-form video"
                    >→</button>
                <?php endif; ?>
            </div>

            <div
                class="featured-work__carousel featured-work__carousel--long"
                data-featured-carousel="long"
                data-active-index="0"
                hidden
            >
                <div class="featured-work__track" data-featured-track>
                    <?php $renderSlides($longVideos, 'long-form'); ?>
                </div>

                <?php if (count($longVideos) > 1): ?>
                    <button
                        class="featured-work__arrow featured-work__arrow--prev"
                        type="button"
                        data-featured-prev
                        aria-label="Previous long-form video"
                    >←</button>

                    <button
                        class="featured-work__arrow featured-work__arrow--next"
                        type="button"
                        data-featured-next
                        aria-label="Next long-form video"
                    >→</button>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>
