import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
import initScrollControls from './scrollControls';

let currentDecade = 1960;
let activeTab = "events";
let historyContent = null;

function updateDecadeTheme(decade) {
  const wrapper = document.getElementById("theme-wrapper");
  if (!wrapper) return;
  wrapper.classList.forEach(cls => {
    if (cls.startsWith("decade-")) wrapper.classList.remove(cls);
  });
  wrapper.classList.add(`decade-${decade}`);
}

document.addEventListener("DOMContentLoaded", () => {
  initScrollControls();

  const urlParams = new URLSearchParams(window.location.search);
  const startDecade = urlParams.has('decade') ? parseInt(urlParams.get('decade'), 10) : 1960;
  const startTab = urlParams.get('tab') || "events";
  const slider = document.getElementById("slider");
  const selectedTitle = document.getElementById("selected-decade-title");
  const viewButton = document.getElementById("view-button");
  historyContent = document.getElementById("history-content");

  if (viewButton) {
    viewButton.disabled = true;
    viewButton.innerText = "Loading...";
  }

  noUiSlider.create(slider, {
    start: [startDecade],
    step: 10,
    range: { min: 1960, max: 2020 },
    tooltips: false,
    format: {
      to: v => Math.round(v),
      from: v => Number(v)
    }
  });

  currentDecade = startDecade;
  activeTab = startTab;
  updateDecadeTheme(currentDecade);

  slider.noUiSlider.on("update", (values, handle) => {
    const decadeKey = Math.floor(values[handle] / 10) * 10;
    selectedTitle.textContent = `Selected: ${decadeKey}`;
    if (decadeKey !== currentDecade) {
      currentDecade = decadeKey;
      updateDecadeTheme(currentDecade);
      loadHistoryContent(activeTab, currentDecade);
    }
  });

  document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".tab-btn").forEach(b => {
        b.classList.remove("bg-blue-600", "text-white");
        b.classList.add("bg-gray-200");
      });
      btn.classList.remove("bg-gray-200");
      btn.classList.add("bg-blue-600", "text-white");

      activeTab = btn.dataset.tab;
      loadHistoryContent(activeTab, currentDecade);
    });
  });

  // Initial load
  slider.noUiSlider.set(startDecade);
  selectedTitle.textContent = `Selected: ${startDecade}`;
  loadHistoryContent(activeTab, currentDecade);

  if (viewButton) {
    viewButton.disabled = false;
    viewButton.innerText = "View History";
    viewButton.addEventListener("click", () => {
      const raw = slider.noUiSlider.get();
      const decade = Math.floor(raw / 10) * 10;
      currentDecade = decade;
      updateDecadeTheme(currentDecade);
      loadHistoryContent(activeTab, currentDecade);
    });
  }
});

function loadHistoryContent(tab, decade) {
  if (!historyContent) return;
  historyContent.innerHTML = `<p class="text-center text-gray-500">Loading ${tab} for the ${decade}s...</p>`;

  const path = `/data/${tab}-${decade}s.json?version=${new Date().getTime()}`;

  fetch(path)
    .then(res => res.json())
    .then(items => {
      if (!Array.isArray(items) || items.length === 0) {
        historyContent.innerHTML = `<p class="text-center text-gray-500">No ${tab} data available for the ${decade}s.</p>`;
        return;
      }

      historyContent.innerHTML = "";

      const grid = document.createElement("div");
      grid.classList.add("grid", "grid-cols-1", "sm:grid-cols-2", "lg:grid-cols-3", "xl:grid-cols-4", "gap-4");

      items.forEach(item => {
        const title = item.title || item.name || "Untitled";
        const description = item.bio || "No summary available.";
        const link = `/history/${tab}/${decade}/${item.id}`;

        const card = document.createElement("div");
        card.classList.add(
          "card", "bg-white", "rounded-lg", "shadow-md",
          "overflow-hidden", "flex", "flex-col", "items-center", "p-4"
        );

        // Safe DOM-based construction to preserve img.onerror

        const h2 = document.createElement("h2");
        h2.className = "text-xl font-bold mb-2 text-center";
        h2.textContent = title;
        card.appendChild(h2);

        const p = document.createElement("p");
        p.className = "text-gray-600 mb-4 text-center";
        p.textContent = description;
        card.appendChild(p);

        const a = document.createElement("a");
        a.href = link;
        a.className = "mt-auto text-blue-600 hover:underline";
        a.textContent = "Read More";
        card.appendChild(a);

        grid.appendChild(card);
      });

      historyContent.appendChild(grid);
    })
    .catch(err => {
      console.error(`Failed to load ${tab} for ${decade}:`, err);
      historyContent.innerHTML = `<p class="text-center text-red-600">Failed to load ${tab} data for the ${decade}s.</p>`;
    });
}
