// resources/js/history.js (slider removed)
import initScrollControls from './scrollControls';

let currentDecade = 1960;
let activeTab = 'events';
let yearFilter = null;

const EL = {
  wrapper: null,
  content: null,
  yearSelect: null,
  tabButtons: () => document.querySelectorAll('.tab-btn'),
};

function updateDecadeTheme(decade) {
  if (!EL.wrapper) return;
  // remove any previous decade-* class
  [...EL.wrapper.classList].forEach((cls) => {
    if (cls.startsWith('decade-')) EL.wrapper.classList.remove(cls);
  });
  EL.wrapper.classList.add(`decade-${decade}`);
}

function populateYearDropdown(decadeStart) {
  if (!EL.yearSelect) return;
  EL.yearSelect.innerHTML = `<option value="">Full Decade</option>`;
  const end = decadeStart + 9;
  for (let y = decadeStart; y <= end; y++) {
    const opt = document.createElement('option');
    opt.value = y;
    opt.textContent = y;
    EL.yearSelect.appendChild(opt);
  }
}

function setYearFilterFromSelect() {
  if (!EL.yearSelect) return;
  const val = parseInt(EL.yearSelect.value, 10);
  yearFilter = Number.isNaN(val) ? null : val;
}

function showHideYearDropdown() {
  if (!EL.yearSelect) return;
  if (activeTab === 'events') {
    EL.yearSelect.classList.remove('hidden');
    populateYearDropdown(currentDecade);
  } else {
    EL.yearSelect.classList.add('hidden');
    EL.yearSelect.value = '';
    yearFilter = null;
  }
}

function wireYearDropdown() {
  if (!EL.yearSelect) return;
  EL.yearSelect.addEventListener('change', () => {
    setYearFilterFromSelect();
    loadHistoryContent(activeTab, currentDecade);
  });
}

function wireTabs() {
  EL.tabButtons().forEach((btn) => {
    btn.addEventListener('click', () => {
      // visual state
      EL.tabButtons().forEach((b) => {
        b.classList.remove('bg-blue-600', 'text-white');
        b.classList.add('bg-gray-300', 'text-black');
      });
      btn.classList.remove('bg-gray-300', 'text-black');
      btn.classList.add('bg-blue-600', 'text-white');

      // data state
      activeTab = btn.dataset.tab || 'events';
      showHideYearDropdown();
      loadHistoryContent(activeTab, currentDecade);
    });
  });
}

function readUrlState() {
  const qs = new URLSearchParams(window.location.search);

  // 1) From route /history/{tab}/{decade}/{id}
  const parts = window.location.pathname.split('/').filter(Boolean);
  let decadeFromPath = null;
  let tabFromPath = null;
  if (parts[0] === 'history' && ['events','cars','drivers'].includes(parts[1] || '')) {
    tabFromPath = parts[1];
    const m = /^(\d{4})s?$/.exec(parts[2] || '');
    if (m) decadeFromPath = parseInt(m[1], 10);
  }

  // 2) From server-rendered data attributes on the wrapper
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
  const startYear = qs.has('year') ? parseInt(qs.get('year'), 10) : null;

  currentDecade = Number.isNaN(startDecade) ? 1960 : startDecade;
  activeTab = startTab;

  if (EL.yearSelect && !Number.isNaN(startYear) && activeTab === 'events') {
    populateYearDropdown(currentDecade);
    EL.yearSelect.value = String(startYear);
    setYearFilterFromSelect();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Always enable scroll assist
  initScrollControls();

  // Element refs
  EL.wrapper = document.getElementById('theme-wrapper');
  EL.content = document.getElementById('history-content');
  EL.yearSelect = document.getElementById('year-filter');

  // Read initial state from URL
  readUrlState();

  // Apply theme and UI state
  updateDecadeTheme(currentDecade);
  showHideYearDropdown();

  // Wire controls
  wireYearDropdown();
  wireTabs();

  // Initial load
  loadHistoryContent(activeTab, currentDecade);
});

function loadHistoryContent(tab, decade) {
  if (!EL.content) return;
  EL.content.innerHTML = `<p class="text-center text-gray-500">Loading ${tab} for the ${decade}s...</p>`;

  const path = `/data/${tab}-${decade}s.json?version=${Date.now()}`;

  fetch(path)
    .then((res) => res.json())
    .then((items) => {
      let list = Array.isArray(items) ? items : [];
      if (tab === 'events' && yearFilter) {
        list = list.filter((item) => item.year === yearFilter);
      }

      if (!list.length) {
        const msg =
          tab === 'events' && yearFilter
            ? `No ${tab} entries found for year ${yearFilter}.`
            : `No ${tab} data available for the ${decade}s.`;
        EL.content.innerHTML = `<p class="text-center text-gray-500">${msg}</p>`;
        return;
      }

      // Build grid
      EL.content.innerHTML = '';
      const grid = document.createElement('div');
      grid.classList.add(
        'grid',
        'grid-cols-1',
        'sm:grid-cols-2',
        'lg:grid-cols-3',
        'xl:grid-cols-4',
        'gap-4'
      );

      list.forEach((item) => {
        const title = item.title || item.name || 'Untitled';
        const description =
          item.bio || item.summary || item.description || 'No summary available.';
        const link = `/history/${tab}/${decade}/${item.id}`;

        const card = document.createElement('div');
        card.classList.add(
          'card',
          'bg-white',
          'rounded-lg',
          'shadow-md',
          'overflow-hidden',
          'flex',
          'flex-col',
          'items-center',
          'p-4'
        );

        const h2 = document.createElement('h2');
        h2.className = 'text-xl font-bold mb-2 text-center';
        h2.textContent = title;

        const p = document.createElement('p');
        p.className = 'text-gray-600 mb-4 text-center';
        p.textContent = description;

        const a = document.createElement('a');
        a.href = link;
        a.className = 'mt-auto text-blue-600 hover:underline';
        a.textContent = 'Read More';

        card.append(h2, p, a);
        grid.appendChild(card);
      });

      EL.content.appendChild(grid);
    })
    .catch((err) => {
      console.error(`Failed to load ${tab} for ${decade}:`, err);
      EL.content.innerHTML = `<p class="text-center text-red-600">Failed to load ${tab} data for the ${decade}s.</p>`;
    });
}