// Bootstrap & Alpine
import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import './stages';

Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();

// Global scroll assist
import initScrollControls from './scrollControls';

// Calendar module (safe no-op if #calendar not present)
import initCalendar from './calendar';

document.addEventListener('DOMContentLoaded', () => {
  // Always enable scroll assist site-wide
  initScrollControls();

  // Initialize FullCalendar only on pages that have the container
  initCalendar('calendar');
});