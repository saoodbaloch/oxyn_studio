(() => {
  'use strict';
  const mq = window.matchMedia('(max-width: 760px)');

  /* Mobile shutter navigation. Existing main.js still owns the basic is-open toggle. */
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-primary-nav]');
  const syncNav = () => {
    if (!toggle || !nav) return;
    const open = mq.matches && nav.classList.contains('is-open');
    document.body.classList.toggle('mobile-nav-open', open);
  };
  toggle?.addEventListener('click', () => requestAnimationFrame(syncNav));
  nav?.addEventListener('click', e => {
    if (e.target.closest('a')) requestAnimationFrame(syncNav);
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && mq.matches && nav?.classList.contains('is-open')) {
      nav.classList.remove('is-open');
      toggle?.setAttribute('aria-expanded','false');
      syncNav();
    }
  });

  /* Services are controlled only by services-scroll.js. Keeping one controller prevents
     competing active states and makes the sticky 01→05 story reliable on mobile. */

  /* Testimonials retain native horizontal swipe; existing video controls remain untouched. */
  const rail = document.querySelector('.testimonials__cards');
  if (rail && mq.matches) rail.scrollLeft = 0;
})();
