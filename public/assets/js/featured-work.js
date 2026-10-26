(() => {
    const root = document.querySelector('[data-featured-work]');
    if (!root) return;

    const toggle = root.querySelector('[data-featured-toggle]');
    const modeButtons = [...root.querySelectorAll('[data-featured-mode-button]')];

    const carousels = {
        short: root.querySelector('[data-featured-carousel="short"]'),
        long: root.querySelector('[data-featured-carousel="long"]')
    };

    let activeMode = 'short';

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const hasGsap = Boolean(window.gsap && window.Flip);

    if (hasGsap) {
        window.gsap.registerPlugin(window.Flip);
        root.classList.add('is-gsap-ready');
    }

    const getSlides = (carousel) =>
        carousel ? [...carousel.querySelectorAll('[data-featured-slide]')] : [];

    const getVideos = () =>
        [...root.querySelectorAll('[data-featured-video]')];

    const signedDistance = (index, active, length) => {
        let distance = index - active;

        if (distance > length / 2) distance -= length;
        if (distance < -length / 2) distance += length;

        return distance;
    };

    const updatePlayButton = (video) => {
        const slide = video.closest('[data-featured-slide]');
        const button = slide?.querySelector('[data-featured-play]');
        const label = button?.querySelector('[data-featured-play-label]');

        if (!button || !label) return;

        const playing = !video.paused && !video.ended;

        button.classList.toggle('is-playing', playing);
        button.setAttribute('aria-label', playing ? 'Pause video' : 'Play video');
        label.textContent = playing ? 'Pause' : 'Play';
    };

    const tryPlay = async (video) => {
        if (!video) return;

        video.muted = true;

        try {
            await video.play();
        } catch (_) {
            /* Muted autoplay may still be blocked by browser policy. */
        }

        updatePlayButton(video);
    };

    const clearPositionClasses = (slide) => {
        slide.classList.remove(
            'is-center',
            'is-left',
            'is-right',
            'is-far-left',
            'is-far-right',
            'is-off-left',
            'is-off-right',
            'is-visible'
        );
    };

    const applyPositions = (carousel, requestedIndex) => {
        if (!carousel) return 0;

        const slides = getSlides(carousel);
        const length = slides.length;

        if (!length) return 0;

        const active = ((requestedIndex % length) + length) % length;
        carousel.dataset.activeIndex = String(active);

        slides.forEach((slide, index) => {
            clearPositionClasses(slide);

            const distance = signedDistance(index, active, length);

            if (distance === 0) {
                slide.classList.add('is-center', 'is-visible');
            } else if (distance === -1) {
                slide.classList.add('is-left', 'is-visible');
            } else if (distance === 1) {
                slide.classList.add('is-right', 'is-visible');
            } else if (distance === -2) {
                slide.classList.add('is-far-left', 'is-visible');
            } else if (distance === 2) {
                slide.classList.add('is-far-right', 'is-visible');
            } else if (distance < -2) {
                slide.classList.add('is-off-left', 'is-visible');
            } else if (distance > 2) {
                slide.classList.add('is-off-right', 'is-visible');
            }
        });

        return active;
    };

    const motionState = new WeakMap();

    const getMotionState = (carousel) => {
        if (!motionState.has(carousel)) {
            motionState.set(carousel, {
                animating: false,
                queuedIndex: null
            });
        }

        return motionState.get(carousel);
    };

    const animateTo = (carousel, requestedIndex) => {
        if (!carousel) return;

        const slides = getSlides(carousel);
        if (!slides.length) return;

        const length = slides.length;
        const target = ((requestedIndex % length) + length) % length;
        const state = getMotionState(carousel);

        if (
            reduceMotion.matches ||
            !hasGsap ||
            slides.length < 2
        ) {
            applyPositions(carousel, target);
            return;
        }

        if (state.animating) {
            /*
             * Do not stack competing tweens.
             * Keep only the latest requested destination.
             */
            state.queuedIndex = target;
            return;
        }

        state.animating = true;
        state.queuedIndex = null;

        root.classList.add('is-gsap-animating');

        /*
         * FLIP:
         * Capture the exact pixel geometry before changing carousel classes.
         * Then change the layout instantly and let Flip animate every card
         * from its previous on-screen position to the new one.
         */
        const flipState = window.Flip.getState(slides, {
            props: 'opacity,filter,borderRadius'
        });

        applyPositions(carousel, target);

        window.Flip.from(flipState, {
            duration: 1.05,
            ease: 'power3.inOut',
            scale: true,
            simple: false,
            absolute: false,
            nested: true,
            prune: true,
            fade: true,

            onComplete: () => {
                state.animating = false;
                root.classList.remove('is-gsap-animating');

                if (
                    state.queuedIndex !== null &&
                    state.queuedIndex !== Number(carousel.dataset.activeIndex || 0)
                ) {
                    const nextTarget = state.queuedIndex;
                    state.queuedIndex = null;

                    requestAnimationFrame(() => {
                        animateTo(carousel, nextTarget);
                    });
                } else {
                    state.queuedIndex = null;
                }
            }
        });
    };

    const setMode = (mode) => {
        if (!carousels[mode]) return;

        activeMode = mode;

        Object.entries(carousels).forEach(([key, carousel]) => {
            if (!carousel) return;

            const visible = key === mode;
            carousel.hidden = !visible;
            carousel.classList.toggle('is-active', visible);
        });

        const longMode = mode === 'long';

        toggle?.setAttribute('aria-checked', String(longMode));

        modeButtons.forEach((button) => {
            button.classList.toggle(
                'is-active',
                button.dataset.featuredModeButton === mode
            );
        });

        const carousel = carousels[mode];
        const current = Number(carousel?.dataset.activeIndex || 0);

        applyPositions(carousel, current);

        /*
         * Toggle changes visibility/layout only.
         * It never changes any video's play/pause state.
         */
    };

    const bindCarousel = (carousel) => {
        if (!carousel) return;

        const slides = getSlides(carousel);
        const prev = carousel.querySelector('[data-featured-prev]');
        const next = carousel.querySelector('[data-featured-next]');

        applyPositions(carousel, 0);

        prev?.addEventListener('click', () => {
            const current = Number(carousel.dataset.activeIndex || 0);
            animateTo(carousel, current - 1);
        });

        next?.addEventListener('click', () => {
            const current = Number(carousel.dataset.activeIndex || 0);
            animateTo(carousel, current + 1);
        });

        slides.forEach((slide, index) => {
            const video = slide.querySelector('[data-featured-video]');
            const playButton = slide.querySelector('[data-featured-play]');

            if (!video) return;

            video.muted = true;
            video.loop = true;

            video.addEventListener('play', () => updatePlayButton(video));
            video.addEventListener('pause', () => updatePlayButton(video));
            video.addEventListener('ended', () => updatePlayButton(video));

            video.addEventListener('click', async (event) => {
                event.preventDefault();

                if (!slide.classList.contains('is-center')) {
                    animateTo(carousel, index);
                    return;
                }

                /*
                 * Playback remains independent:
                 * clicking center pauses/resumes ONLY this video.
                 */
                if (video.paused) {
                    await tryPlay(video);
                } else {
                    video.pause();
                    updatePlayButton(video);
                }
            });

            playButton?.addEventListener('click', async (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (video.paused) {
                    await tryPlay(video);
                } else {
                    video.pause();
                    updatePlayButton(video);
                }
            });

            updatePlayButton(video);
        });
    };

    bindCarousel(carousels.short);
    bindCarousel(carousels.long);

    toggle?.addEventListener('click', () => {
        setMode(activeMode === 'short' ? 'long' : 'short');
    });

    modeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setMode(button.dataset.featuredModeButton);
        });
    });

    window.addEventListener('load', () => {
        setMode('short');

        getVideos().forEach((video) => {
            video.currentTime = 0;
            tryPlay(video);
        });
    }, { once: true });
})();
