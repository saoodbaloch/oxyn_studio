(() => {
  const sidebar = document.querySelector('[data-admin-sidebar]');
  const overlay = document.querySelector('[data-admin-overlay]');
  const toggle = document.querySelector('[data-admin-menu-toggle]');

  const closeDrawer = () => {
    sidebar?.classList.remove('is-open');
    overlay?.classList.remove('is-open');
  };
  const openDrawer = () => {
    sidebar?.classList.add('is-open');
    overlay?.classList.add('is-open');
  };

  toggle?.addEventListener('click', () => {
    if (sidebar?.classList.contains('is-open')) closeDrawer(); else openDrawer();
  });
  overlay?.addEventListener('click', closeDrawer);

  document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      const msg = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(msg)) e.preventDefault();
    });
  });

  // Upload drop zones: clicking the dashed box opens the hidden file input,
  // and shows the chosen filename so it doesn't look like nothing happened.
  document.querySelectorAll('[data-upload-drop]').forEach((drop) => {
    const input = drop.querySelector('input[type="file"]');
    const label = drop.querySelector('[data-upload-filename]');
    if (!input) return;
    input.addEventListener('change', () => {
      if (input.files && input.files[0] && label) {
        label.textContent = input.files[0].name;
      }
    });
  });

  // Auto-slug preview on blog post title field.
  const titleInput = document.querySelector('[data-slug-source]');
  const slugPreview = document.querySelector('[data-slug-preview]');
  if (titleInput && slugPreview) {
    const slugify = (s) => s.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'post';
    titleInput.addEventListener('input', () => {
      if (!slugPreview.dataset.locked) {
        slugPreview.textContent = slugify(titleInput.value);
      }
    });
  }
})();
