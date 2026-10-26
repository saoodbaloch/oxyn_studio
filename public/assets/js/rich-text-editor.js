(() => {
    const target = document.querySelector('[data-format-target]');
    const toolbar = document.querySelector('[data-editor-toolbar]');

    if (!target || !toolbar) return;

    const headingSelect = toolbar.querySelector('[data-heading-select]');
    const formatButtons = toolbar.querySelectorAll('[data-format]');
    const colorToggle = toolbar.querySelector('[data-color-toggle]');
    const palette = toolbar.querySelector('[data-color-palette]');
    const swatches = toolbar.querySelectorAll('[data-text-color]');
    const colorLetter = toolbar.querySelector('[data-color-letter]');

    const state = {
        bold: false,
        italic: false,
        underline: false,
        heading: '1',
        color: '#111111'
    };

    const applyState = () => {
        target.classList.toggle('is-bold', state.bold);
        target.classList.toggle('is-italic', state.italic);
        target.classList.toggle('is-underlined', state.underline);

        target.dataset.heading = state.heading;
        target.style.color = state.color;

        formatButtons.forEach((button) => {
            const key = button.dataset.format;
            const active = Boolean(state[key]);
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', String(active));
        });

        swatches.forEach((swatch) => {
            swatch.classList.toggle('is-active', swatch.dataset.textColor?.toLowerCase() === state.color.toLowerCase());
        });

        if (colorLetter) colorLetter.style.color = state.color;
    };

    formatButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.format;
            if (!(key in state)) return;
            state[key] = !state[key];
            applyState();
        });
    });

    if (headingSelect) {
        headingSelect.addEventListener('change', () => {
            state.heading = headingSelect.value;
            applyState();
        });
    }

    const closePalette = () => {
        if (!palette || !colorToggle) return;
        palette.classList.remove('is-open');
        colorToggle.classList.remove('is-open');
        colorToggle.setAttribute('aria-expanded', 'false');
    };

    if (colorToggle && palette) {
        colorToggle.addEventListener('click', () => {
            const isOpen = palette.classList.toggle('is-open');
            colorToggle.classList.toggle('is-open', isOpen);
            colorToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    swatches.forEach((swatch) => {
        swatch.addEventListener('click', () => {
            const color = swatch.dataset.textColor;
            if (!color) return;
            state.color = color;
            applyState();
            closePalette();
        });
    });

    document.addEventListener('pointerdown', (event) => {
        if (!toolbar.contains(event.target)) closePalette();
    });

    applyState();
})();
