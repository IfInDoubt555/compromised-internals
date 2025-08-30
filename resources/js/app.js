import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

import './stages';
import initScrollControls from './scrollControls';
import initCalendar from './calendar';

window.Alpine = Alpine;          // expose globally for console/tests
Alpine.plugin(intersect);

document.addEventListener('alpine:init', () => {
  Alpine.store('theme', {
    dark: (() => {
      try { return localStorage.getItem('ci-theme') === 'dark'; } catch { return false; }
    })(),
    toggle() {
      this.dark = !this.dark;
      try { localStorage.setItem('ci-theme', this.dark ? 'dark' : 'light'); } catch {}
      document.documentElement.classList.toggle('dark', this.dark);
      console.log('[theme] dark:', this.dark);
    }
  });

  // apply immediately after Alpine boots
  document.documentElement.classList.toggle('dark', Alpine.store('theme').dark);
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
  initScrollControls();
  initCalendar('calendar');
});