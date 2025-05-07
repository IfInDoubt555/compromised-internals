@extends('layouts.app')

@section('content')
  <div id="top"></div>

  <div id="theme-wrapper" class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto text-center mb-8">
      <h1 class="text-4xl font-bold mb-2">🏁 Rally History Archive</h1>
      <p class="text-lg text-gray-700">
        Explore rally legends, iconic cars, and pivotal events from the 1960s to today.
        Use the slider below to pick a decade and switch between timelines.
      </p>
    </div>

    <div id="slider" class="w-full sm:w-2/3 max-w-xl mx-auto my-6"></div>
    <div id="selected-decade-title" class="text-xl font-semibold text-center mb-4"></div>

    <div class="flex justify-center gap-4 mb-6">
      <button class="tab-btn px-4 py-2 bg-blue-600 text-white rounded hover:bg-gray-400" data-tab="events">
        Events
      </button>
      <button class="tab-btn px-4 py-2 bg-gray-200 rounded hover:bg-gray-400" data-tab="cars">
        Cars
      </button>
      <button class="tab-btn px-4 py-2 bg-gray-200 rounded hover:bg-gray-400" data-tab="drivers">
        Drivers
      </button>
    </div>

    <div id="history-content" class="mt-10 space-y-6"></div>

    <noscript>
      <p class="text-center text-red-600 font-medium mt-8">
        ⚠️ JavaScript is required to view the interactive timeline. Please enable it in your browser.
      </p>
    </noscript>
  </div>

  <a href="#top"
     id="back-to-top"
     class="hidden fixed bottom-28 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none"
     aria-label="Back to top">
    <!-- up arrow -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M5 15l7-7 7 7" />
    </svg>
  </a>

  <a href="#"
     id="scroll-middle"
     class="hidden fixed bottom-16 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none"
     aria-label="Scroll to middle">
    <!-- up‐and‐down arrow -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M5 15l7-7 7 7" />
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M5 9l7 7 7-7" />
    </svg>
  </a>

  <a href="#"
     id="scroll-bottom"
     class="hidden fixed bottom-4 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none"
     aria-label="Scroll to bottom">
    <!-- down arrow (rotated) -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform rotate-180" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M5 15l7-7 7 7" />
    </svg>
  </a>

  <div id="back-to-top-tooltip"
       class="pointer-events-none fixed bottom-32 right-4 bg-gray-800 text-white text-sm px-3 py-1 rounded opacity-0 transition-opacity duration-300">
    Click me to return to the top of the page.
  </div>
@endsection

@push('scripts')
  @vite('resources/js/history.js')
@endpush
