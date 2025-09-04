// Simple Alpine-powered lightbox (no deps)
document.addEventListener('alpine:init', () => {
  Alpine.store('lightbox', {
    open: false,
    src: '',
    alt: '',
    show(src, alt = '') {
      this.src = src;
      this.alt = alt;
      this.open = true;
      document.body.classList.add('overflow-hidden');
    },
    close() {
      this.open = false;
      this.src = '';
      this.alt = '';
      document.body.classList.remove('overflow-hidden');
    },
  });
});

// Delegate clicks on images inside any .js-lightbox-scope container
document.addEventListener('click', (e) => {
  const img = e.target.closest('.js-lightbox-scope img');
  if (!img) return;
  if (img.hasAttribute('data-nolightbox')) return;

  // If inside an anchor, prevent navigation
  const a = img.closest('a');
  if (a) e.preventDefault();

  const src = img.getAttribute('src');
  const alt = img.getAttribute('alt') || '';

  if (window.Alpine?.store('lightbox')) {
    window.Alpine.store('lightbox').show(src, alt);
  }
});

// Close on ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && window.Alpine?.store('lightbox')) {
    window.Alpine.store('lightbox').close();
  }
});