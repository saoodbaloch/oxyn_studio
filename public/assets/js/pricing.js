(() => {
    const root = document.querySelector('[data-pricing]');
    if (!root) return;

    const toggle = root.querySelector('[data-pricing-toggle]');
    const card = root.querySelector('[data-pricing-card]');
    const name = root.querySelector('[data-pricing-name]');
    const description = root.querySelector('[data-pricing-description]');
    const price = root.querySelector('[data-pricing-price]');
    const features = root.querySelector('[data-pricing-features]');
    const popular = root.querySelector('[data-pricing-popular]');
    const creatorLabel = root.querySelector('[data-pricing-label="creator"]');
    const agencyLabel = root.querySelector('[data-pricing-label="agency"]');

    const plans = {
        creator: {
            name: 'For Creators',
            description: 'Ideal For Individual Creators And<br class="pricing-card__desktop-break">Freelancers Building Growth',
            price: '$899',
            features: [
                '18 Video Edits Per Month',
                '24-Hour Turnaround Time',
                'Access To Stock Footage',
                '3 Revisions Per Video',
                'Email Support & More',
                'Custom Intro And Outro Options',
                'Basic Social Media Optimization'
            ],
            popular: true
        },
        agency: {
            name: 'For Agencies',
            description: 'Tailored For Agencies Handling<br class="pricing-card__desktop-break">Multiple Clients & Brands',
            price: '$2499',
            features: [
                '50 Video Edits Per Month',
                '28-Hour Turnaround Time',
                'Access To Every Tools',
                '10 Revisions Per Video',
                'Email Support & More',
                'Custom Intro And Outro Options',
                'Full Social Media Optimization'
            ],
            popular: false
        }
    };

    let current = 'creator';
    let timer = 0;

    function render(planKey) {
        const plan = plans[planKey];
        card.classList.add('is-switching');
        window.clearTimeout(timer);

        timer = window.setTimeout(() => {
            name.textContent = plan.name;
            description.innerHTML = plan.description;
            price.textContent = plan.price;
            features.innerHTML = plan.features.map(item => `<li>${item}</li>`).join('');
            popular.classList.toggle('is-hidden', !plan.popular);
            creatorLabel.classList.toggle('is-active', planKey === 'creator');
            agencyLabel.classList.toggle('is-active', planKey === 'agency');
            toggle.setAttribute('aria-checked', planKey === 'agency' ? 'true' : 'false');

            requestAnimationFrame(() => {
                card.classList.remove('is-switching');
            });
        }, 110);
    }

    toggle.addEventListener('click', () => {
        current = current === 'creator' ? 'agency' : 'creator';
        render(current);
    });
})();
