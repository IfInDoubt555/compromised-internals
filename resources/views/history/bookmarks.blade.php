{{-- resources/views/history/bookmarks.blade.php --}}
@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally History Archive | Decades of Events, Drivers & Cars – Compromised Internals',
        'description' => 'Dive into rally history: explore legendary events, iconic cars, and drivers from the 1960s through today in our interactive archive, organized by decade.',
        'url'         => url()->current(),
        'image'       => asset('images/history-og.png'),
    ];

    $ld = [
        '@context' => 'https://schema.org',
        '@type'    => 'CollectionPage',
        'url'      => $seo['url'],
        'name'     => 'Rally History Archive',
        'description' => $seo['description'],
        'isPartOf' => [
            '@type' => 'WebSite',
            'name'  => 'Compromised Internals',
            'url'   => url('/'),
        ],
    ];
@endphp

@push('head')
    {{-- Canonical + robots --}}
    <link rel="canonical" href="{{ $seo['url'] }}">
    <meta name="robots" content="index,follow">

    {{-- Basic Meta --}}
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Compromised Internals">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['url'] }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    {{-- Structured Data --}}
    <script type="application/ld+json">
        @json($ld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
    </script>
@endpush

@section('content')
<div id="top"></div>

<div
  id="history-root"
  class="min-h-screen history-body overflow-x-hidden decade-{{ $themeDecade }}"
  data-decade="{{ $themeDecade }}"
  data-tab="{{ $tab }}"
  x-data="historyDrawerOnly('{{ $tab }}', '{{ $decade }}', '{{ $year ?? '' }}')"
  :class="{ 'drawer-open': drawerOpen }"
  x-init="init()"
>
  {{-- Mobile header --}}
  <div class="block md:hidden flex items-center justify-center px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-center">
      Rally History Archive
    </h1>
  </div>

  {{-- MOBILE pull-tab + hint --}}
  <div class="md:hidden fixed left-0 top-1/2 -translate-y-1/2 z-40">
    <button
      type="button"
      class="history-pull-tab"
      @click="drawerOpen = true; markHintSeen()"
      aria-label="Open browse drawer"
    >▎</button>

    <div
      x-show="showHint"
      x-transition.opacity
      class="absolute left-[36px] top-1/2 -translate-y-1/2 bg-black/80 text-white text-xs rounded-lg px-3 py-2 shadow-md"
      style="pointer-events:none"
    >
      Tap for more options <span class="ml-1 opacity-70">→</span>
      <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-0 h-0
                  border-y-8 border-y-transparent border-r-8 border-r-black/80"></div>
    </div>
  </div>

  {{-- MOBILE drawer --}}
  <div class="md:hidden fixed inset-0 z-50"
       x-show="drawerOpen"
       x-transition.opacity
       @click.self="drawerOpen = false">
    <div class="history-drawer"
         x-show="drawerOpen"
         x-transition
         @keydown.escape.window="drawerOpen=false">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Browse</h3>
        <button class="text-sm underline" @click="drawerOpen=false">Close</button>
      </div>

      {{-- Tabs --}}
      <div class="flex gap-2 mb-3">
        @foreach(['events' => 'Events', 'cars' => 'Cars', 'drivers' => 'Drivers'] as $key => $label)
          <a href="{{ route('history.index', ['decade'=>$decade, 'tab'=>$key]) }}"
             class="history-chip px-3 py-1 rounded-full text-sm {{ $tab === $key ? 'ring-1 ring-black/10' : 'hover:bg-white/80' }}"
             @click="drawerOpen=false">
            {{ $label }}
          </a>
        @endforeach
      </div>

      {{-- Decades --}}
      <p class="text-xs uppercase tracking-wide text-gray-600 mb-1">Decades</p>
      <ul class="grid grid-cols-3 gap-2 mb-4">
        @foreach($decades as $d)
          @php
            $params = ['decade'=>$d, 'tab'=>$tab];
            if ($tab === 'events' && $year) { $params['year'] = $year; }
          @endphp
          <li>
            <a href="{{ route('history.index', $params) }}"
               class="history-chip block text-center px-2 py-1 rounded {{ $d===$decade ? 'ring-1 ring-black/20' : '' }}"
               @click="drawerOpen=false">
              {{ $d }}
            </a>
          </li>
        @endforeach
      </ul>

      {{-- Years (events only) --}}
      @if($tab === 'events' && !empty($years))
        <p class="text-xs uppercase tracking-wide text-gray-600 mb-1">Years</p>
        <div class="flex flex-wrap gap-2">
          @foreach($years as $y)
            <a href="{{ route('history.index', ['decade'=>$decade, 'tab'=>'events', 'year'=>$y]) }}"
               class="history-chip px-2 py-1 rounded text-sm {{ (string)$y === (string)($year ?? '') ? 'ring-1 ring-black/20' : '' }}"
               @click="drawerOpen=false">
              {{ $y }}
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- DESKTOP header --}}
  <header class="hidden md:block max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Rally History Archive</h1>
    <div class="mt-4 flex gap-2">
      @foreach(['events' => 'Events', 'cars' => 'Cars', 'drivers' => 'Drivers'] as $key => $label)
        <a href="{{ route('history.index', ['decade'=>$decade, 'tab'=>$key]) }}"
           class="history-chip px-3 py-1 rounded-full text-sm hover:bg-white/80 {{ $tab === $key ? 'ring-1 ring-black/10' : '' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>
  </header>

  {{-- DESKTOP grid --}}
  <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-[160px_1fr] gap-6">
    {{-- Decade rail --}}
    <nav class="hidden md:block">
      <ul class="flex md:flex-col gap-2">
        @foreach($decades as $d)
          @php
            $active = $d === $decade;
            $params = ['decade'=>$d, 'tab'=>$tab];
            if ($tab === 'events' && $year) { $params['year'] = $year; }
          @endphp
          <li>
            <a href="{{ route('history.index', $params) }}"
               class="history-decade-tab block px-4 py-2 rounded-r-lg transition {{ $active ? 'is-active' : 'hover:translate-x-[2px]' }}">
              <span class="inline-block -skew-x-6">{{ $d }}</span>
            </a>
          </li>
        @endforeach
      </ul>
    </nav>

    {{-- Main panel --}}
    <section class="pb-16 md:pb-8">
      @if($tab === 'events' && !empty($years))
        <div class="hidden md:flex flex-wrap gap-2 mb-4">
          @foreach($years as $y)
            <a href="{{ route('history.index', ['decade'=>$decade, 'tab'=>'events', 'year'=>$y]) }}"
               class="history-chip px-3 py-1 rounded-full text-sm {{ (string)$y === (string)($year ?? '') ? 'ring-1 ring-black/20' : 'hover:bg-white/80' }}">
              {{ $y }}
            </a>
          @endforeach
        </div>
      @endif

      <ul class="space-y-3">
        @foreach($items as $e)
          @php
            $display = $e['title'] ?? $e['name'] ?? $e['model'] ?? $e['driver'] ?? 'Untitled';
            $blurb   = $e['bio'] ?? $e['summary'] ?? $e['description'] ?? '';
            $href    = route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $e['id']]);
          @endphp

          <li class="history-card p-4 md:p-5 transition hover:bg-white/90">
            <a href="{{ $href }}" class="history-link history-title text-lg md:text-xl hover:underline">
              {{ $display }}
            </a>
            @if($blurb !== '')
              <p class="history-blurb mt-1 text-sm md:text-base">{{ $blurb }}</p>
              <a href="{{ $href }}" class="mt-2 inline-flex items-center text-blue-700 hover:underline text-sm">
                Read More
              </a>
            @endif
          </li>
        @endforeach
      </ul>
    </section>
  </div>
</div>

@push('after-body')
  {{-- Lazy-load scroll controls only on history pages --}}
  <x-scroll-controls />
@endpush

@push('scripts')
<script>
function historyDrawerOnly(tab, decade, year) {
  const KEY = 'historyDrawerHintSeen:v1';
  return {
    tab, decade, year,
    drawerOpen: false,
    showHint: false,
    hintTimer: null,
    init() {
      try {
        const seen = localStorage.getItem(KEY);
        if (!seen) {
          this.showHint = true;
          this.hintTimer = setTimeout(() => this.markHintSeen(), 3000);
        }
      } catch (_) {}
    },
    markHintSeen() {
      if (this.hintTimer) clearTimeout(this.hintTimer);
      this.showHint = false;
      try { localStorage.setItem(KEY, '1'); } catch (_) {}
    }
  }
}
</script>
@endpush

@push('scripts')
  @vite('resources/js/history.js')
@endpush