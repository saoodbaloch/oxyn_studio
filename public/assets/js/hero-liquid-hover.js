(() => {
    const artwork = document.querySelector('[data-liquid-hover]');
    const noise = document.querySelector('#hero-liquid-noise');
    const displacement = document.querySelector('#hero-liquid-displacement');

    if (!artwork || !noise || !displacement) return;
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    let targetX = 50;
    let targetY = 50;
    let currentX = 50;
    let currentY = 50;
    let velocityX = 0;
    let velocityY = 0;
    let previousX = 50;
    let previousY = 50;
    let active = false;
    let raf = 0;
    let start = performance.now();

    const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

    const animate = (now) => {
        if (!active) {
            raf = 0;
            return;
        }

        const t = (now - start) / 1000;

        currentX += (targetX - currentX) * 0.19;
        currentY += (targetY - currentY) * 0.19;

        velocityX = currentX - previousX;
        velocityY = currentY - previousY;
        previousX = currentX;
        previousY = currentY;

        const speed = Math.sqrt(velocityX * velocityX + velocityY * velocityY);
        const wobble = Math.sin(t * 7.6) * 3.2 + Math.sin(t * 12.4 + 0.9) * 2.2;
        const scale = clamp(19 + speed * 10 + wobble, 16, 40);

        const fx = 0.0085 + Math.sin(t * 1.9) * 0.0014;
        const fy = 0.0205 + Math.cos(t * 1.55) * 0.0028;

        noise.setAttribute('baseFrequency', `${fx.toFixed(4)} ${fy.toFixed(4)}`);
        noise.setAttribute('seed', String(7 + Math.floor((t * 4) % 7)));
        displacement.setAttribute('scale', scale.toFixed(2));

        artwork.style.setProperty('--mx', `${currentX}%`);
        artwork.style.setProperty('--my', `${currentY}%`);
        artwork.style.setProperty('--ripple-x', `${clamp(velocityX * 5.5, -12, 12)}px`);
        artwork.style.setProperty('--ripple-y', `${clamp(velocityY * 5.5, -12, 12)}px`);
        artwork.style.setProperty('--ripple-rotate', `${clamp(velocityX * 0.55, -2.2, 2.2)}deg`);
        artwork.style.setProperty('--ripple-scale', `${1.018 + Math.min(speed * 0.0025, 0.022)}`);

        raf = requestAnimationFrame(animate);
    };

    artwork.addEventListener('pointerenter', (event) => {
        const rect = artwork.getBoundingClientRect();
        targetX = ((event.clientX - rect.left) / rect.width) * 100;
        targetY = ((event.clientY - rect.top) / rect.height) * 100;
        currentX = targetX;
        currentY = targetY;
        previousX = targetX;
        previousY = targetY;
        start = performance.now();
        active = true;
        artwork.classList.add('is-hovering');
        if (!raf) raf = requestAnimationFrame(animate);
    });

    artwork.addEventListener('pointermove', (event) => {
        const rect = artwork.getBoundingClientRect();
        targetX = clamp(((event.clientX - rect.left) / rect.width) * 100, 0, 100);
        targetY = clamp(((event.clientY - rect.top) / rect.height) * 100, 0, 100);
    });

    artwork.addEventListener('pointerleave', () => {
        active = false;
        artwork.classList.remove('is-hovering');
        artwork.style.setProperty('--ripple-x', '0px');
        artwork.style.setProperty('--ripple-y', '0px');
        artwork.style.setProperty('--ripple-rotate', '0deg');
        artwork.style.setProperty('--ripple-scale', '1.018');
    });
})();
