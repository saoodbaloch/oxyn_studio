(() => {
    const section = document.querySelector('[data-services-story]');
    if (!section) return;

    const panels = [...section.querySelectorAll('[data-service-panel]')];
    const visuals = [...section.querySelectorAll('[data-service-visual]')];
    const wheel = section.querySelector('[data-services-wheel]');
    const numbers = [...section.querySelectorAll('[data-service-wheel-number]')];
    const activeDot = section.querySelector('.services-wheel__active-dot');
    const progressLabel = section.querySelector('[data-services-progress]');

    if (!panels.length || panels.length !== visuals.length) return;

    const COUNT = panels.length;
    const LAST = COUNT - 1;
    const STEP_ANGLE = 30;
    const desktop = window.matchMedia('(min-width: 901px)');
    const mobile = window.matchMedia('(max-width: 760px)');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    let rafId = 0;
    let activeIndex = -1;

    const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

    const getProgress = () => {
        /* Use document coordinates, not the sticky child's current rect. This stays
           stable on iOS Safari while the viewport is pinned. */
        const stickyTop = mobile.matches ? 64 : 0;
        const sectionTop = section.offsetTop;
        const viewportHeight = mobile.matches
            ? Math.max(1, window.innerHeight - stickyTop)
            : window.innerHeight;
        const scrollable = Math.max(1, section.offsetHeight - viewportHeight);
        return clamp((window.scrollY + stickyTop - sectionTop) / scrollable, 0, 1);
    };

    const setActiveIndex = (index) => {
        if (index === activeIndex) return;
        activeIndex = index;

        panels.forEach((panel, i) => {
            const active = i === index;
            panel.classList.toggle('is-active', active);
            panel.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        visuals.forEach((visual, i) => {
            visual.classList.toggle('is-active', i === index);
        });

        numbers.forEach((number, i) => {
            number.classList.toggle('is-current', i === index);
        });

        if (progressLabel) {
            progressLabel.textContent =
                `${String(index + 1).padStart(2, '0')} / ${String(COUNT).padStart(2, '0')}`;
        }
    };

    /*
     * Reference dial behaviour:
     * - the numbered rail moves continuously with the same scroll progress as copy
     * - spacing is deliberately generous (01 → 02 etc.)
     * - the red dot does NOT glide continuously; it jumps to the newly active
     *   number, but always remains exactly on the circular line.
     */
    const updateWheel = (position, activeIndex) => {
        if (!wheel) return;

        const size = wheel.getBoundingClientRect().width;
        if (!size) return;

        // The ring stroke sits on the wheel's exact outer radius. Keep the
        // dot on that same geometry (not an inset radius). A tiny 1px inset
        // keeps the dot visually centered over the 1px border.
        const radius = Math.max(0, size / 2 - 1);

        numbers.forEach((number, numberIndex) => {
            const angle = ((numberIndex - position) * STEP_ANGLE) * Math.PI / 180;
            const distance = Math.abs(numberIndex - position);
            const x = Math.cos(angle) * radius;
            const y = Math.sin(angle) * radius;

            number.style.transform =
                `translate(-50%, -50%) translate3d(${x}px, ${y}px, 0)`;
            number.style.opacity = String(Math.max(.16, 1 - distance * .24));
        });

        if (activeDot) {
            /* Jump to the current service's exact point on the moving arc. */
            const dotAngle = ((activeIndex - position) * STEP_ANGLE) * Math.PI / 180;
            const x = Math.cos(dotAngle) * radius;
            const y = Math.sin(dotAngle) * radius;

            activeDot.style.transform =
                `translate(-50%, -50%) translate3d(${x}px, ${y}px, 0)`;
        }
    };

    /*
     * Mobile geometry is READ FROM THE DOM instead of being hard-coded, so the
     * rail can never drift away from the stroke when the CSS ellipse changes.
     * Everything below is expressed in the wheel's own coordinate space.
     */
    const mobileRing = wheel ? wheel.querySelector('.services-wheel__ring') : null;

    /* Pin a node to the wheel's own coordinate space (0,0 = wheel top-left). */
    const setPinned = (element, transform) => {
        element.style.setProperty('left', '0px', 'important');
        element.style.setProperty('top', '0px', 'important');
        element.style.setProperty('right', 'auto', 'important');
        element.style.setProperty('bottom', 'auto', 'important');
        element.style.setProperty('margin', '0', 'important');
        element.style.setProperty('transform', transform, 'important');
    };

    const readMobileGeometry = () => {
        if (!wheel || !mobileRing) return null;

        const wheelBox = wheel.getBoundingClientRect();
        const ringBox = mobileRing.getBoundingClientRect();
        if (!wheelBox.width || !ringBox.width || !ringBox.height) return null;

        return {
            width: wheelBox.width,
            rx: ringBox.width / 2,
            ry: ringBox.height / 2,
            cx: ringBox.left - wheelBox.left + ringBox.width / 2,
            cy: ringBox.top - wheelBox.top + ringBox.height / 2,
        };
    };

    const updateMobileWheel = (position, currentIndex) => {
        if (!wheel) return;

        const geo = readMobileGeometry();
        if (!geo) return;

        /* Mobile reference geometry:
         * - numbers ride the SAME shallow curve as the visible stroke
         * - each label is rotated to the tangent of that stroke
         * - the rail glides continuously with scroll
         * - the red indicator jumps at each active service, like desktop,
         *   while staying exactly on the curve
         */
        const spacing = clamp(geo.width * .32, 104, 134);

        /* Lower arc of the ellipse: y = cy + ry * sqrt(1 - (x/rx)^2) */
        const pointAt = (xFromCenter) => {
            const nx = clamp(xFromCenter / geo.rx, -.985, .985);
            const root = Math.sqrt(Math.max(1e-4, 1 - nx * nx));
            const slope = -(geo.ry / geo.rx) * (nx / root);

            return {
                y: geo.cy + geo.ry * root,
                angle: Math.atan(slope) * 180 / Math.PI,
            };
        };

        numbers.forEach((number, numberIndex) => {
            const delta = numberIndex - position;
            const xFromCenter = delta * spacing;
            const spot = pointAt(xFromCenter);
            /* Keep far-off labels from tipping over near the steep ends. */
            const angle = clamp(spot.angle, -24, 24);

            // Labels sit just beneath the stroke, following its curvature.
            const labelOffset = numberIndex === currentIndex ? 34 : 30;
            const x = geo.cx + xFromCenter;
            const y = spot.y + labelOffset;

            /* Legacy mobile layers still carry `left/top/transform !important`
               for these nodes. Writing with `important` keeps the script
               authoritative without having to unpick that older CSS. */
            setPinned(number, `translate3d(${x}px, ${y}px, 0) translate(-50%, -50%) rotate(${angle.toFixed(2)}deg)`);
            number.style.setProperty('opacity', String(clamp(1 - Math.abs(delta) * .22, .16, 1)), 'important');
        });

        if (activeDot) {
            // currentIndex changes discretely, therefore the dot visibly jumps to
            // the next service point while the number rail itself keeps gliding.
            const dotXFromCenter = (currentIndex - position) * spacing;
            const dotSpot = pointAt(dotXFromCenter);

            setPinned(activeDot, `translate3d(${geo.cx + dotXFromCenter}px, ${dotSpot.y}px, 0) translate(-50%, -50%)`);
        }
    };

    const updateMobilePanels = (index) => {
        panels.forEach((panel, i) => {
            const active = i === index;
            panel.classList.toggle('is-active', active);
            panel.setAttribute('aria-hidden', active ? 'false' : 'true');
            panel.style.transform = active ? 'translate3d(0,0,0)' : 'translate3d(0,12px,0)';
            panel.style.filter = active ? 'none' : 'blur(3px)';
            panel.style.opacity = active ? '1' : '0';
            panel.style.visibility = active ? 'visible' : 'hidden';
            panel.style.zIndex = active ? '2' : '1';
        });
    };

    const updatePanels = (position) => {
        const viewport = section.querySelector('.services-story__viewport');
        const viewportHeight = viewport?.clientHeight || window.innerHeight;

        /*
         * One full service step has enough vertical travel for the current copy
         * to physically move up into/behind the sticky navbar before the next
         * copy reaches the reading position. This is why 01/02/03 spacing is
         * intentionally generous in the wheel as well.
         */
        const travel = clamp(viewportHeight * .66, 430, 690);

        panels.forEach((panel, index) => {
            const delta = index - position;
            const distance = Math.abs(delta);
            const y = delta * travel;

            /*
             * Outgoing copy does NOT fade out. It remains solid and is removed
             * naturally by the viewport clipping as it travels behind the navbar.
             * Incoming copy may start softly blurred, then becomes crisp in place.
             */
            const incoming = delta > 0;
            const blur = incoming ? clamp(distance * 7.5, 0, 9) : 0;
            const incomingOpacity = incoming ? clamp(1 - Math.max(0, distance - .48) * 1.35, .28, 1) : 1;

            panel.style.opacity = String(incomingOpacity);
            panel.style.transform = `translate3d(0, ${y}px, 0)`;
            panel.style.filter = blur > .05 ? `blur(${blur}px)` : 'none';
            panel.style.visibility = distance <= 1.2 ? 'visible' : 'hidden';
            panel.style.zIndex = String(20 - Math.round(distance * 4));
        });
    };

    const updateVisuals = (position) => {
        visuals.forEach((visual, index) => {
            const delta = index - position;
            const distance = Math.abs(delta);

            /* Keep the art stable; only a restrained cross-transition is used. */
            const opacity = clamp(1 - distance * 1.35, 0, 1);
            const scale = 1 - Math.min(.018, distance * .012);
            const blur = clamp(distance * 3.5, 0, 4);

            visual.style.opacity = String(opacity);
            visual.style.transform = `translate3d(0, 0, 0) scale(${scale})`;
            visual.style.filter = blur > .05 ? `blur(${blur}px)` : 'none';
            visual.style.visibility = opacity > .01 ? 'visible' : 'hidden';
        });
    };

    const render = () => {
        rafId = 0;
        if (!desktop.matches && !mobile.matches) return;

        const progress = getProgress();
        const position = progress * LAST;
        const nearest = clamp(Math.round(position), 0, LAST);

        setActiveIndex(nearest);

        if (mobile.matches) {
            updateMobileWheel(position, nearest);
            updateMobilePanels(nearest);
            return;
        }

        updateWheel(position, nearest);

        if (reduced.matches) {
            panels.forEach((panel, i) => {
                const active = i === nearest;
                panel.style.opacity = active ? '1' : '0';
                panel.style.transform = 'translate3d(0,0,0)';
                panel.style.filter = 'none';
                panel.style.visibility = active ? 'visible' : 'hidden';
            });

            visuals.forEach((visual, i) => {
                const active = i === nearest;
                visual.style.opacity = active ? '1' : '0';
                visual.style.transform = 'translate3d(0,0,0) scale(1)';
                visual.style.filter = 'none';
                visual.style.visibility = active ? 'visible' : 'hidden';
            });
        } else {
            updatePanels(position);
            updateVisuals(position);
        }
    };

    const requestRender = () => {
        if (!rafId) rafId = requestAnimationFrame(render);
    };

    const clearInline = () => {
        panels.forEach(panel => panel.removeAttribute('style'));
        visuals.forEach(visual => visual.removeAttribute('style'));
        numbers.forEach(number => number.removeAttribute('style'));
        activeDot?.removeAttribute('style');
        activeIndex = -1;
    };

    const syncMode = () => {
        clearInline();
        if (desktop.matches || mobile.matches) requestRender();
    };

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    window.addEventListener('pageshow', requestRender);
    document.addEventListener('DOMContentLoaded', requestRender);

    desktop.addEventListener?.('change', syncMode);
    mobile.addEventListener?.('change', syncMode);
    reduced.addEventListener?.('change', requestRender);

    syncMode();
})();
