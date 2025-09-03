// resources/js/history.js
import initScrollControls from './scrollControls';

let currentDecade = 1960;
let activeTab = 'events';

const EL = {
  wrapper: null,
  yearSelect: null,
  tabButtons: () => document.querySelectorAll('.tab-btn'),
};

function updateDecadeTheme(decade) {
  if (!EL.wrapper) return;
  // Remove any previous decade-* class
  for (const cls of [...EL.wrapper.classList]) {
    if (cls.startsWith('decade-')) EL.wrapper.classList.remove(cls);
  }
  EL.wrapper.classList.add(`decade-${decade}`);
}

function readUrlState() {
  const qs = new URLSearchParams(window.location.search);

  // 1) From route /history/{tab}/{decade}/{id}
  const parts = window.location.pathname.split('/').filter(Boolean);
  let decadeFromPath = null;
  let tabFromPath = null;
  if (parts[0] === 'history' && ['events', 'cars', 'drivers'].includes(parts[1] || '')) {
    tabFromPath = parts[1];
    const m = /^(\d{4})s?$/.exec(parts[2] || '');
    if (m) decadeFromPath = parseInt(m[1], 10);
  }

  // 2) From server-rendered data attributes
  const decadeFromData = EL.wrapper?.dataset.decade
    ? parseInt(EL.wrapper.dataset.decade, 10)
    : null;
  const tabFromData = EL.wrapper?.dataset.tab || null;

  // 3) From query string
  const decadeFromQS = qs.has('decade') ? parseInt(qs.get('decade'), 10) : null;
  const tabFromQS = qs.get('tab');

  // Resolve with sane defaults
  const startDecade = decadeFromPath ?? decadeFromData ?? decadeFromQS ?? 1960;
  const startTab = tabFromPath ?? tabFromData ?? tabFromQS ?? 'events';

  currentDecade = Number.isNaN(startDecade) ? 1960 : startDecade;
  activeTab = startTab;
}

function wireTabs() {
  const btns = EL.tabButtons();
  if (!btns.length) return;

  btns.forEach((btn) => {
    btn.addEventListener('click', () => {
      // simple visual state sync (content remains SSR)
      btns.forEach((b) => {
        b.classList.remove('bg-blue-600', 'text-white');
        b.classList.add('bg-gray-300', 'text-black');
      });
      btn.classList.remove('bg-gray-300', 'text-black');
      btn.classList.add('bg-blue-600', 'text-white');

      activeTab = btn.dataset.tab || 'events';
      // No client re-render; SSR handles content.
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  EL.wrapper = document.getElementById('history-root');
  EL.yearSelect = document.getElementById('year-filter'); // optional; SSR chips handle year

  readUrlState();
  updateDecadeTheme(currentDecade);
  wireTabs();
});