<div id="ci-lightbox-root"
     class="fixed inset-0 z-[10000] hidden"
     aria-hidden="true">
  <!-- backdrop -->
  <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity duration-150 opacity-0"
       data-ci="backdrop"></div>

  <!-- stage -->
  <div class="relative z-10 h-full w-full flex items-center justify-center p-4">
    <img id="ci-lightbox-img"
         alt=""
         class="max-w-[94vw] max-h-[92vh] object-contain rounded-xl shadow-2xl ring-1 ring-white/10
                transition-transform duration-150 scale-95 opacity-0 select-none"
         draggable="false" />
    <button type="button"
            class="absolute top-4 right-4 rounded-lg bg-white/10 text-white ring-1 ring-white/20
                   hover:bg-white/20 focus:outline-none px-3 py-2 text-sm"
            data-ci="close"
            aria-label="Close (Esc)">
      ✕
    </button>
  </div>
</div>