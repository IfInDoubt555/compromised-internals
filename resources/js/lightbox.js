(() => {
  const root = document.getElementById('ci-lightbox-root');
  if (!root) return;

  const imgEl = root.querySelector('#ci-lightbox-img');
  const backdrop = root.querySelector('[data-ci="backdrop"]');
  const btnClose = root.querySelector('[data-ci="close"]');

  let prevOverflow = '';
  let active = false;

  const open = (src) => {
    if (!src) return;
    active = true;
    imgEl.src = src;

    // show
    root.classList.remove('hidden');
    prevOverflow = document.documentElement.style.overflow;
    document.documentElement.style.overflow = 'hidden';
    root.setAttribute('aria-hidden', 'false');

    // small fade / scale-in
    requestAnimationFrame(() => {
      backdrop.classList.remove('opacity-0');
      imgEl.classList.remove('opacity-0', 'scale-95');
    });

    // focus for a11y
    btnClose.focus({ preventScroll: true });
  };

  const close = () => {
    if (!active) return;
    active = false;

    // animate out
    backdrop.classList.add('opacity-0');
    imgEl.classList.add('opacity-0', 'scale-95');

    setTimeout(() => {
      root.classList.add('hidden');
      root.setAttribute('aria-hidden', 'true');
      imgEl.removeAttribute('src');
      document.documentElement.style.overflow = prevOverflow || '';
    }, 150);
  };

  // Close interactions
  btnClose.addEventListener('click', close);
  backdrop.addEventListener('click', close);
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });

  // Delegate clicks on images within any .js-lightbox-scope container
  document.addEventListener('click', (e) => {
    if (active) return;

    const img = e.target.closest('.js-lightbox-scope img');
    if (!img || img.dataset.noLightbox !== undefined) return;

    // prevent parent link navigation if any
    if (e.target.tagName === 'IMG') e.preventDefault();
    e.stopPropagation();

    const src = img.currentSrc || img.src;
    open(src);
  }, { capture: true });
})();