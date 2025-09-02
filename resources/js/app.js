import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';


import './stages';
import initScrollControls from './scrollControls';
import initCalendar from './calendar';

window.Alpine = Alpine;          // expose globally for console/tests
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

document.addEventListener('DOMContentLoaded', () => {
  initScrollControls();
  initCalendar('calendar');
});