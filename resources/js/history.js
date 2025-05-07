// resources/js/history.js

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
import initScrollControls from './scrollControls';

// state
let historyData = null;
let historyContent = null;
let currentDecade = 1960;
let activeTab = "events";

// apply the colored‐theme class for the decade
function updateDecadeTheme(decade) {
  const wrapper = document.getElementById("theme-wrapper");
  if (!wrapper) return;
  // remove any existing decade-*
  wrapper.classList.forEach(cls => {
    if (cls.startsWith("decade-")) wrapper.classList.remove(cls);
  });
  wrapper.classList.add(`decade-${decade}`);
}

document.addEventListener("DOMContentLoaded", () => {
  // initialize scroll buttons (Top/Middle/Bottom + tooltip)
  initScrollControls();

  // cache DOM elements
  const urlParams      = new URLSearchParams(window.location.search);
  const startDecade    = urlParams.has('decade') ? parseInt(urlParams.get('decade'), 10) : 1960;
  const startTab       = urlParams.get('tab') || "events";
  const slider         = document.getElementById("slider");
  const selectedTitle  = document.getElementById("selected-decade-title");
  const viewButton     = document.getElementById("view-button");
  historyContent       = document.getElementById("history-content");

  // disable the view button until data is ready
  if (viewButton) {
    viewButton.disabled = true;
    viewButton.innerText = "Loading...";
  }

  // fetch the timeline JSON
  fetch(`/data/rally-history.json?version=${Date.now()}`)
    .then(res => res.json())
    .then(data => {
      historyData = data;

      // build the decade slider
      noUiSlider.create(slider, {
        start: [startDecade],
        step: 10,
        range: { min: 1960, max: 2025 },
        tooltips: false,
        format: {
          to:   v => Math.round(v),
          from: v => Number(v)
        }
      });

      currentDecade = startDecade;
      activeTab     = startTab;
      updateDecadeTheme(currentDecade);

      // when the user moves the slider…
      slider.noUiSlider.on("update", (values, handle) => {
        const decadeKey = Math.floor(values[handle] / 10) * 10;
        selectedTitle.textContent = `Selected: ${decadeKey}`;

        if (decadeKey !== currentDecade) {
          currentDecade = decadeKey;
          updateDecadeTheme(currentDecade);
          loadHistoryContent(activeTab, currentDecade);
        }
      });

      // tab button clicks
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

      // set initial slider position & load content
      slider.noUiSlider.set(startDecade);
      loadHistoryContent(activeTab, currentDecade);

      // re-enable the view button
      if (viewButton) {
        viewButton.disabled = false;
        viewButton.innerText = "View History";
        viewButton.addEventListener("click", () => {
          if (!historyData) {
            return alert("History data is still loading. Please wait.");
          }
          const raw    = slider.noUiSlider.get();
          const decade = Math.floor(raw / 10) * 10;
          currentDecade = decade;
          updateDecadeTheme(currentDecade);
          loadHistoryContent(activeTab, currentDecade);
        });
      }
    })
    .catch(err => {
      console.error("Failed to load JSON:", err);
      if (viewButton) viewButton.innerText = "Error loading history!";
    });
});

/**
 * Renders the grid of event/car/driver cards for a given decade.
 */
function loadHistoryContent(tab, decade) {
  if (!historyContent) return;
  historyContent.innerHTML = "";

  const decadeData = historyData?.[decade];
  if (!decadeData) {
    historyContent.innerHTML = `<p class="text-center text-gray-500">No data for the ${decade}s.</p>`;
    return;
  }

  const items = decadeData[tab] || [];
  if (items.length === 0) {
    historyContent.innerHTML = `<p class="text-center text-gray-500">No ${tab} available for the ${decade}s.</p>`;
    return;
  }

  const grid = document.createElement("div");
  grid.classList.add("grid", "grid-cols-1", "md:grid-cols-2", "lg:grid-cols-4", "gap-6");

  items.forEach(item => {
    const title       = item.title || item.name || "Untitled";
    const description = item.bio   || item.description || "No summary available.";
    const imgSrc      = item.image
      ? item.image
      : "/images/placeholder.png";
    const imgClass    = item.image
      ? "h-80 object-cover mb-4 rounded"
      : "h-80 object-cover mb-4 rounded opacity-50";
    const link        = `/history/${tab}/${decade}/${item.id}`;

    const card = document.createElement("div");
    card.classList.add("card", "bg-white", "rounded-lg", "shadow-md", "overflow-hidden", "flex", "flex-col", "items-center", "p-4");

    card.innerHTML = `
      <img src="${imgSrc}" alt="${title}" class="w-full ${imgClass}">
      <h2 class="text-xl font-bold mb-2 text-center">${title}</h2>
      <p class="text-gray-600 mb-4 text-center">${description}</p>
      <a href="${link}" class="mt-auto text-blue-600 hover:underline">Read More</a>
    `;

    grid.appendChild(card);
  });

  historyContent.appendChild(grid);
}