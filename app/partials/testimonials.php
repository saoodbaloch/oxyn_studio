<?php
/**
 * Testimonials section.
 * Video files can be replaced later without changing markup:
 * public/assets/video/testimonials/testimonial-01.mp4
 * public/assets/video/testimonials/testimonial-02.mp4
 * public/assets/video/testimonials/testimonial-03.mp4
 */
$testimonials = [
    [
        'video' => 'video/testimonials/testimonial-01.mp4',
        'label' => 'Client testimonial one',
    ],
    [
        'video' => 'video/testimonials/testimonial-02.mp4',
        'label' => 'Client testimonial two',
    ],
    [
        'video' => 'video/testimonials/testimonial-03.mp4',
        'label' => 'Client testimonial three',
    ],
];
?>
<section id="testimonials" class="testimonials" aria-labelledby="testimonials-title" data-testimonials>
    <div class="testimonials__inner">
        <div class="testimonials__eyebrow" aria-hidden="true">
            <span class="testimonials__eyebrow-dot">•</span>
            <span>TESTIMONIALS</span>
        </div>

        <h2 class="testimonials__title" id="testimonials-title">
            What our premium clients are<br>
            saying about us
        </h2>

        <div class="testimonials__cards" aria-label="Client testimonial videos">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <article class="testimonial-card testimonial-card--<?= e((string) ($index + 1)) ?>">
                    <div class="testimonial-card__media">
                        <video
                            class="testimonial-card__video"
                            muted
                            loop
                            playsinline
                            autoplay
                            preload="metadata"
                            data-testimonial-video
                            aria-label="<?= e($testimonial['label']) ?>"
                        >
                            <source src="<?= e(asset($testimonial['video'])) ?>" type="video/mp4">
                        </video>

                        <button
                            class="testimonial-card__play"
                            type="button"
                            data-testimonial-toggle
                            aria-label="Pause testimonial video"
                            aria-pressed="false"
                        >
                            <span class="testimonial-card__play-icon" aria-hidden="true"></span>
                            <span data-testimonial-label>Pause</span>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="testimonials__stats" aria-label="Oxyn Studios statistics">
            <div class="testimonials__stat">
                <strong>54+</strong>
                <span>Projects Delivered</span>
            </div>
            <div class="testimonials__stat">
                <strong>96%</strong>
                <span>Industries Impacted</span>
            </div>
            <div class="testimonials__stat">
                <strong>6+</strong>
                <span>Years of Experience</span>
            </div>
        </div>
    </div>
</section>
