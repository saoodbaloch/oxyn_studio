<?php
/** @var array $theme */
$services = require dirname(__DIR__) . '/config/services.php';
?>
<section
    class="services-story"
    id="services"
    aria-label="Video editing services"
    data-services-story
>
    <div class="services-story__viewport">
<div class="services-wheel" aria-hidden="true" data-services-wheel>
            <div class="services-wheel__ring"></div>
            <span class="services-wheel__active-dot"></span>

            <div class="services-wheel__numbers" data-services-wheel-numbers>
                <?php foreach ($services as $index => $service): ?>
                    <span
                        class="services-wheel__number"
                        data-service-wheel-number
                        data-service-index="<?= e((string) $index) ?>"
                    >
                        <?= e($service['number']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="services-story__content" data-services-content>
            <?php foreach ($services as $index => $service): ?>
                <article
                    class="service-panel<?= $index === 0 ? ' is-active' : '' ?>"
                    data-service-panel
                    data-service-index="<?= e((string) $index) ?>"
                    aria-hidden="<?= $index === 0 ? 'false' : 'true' ?>"
                >
                    <p class="service-panel__eyebrow" aria-hidden="true">
                        <?= e($service['number']) ?>
                    </p>

                    <h2 class="service-panel__title">
                        <?= e($service['title']) ?>
                    </h2>

                    <p class="service-panel__description">
                        <?= e($service['description']) ?>
                    </p>

                    <div class="service-panel__tags" aria-label="Service capabilities">
                        <?php foreach ($service['tags'] as $tag): ?>
                            <span class="service-panel__tag"><?= e($tag) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <figure class="service-panel__mobile-visual" aria-hidden="true">
                        <img src="<?= e(asset($service['image'])) ?>" alt="" draggable="false">
                    </figure>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="services-story__visuals" aria-hidden="true">
            <?php foreach ($services as $index => $service): ?>
                <figure
                    class="service-visual<?= $index === 0 ? ' is-active' : '' ?>"
                    data-service-visual
                    data-service-index="<?= e((string) $index) ?>"
                >
                    <img
                        src="<?= e(asset($service['image'])) ?>"
                        alt=""
                        draggable="false"
                    >
                </figure>
            <?php endforeach; ?>
        </div>

        <div class="services-story__progress" aria-hidden="true">
            <span data-services-progress>01 / 05</span>
        </div>
    </div>
</section>
