{{-- resources/views/components/scroll-controls.blade.php --}}
@props([
  // Extra bottom offset in px if something else sits bottom-right (e.g., chat)
  'bottomOffset' => 0,
])

<div
  x-data="scrollControls({ bottomOffset: {{ (int) $bottomOffset }} })"
  x-init="init()"
  class="fixed z-50 pointer-events-none"
  :style="style"
  data-has-scroll-controls
>
  <div class="flex flex-col items-center gap-2 pointer-events-auto select-none">

    {{-- Back to top (visible after some scroll) --}}
    <button
      x-show="showTop"
      x-transition.opacity
      type="button"
      @click="toTop"
      class="h-12 w-12 grid place-items-center rounded-full shadow-lg ring-1 ring-black/10
             bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/60
             text-gray-800 hover:bg-white
             dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-900"
      aria-label="Back to top"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
      </svg>
    </button>

    {{-- Nudge up --}}
    <button
      type="button"
      @click="nudge(-1)"
      class="h-12 w-12 grid place-items-center rounded-full shadow-lg ring-1 ring-black/10
             bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/60
             text-gray-800 hover:bg-white
             dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-900"
      aria-label="Scroll up"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l5-5 5 5"/>
      </svg>
    </button>

    {{-- Nudge down --}}
    <button
      type="button"
      @click="nudge(1)"
      class="h-12 w-12 grid place-items-center rounded-full shadow-lg ring-1 ring-black/10
             bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/60
             text-gray-800 hover:bg-white
             dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-900"
      aria-label="Scroll down"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7 10l5 5 5-5"/>
      </svg>
    </button>

  </div>
</div>

@push('scripts')
<script nonce="@cspNonce">
function scrollControls(opts = {}) {
  return {
    showTop: false,
    style: '',
    bottomOffset: Number(opts.bottomOffset || 0),

    init() {
      // Safe-area aware, bottom-right anchored
      const right = `calc(env(safe-area-inset-right, 0px) + 16px)`;
      const bottom = `calc(env(safe-area-inset-bottom, 0px) + ${16 + this.bottomOffset}px)`;
      this.style = `right:${right}; bottom:${bottom};`;

      const onScroll = () => { this.showTop = window.scrollY > 400; };
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });
    },

    nudge(dir = 1) {
      const amount = Math.max(window.innerHeight * 0.5, 300);
      const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';
      window.scrollBy({ top: dir * amount, behavior });
    },

    toTop() {
      const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';
      window.scrollTo({ top: 0, behavior });
    },
  };
}
</script>
@endpush