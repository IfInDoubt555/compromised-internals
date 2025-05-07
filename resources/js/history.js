import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';

let historyData = null;
let historyContent = null;
let currentDecade = 1960;
let activeTab = "events";

function updateDecadeTheme(decade) {
  const wrapper = document.getElementById("theme-wrapper");
  wrapper?.classList.forEach(cls => {
    if (cls.startsWith("decade-")) wrapper.classList.remove(cls);
  });
  wrapper?.classList.add(`decade-${decade}`);
}

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const startDecade = urlParams.get('decade') ? parseInt(urlParams.get('decade')) : 1960;
  const startTab = urlParams.get('tab') || "events";

  const slider = document.getElementById("slider");
  const selectedTitle = document.getElementById("selected-decade-title");
  const viewButton = document.getElementById("view-button");
  historyContent = document.getElementById("history-content");

  if (viewButton) {
    viewButton.disabled = true;
    viewButton.innerText = 'Loading...';
  }

  fetch("/data/rally-history.json?version=" + Date.now())
    .then(res => res.json())
    .then(data => {
      historyData = data;

      noUiSlider.create(slider, {
        start: [startDecade],
        step: 10,
        range: { min: 1960, max: 2025 },
        tooltips: false,
        format: {
          to: value => Math.round(value),
          from: value => Number(value)
        }
      });

      currentDecade = startDecade;
      activeTab = startTab;
      updateDecadeTheme(currentDecade);

      slider.noUiSlider.on("update", (values, handle) => {
        const key = Math.floor(values[handle] / 10) * 10;
        selectedTitle.textContent = "Selected: " + key;

        if (key !== currentDecade) {
          currentDecade = key;
          updateDecadeTheme(currentDecade);
          loadHistoryContent(activeTab, currentDecade);
        }
      });

      document.querySelectorAll(".tab-btn").forEach(btn => {
        btn.addEventListener("click", function () {
          document.querySelectorAll(".tab-btn").forEach(b => {
            b.classList.remove("bg-blue-600", "text-white");
            b.classList.add("bg-gray-200");
          });
          this.classList.remove("bg-gray-200");
          this.classList.add("bg-blue-600", "text-white");

          activeTab = this.dataset.tab;
          loadHistoryContent(activeTab, currentDecade);
        });
      });

      slider.noUiSlider.set(startDecade);
      loadHistoryContent(activeTab, currentDecade);

      if (viewButton) {
        viewButton.disabled = false;
        viewButton.innerText = 'View History';
        viewButton.addEventListener("click", () => {
          if (!historyData) {
            return alert("History data is still loading. Please wait.");
          }
          const raw = slider.noUiSlider.get();
          const key = Math.floor(raw / 10) * 10;
          currentDecade = key;
          updateDecadeTheme(currentDecade);
          loadHistoryContent(activeTab, currentDecade);
        });
      }
    })
    .catch(err => {
      console.error("Failed to load JSON:", err);
      if (viewButton) viewButton.innerText = 'Error loading history!';
    });
});

function loadHistoryContent(tab, decade) {
  historyContent.innerHTML = "";
  const decadeData = historyData?.[decade];

  if (!decadeData) {
    historyContent.innerHTML = `<p class="text-center text-gray-500">No data for the ${decade}s.</p>`;
    return;
  }

  const items = decadeData[tab] || [];

  if (!items.length) {
    historyContent.innerHTML = `<p class="text-center text-gray-500">No ${tab} available for the ${decade}s.</p>`;
    return;
  }

  const grid = document.createElement("div");
  grid.classList.add("grid", "grid-cols-1", "md:grid-cols-2", "lg:grid-cols-4", "gap-6");

  items.forEach(item => {
    const title = item.title || item.name || "Untitled";
    const description = item.bio || item.description || "No summary available.";
    const imageUrl = item.image
      ? `<img src="${item.image}" alt="${title}" class="w-full h-95 object-cover mb-4 rounded">`
      : `<img src="/images/placeholder.png" alt="No Image" class="w-full h-70 object-cover mb-4 rounded opacity-50">`;

    const link = `/history/${tab}/${decade}/${item.id}`;

    const card = document.createElement("div");
    card.classList.add("card", "bg-white", "rounded-lg", "shadow-md", "overflow-hidden", "flex", "flex-col", "items-center", "p-4");
    card.innerHTML = `
      ${imageUrl}
      <h2 class="text-xl font-bold mb-2 text-center">${title}</h2>
      <p class="text-gray-600 mb-4 text-center">${description}</p>
      <a href="${link}" class="mt-auto text-blue-600 hover:underline">Read More</a>
    `;

    grid.appendChild(card);
  });

  historyContent.appendChild(grid);
}