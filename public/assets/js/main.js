(() => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-primary-nav]');

    if (!toggle || !nav) return;

    toggle.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            nav.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();


(() => {
    const header = document.querySelector('[data-site-header]');
    if (!header) return;

    let ticking = false;

    const updateHeader = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 24);
        ticking = false;
    };

    const requestUpdate = () => {
        if (ticking) return;
        ticking = true;
        window.requestAnimationFrame(updateHeader);
    };

    updateHeader();

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate, { passive: true });
})();
