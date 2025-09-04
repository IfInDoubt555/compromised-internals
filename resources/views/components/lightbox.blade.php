{{-- Global lightbox modal; include once (e.g., in layouts.app footer) --}}
<div
  x-data
  x-show="$store.lightbox.open"
  x-transition.opacity
  @click.self="$store.lightbox.close()"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/80 p-4 sm:p-6 md:p-8 lg:p-10"
  :class="{ 'flex': $store.lightbox.open }"
  aria-modal="true" role="dialog"
>
  <button
    type="button"
    class="absolute top-4 right-4 inline-flex items-center rounded-full bg-white/90 px-3 py-1 text-sm font-semibold shadow ring-1 ring-black/10 hover:bg-white dark:bg-stone-800/90 dark:text-stone-100 dark:ring-white/10"
    @click="$store.lightbox.close()"
    aria-label="Close image"
  >
    ✕ Close
  </button>

  <figure class="max-h-full max-w-full">
    <img
      class="max-h-[85vh] max-w-[90vw] rounded-xl shadow-2xl ring-1 ring-black/20 dark:ring-white/10 object-contain"
      :src="$store.lightbox.src"
      :alt="$store.lightbox.alt"
      loading="eager"
    />
    <figcaption class="mt-3 text-center text-sm text-white/80" x-text="$store.lightbox.alt"></figcaption>
  </figure>
</div>