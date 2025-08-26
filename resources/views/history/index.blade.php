@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally History Archive | Compromised Internals',
        'description' => 'Explore rally legends, iconic cars, and pivotal events from the 1960s to today in our interactive history archive.',
        'url'         => url()->current(),
        'image'       => asset('images/history-og.png'),
    ];
@endphp

@push('head')
    <!-- Primary Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}" />

    <!-- Open Graph / Link Preview Tags -->
    <meta property="og:type"        content="website" />
    <meta property="og:site_name"   content="Compromised Internals" />
    <meta property="og:url"         content="{{ $seo['url'] }}" />
    <meta property="og:title"       content="{{ $seo['title'] }}" />
    <meta property="og:description" content="{{ $seo['description'] }}" />
    <meta property="og:image"       content="{{ $seo['image'] }}" />

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:url"         content="{{ $seo['url'] }}">
    <meta name="twitter:title"       content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image"       content="{{ $seo['image'] }}">
@endpush

@section('content')
<div id="top"></div>

<div id="theme-wrapper" class="min-h-screen py-12 px-4">
  <div class="max-w-4xl backdrop-blur-md bg-white/30 dark:bg-white/40 rounded-xl px-6 py-4 shadow-xl mx-auto text-center mb-8">
    <h1 class="text-4xl font-bold mb-2">🏁 Rally History Archive</h1>
    <p class="text-lg text-gray-700">
      Explore rally legends, iconic cars, and pivotal events from the 1960s to today.
      Use the slider below to pick a decade and switch between timelines.
    </p>
  </div>

  <div id="slider" class="w-full sm:w-2/3 max-w-xl mx-auto my-6"></div>
  <div class="max-w-fit mx-auto">
    <div id="selected-decade-title" class="text-xl backdrop-blur-md bg-white/30 dark:bg-white/40 rounded-xl px-6 py-4 shadow-xl font-semibold text-center mb-4">
    </div>
  </div>


  <div class="flex justify-center mt-4 mb-4">
    <select
      id="year-filter"
      class="border px-2 py-1 rounded text-center w-40 hidden">
      <option value="">Full Decade</option>
    </select>
  </div>

  <div class="flex justify-center gap-4 mb-6">
    <button class="tab-btn px-4 py-2 rounded font-semibold transition-colors duration-200 bg-gray-300 text-black hover:bg-blue-400 hover:text-white" data-tab="events">Events</button>
    <button class="tab-btn px-4 py-2 rounded font-semibold transition-colors duration-200 bg-gray-300 text-black hover:bg-blue-400 hover:text-white" data-tab="cars">Cars</button>
    <button class="tab-btn px-4 py-2 rounded font-semibold transition-colors duration-200 bg-gray-300 text-black hover:bg-blue-400 hover:text-white" data-tab="drivers">Drivers</button>
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
   class="hidden fixed bottom-40 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none"
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
   class="hidden fixed bottom-32 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none"
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
   class="hidden fixed bottom-24 right-4 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 focus:outline-none"
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