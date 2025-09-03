import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';

// ⛔️ Remove eager imports of heavy modules
// import './stages';
// import initScrollControls from './scrollControls';
// import initCalendar from './calendar';

window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(focus);

const prefersDark = () =>
  window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

document.addEventListener('alpine:init', () => {
  // Full-featured theme store (light | dark | system), integrated with CI_THEME
  Alpine.store('theme', {
    mode: (window.CI_THEME?.getMode?.()) || localStorage.getItem('ci-theme') || 'system',

    get dark() {
      return this.mode === 'dark' || (this.mode === 'system' && prefersDark());
    },

    setMode(mode) {
      this.mode = mode;                 // reflect in Alpine
      window.CI_THEME?.setMode?.(mode); // apply + persist + broadcast
    },

    // aliases for UI calls
    choose(mode) { this.setMode(mode); },
    toggle() {
      this.setMode(this.mode === 'light' ? 'dark'
                 : this.mode === 'dark'  ? 'system'
                 :                          'light');
    },
    cycle() { this.toggle(); }
  });

  // Keep Alpine store in sync if CI_THEME changes from elsewhere
  document.addEventListener('ci-theme:changed', (e) => {
    Alpine.store('theme').mode = e.detail.mode;
  });
});

Alpine.start();

/**
 * Lazy/conditional boot
 * - Only load modules if the page actually uses them (data-* hooks)
 * - On mobile, delay to idle so LCP stays clean
 */
const isMobile = () => window.matchMedia && matchMedia('(max-width: 768px)').matches;
const idle = (fn) => ('requestIdleCallback' in window) ? requestIdleCallback(fn) : setTimeout(fn, 200);

document.addEventListener('DOMContentLoaded', () => {
  // Scroll controls
  if (document.querySelector('[data-has-scroll-controls]')) {
    const boot = () => import('./scrollControls').then(m => {
      const fn = m.default || m.initScrollControls;
      if (typeof fn === 'function') fn();
    });    isMobile() ? idle(boot) : boot();
  }

  // Calendar (expects same signature as before: initCalendar('calendar'))
  if (document.querySelector('[data-calendar]')) {
    const boot = () => import('./calendar').then(m => {
      const initCalendar = m.default || m.initCalendar;
      if (typeof initCalendar === 'function') initCalendar('calendar');
    });
    isMobile() ? idle(boot) : boot();
  }

  // Stages module (side effects only)
  if (document.querySelector('[data-stages]')) {
    const boot = () => import('./stages');
    isMobile() ? idle(boot) : boot();
  }

  // ---- Keep a CSS var with the current nav height
(function setNavHeightVar() {
  const nav = document.getElementById('site-nav');
  if (!nav) return;

  const apply = () => {
    const h = nav.offsetHeight || 64; // fallback if not rendered yet
    document.documentElement.style.setProperty('--nav-h', `${h}px`);
  };

  // run now and keep it fresh on resize or font swap
  document.addEventListener('DOMContentLoaded', apply);
  window.addEventListener('resize', apply, { passive: true });

  // fonts can shift height after load
  window.addEventListener('load', () => setTimeout(apply, 0), { once: true });
  apply();
})();
});