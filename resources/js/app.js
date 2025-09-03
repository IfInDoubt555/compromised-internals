// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';

window.Alpine = Alpine;
Alpine.plugin(intersect);
Alpine.plugin(focus);

const prefersDark = () =>
  window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

document.addEventListener('alpine:init', () => {
  Alpine.store('theme', {
    mode:
      (window.CI_THEME && typeof window.CI_THEME.getMode === 'function'
        ? window.CI_THEME.getMode()
        : null) ||
      localStorage.getItem('ci-theme') ||
      'system',

    get dark() {
      return this.mode === 'dark' || (this.mode === 'system' && prefersDark());
    },

    setMode(mode) {
      this.mode = mode; // reflect in Alpine
      if (window.CI_THEME && typeof window.CI_THEME.setMode === 'function') {
        window.CI_THEME.setMode(mode); // apply + persist + broadcast
      }
    },

    choose(mode) { this.setMode(mode); },
    toggle() {
      this.setMode(
        this.mode === 'light' ? 'dark' :
        this.mode === 'dark'  ? 'system' : 'light'
      );
    },
    cycle() { this.toggle(); }
  });

  document.addEventListener('ci-theme:changed', (e) => {
    Alpine.store('theme').mode = e.detail.mode;
  });
});

Alpine.start();

/* -------- Lazy/conditional boot -------- */
const isMobile = () =>
  window.matchMedia && window.matchMedia('(max-width: 768px)').matches;
const idle = (fn) =>
  'requestIdleCallback' in window ? window.requestIdleCallback(fn) : setTimeout(fn, 200);

document.addEventListener('DOMContentLoaded', () => {
  // Scroll controls
  if (document.querySelector('[data-has-scroll-controls]')) {
    const boot = () =>
      import('./scrollControls').then((m) => {
        const fn = m.default || m.initScrollControls;
        if (typeof fn === 'function') fn();
      });
    isMobile() ? idle(boot) : boot();
  }

  // Calendar
  if (document.querySelector('[data-calendar]')) {
    const boot = () =>
      import('./calendar').then((m) => {
        const initCalendar = m.default || m.initCalendar;
        if (typeof initCalendar === 'function') initCalendar('calendar');
      });
    isMobile() ? idle(boot) : boot();
  }

  // Stages (side effects)
  if (document.querySelector('[data-stages]')) {
    const boot = () => import('./stages');
    isMobile() ? idle(boot) : boot();
  }

  /* ---- Keep CSS var --nav-h in sync with the fixed nav height ---- */
  const nav = document.querySelector('[data-sticky-nav]');
  if (nav && 'ResizeObserver' in window) {
    const applyNavHeight = () => {
      const h = Math.ceil(nav.getBoundingClientRect().height || 64);
      document.documentElement.style.setProperty('--nav-h', `${h}px`);
    };
    const ro = new ResizeObserver(applyNavHeight);
    ro.observe(nav);
    window.addEventListener('load', applyNavHeight, { once: true });
    applyNavHeight();
  }
});