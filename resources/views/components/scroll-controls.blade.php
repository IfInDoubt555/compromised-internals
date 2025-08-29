{{-- resources/views/components/scroll-controls.blade.php --}}
<a href="#top" id="back-to-top"
   class="hidden scroll-btn scroll-top
          fixed right-4 bottom-24 md:flex items-center justify-center
          w-11 h-11 rounded-full transition
          bg-white text-gray-700 shadow ring-1 ring-black/5 hover:bg-gray-50
          dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-800"
   aria-label="Back to top">
  {{-- ↑ icon --}}
  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
  </svg>
</a>

<a href="#" id="scroll-middle"
   class="hidden scroll-btn scroll-middle
          fixed right-4 bottom-14 md:flex items-center justify-center
          w-11 h-11 rounded-full transition
          bg-white text-gray-700 shadow ring-1 ring-black/5 hover:bg-gray-50
          dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-800"
   aria-label="Scroll to middle">
  {{-- ↕ icon --}}
  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l5 5 5-5M7 8l5-5 5 5"/>
  </svg>
</a>

<a href="#" id="scroll-bottom"
   class="hidden scroll-btn scroll-bottom
          fixed right-4 bottom-4 md:flex items-center justify-center
          w-11 h-11 rounded-full transition
          bg-white text-gray-700 shadow ring-1 ring-black/5 hover:bg-gray-50
          dark:bg-stone-900/80 dark:text-stone-100 dark:ring-white/10 dark:hover:bg-stone-800"
   aria-label="Scroll to bottom">
  {{-- ↓ icon --}}
  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
  </svg>
</a>