(() => {
    'use strict';

    const section = document.querySelector('[data-testimonials]');
    if (!section) return;

    const cards = [...section.querySelectorAll('.testimonial-card')];

    const tryPlay = (video) => {
        const promise = video.play();
        if (promise && typeof promise.catch === 'function') {
            promise.catch(() => {
                // Autoplay can be blocked by browser policy. The button remains usable.
            });
        }
    };

    cards.forEach((card) => {
        const video = card.querySelector('[data-testimonial-video]');
        const button = card.querySelector('[data-testimonial-toggle]');
        const label = card.querySelector('[data-testimonial-label]');

        if (!video || !button || !label) return;

        video.muted = true;
        video.loop = true;
        video.playsInline = true;
        video.dataset.userPaused = 'false';

        const sync = () => {
            const paused = video.paused;
            button.classList.toggle('is-paused', paused);
            button.setAttribute('aria-pressed', paused ? 'true' : 'false');
            button.setAttribute('aria-label', paused ? 'Play testimonial video' : 'Pause testimonial video');
            label.textContent = paused ? 'Play' : 'Pause';
        };

        button.addEventListener('click', () => {
            if (video.paused) {
                video.dataset.userPaused = 'false';
                tryPlay(video);
            } else {
                video.dataset.userPaused = 'true';
                video.pause();
            }
            window.setTimeout(sync, 0);
        });

        video.addEventListener('play', sync);
        video.addEventListener('pause', sync);
        video.addEventListener('loadeddata', () => {
            if (video.dataset.userPaused !== 'true') tryPlay(video);
            sync();
        }, { once: true });

        tryPlay(video);
        sync();
    });

    // Browser tab visibility: resume ONLY videos the user did not pause.
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) return;
        cards.forEach((card) => {
            const video = card.querySelector('[data-testimonial-video]');
            if (video && video.dataset.userPaused !== 'true') tryPlay(video);
        });
    });
})();
