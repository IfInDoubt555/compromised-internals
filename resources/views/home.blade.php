@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Compromised Internals | Rally Racing News, History, Calendar & Travel Guides',
        'description' => 'Your rally racing hub: history archive, event calendar, driver & car profiles, plus travel guides to help fans plan trips to WRC, ERC & ARA rallies worldwide.',
        'url'         => url('/'),
        'image'       => asset('images/ci-og.png'),
        'favicon'     => asset('favicon.png'),
    ];

    // JSON-LD WebSite with site search
    $ld = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'url'      => $seo['url'],
        'name'     => 'Compromised Internals',
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => url('/blog') . '?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
@endphp

@push('head')
    {{-- Favicon --}}
    <link rel="icon" href="{{ $seo['favicon'] }}" type="image/png" />

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
    {{-- (Optional) help some scrapers with dimensions if you know them --}}
    {{-- <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630"> --}}

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['url'] }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    {{-- Structured Data --}}
    <script type="application/ld+json" nonce="@cspNonce">
      @json($ld, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)
    </script>
@endpush
@push('after-body')
  {{-- Fixed, page-level background (light/dark) --}}
  <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
    {{-- Light --}}
    <picture class="absolute inset-0 block dark:hidden" aria-hidden="true">
      <source media="(min-width: 1024px)" type="image/avif"
              srcset="{{ asset('images/homepage-banner-light/homepage-banner-light-desktop-1920.avif') }} 1920w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-desktop-2560.avif') }} 2560w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-desktop-3840.avif') }} 3840w"
              sizes="100vw">
      <source media="(max-width: 1023px)" type="image/avif"
              srcset="{{ asset('images/homepage-banner-light/homepage-banner-light-mobile-720.avif') }} 720w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-mobile-1080.avif') }} 1080w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-mobile-2160.avif') }} 2160w"
              sizes="100vw">
      <img
        src="{{ asset('images/homepage-banner-light/homepage-banner-light-desktop-1920.avif') }}"
        alt=""
        class="w-full h-full object-cover will-change-transform"
        loading="eager" fetchpriority="high" decoding="async">
    </picture>

    {{-- Dark --}}
    <picture class="absolute inset-0 hidden dark:block" aria-hidden="true">
      <source media="(min-width: 1024px)" type="image/avif"
              srcset="{{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-1280.avif') }} 1280w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-1920.avif') }} 1920w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-2560.avif') }} 2560w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-3840.avif') }} 3840w"
              sizes="100vw">
      <source media="(max-width: 1023px)" type="image/avif"
              srcset="{{ asset('images/homepage-banner-dark/homepage-banner-dark-mobile-400.avif') }} 400w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-mobile-720.avif') }} 720w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-mobile-1080.avif') }} 1080w"
              sizes="100vw">
      <img
        src="{{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-master.png') }}"
        alt=""
        class="w-full h-full object-cover will-change-transform"
        loading="eager" fetchpriority="high" decoding="async">
    </picture>

    {{-- Contrast wash --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/0 via-black/0 to-black/20
                dark:from-black/20 dark:via-black/30 dark:to-black/50"></div>
  </div>
@endpush

@section('content')
<div
  class="min-h-screen antialiased overflow-x-clip text-stone-900 dark:text-stone-200
         selection:bg-rose-500/30 bg-transparent">
  {{-- ===== HERO + HISTORY/NEXT (scoped banner behind) ===== --}}
  <section id="home-hero" class="relative isolate">

    {{-- CONTENT (centered container) --}}
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 relative z-10">

      {{-- HERO CARD --}}
      <section class="pt-8">
        <div
          class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 p-6 sm:p-8 shadow-xl
                 dark:bg-stone-900/70 dark:ring-white/10">
          <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
              <h1 class="font-orbitron text-3xl sm:text-4xl font-bold tracking-tight">
                Compromised Internals
              </h1>
              <p class="mt-2 text-stone-700 dark:text-stone-300">
                News, history, schedules, and deep-dive profiles for rally fans.
              </p>
            </div>
            <div class="flex items-center gap-3">
              <a href="{{ route('contact') }}"
                 class="inline-flex items-center rounded-lg bg-amber-400/90 text-stone-900 px-3 py-2 text-sm font-semibold hover:bg-amber-400 transition">
                Leave feedback
              </a>
            </div>
          </div>
        </div>
      </section>

      {{-- HISTORY + NEXT RALLIES --}}
      <section class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- LEFT: History features --}}
        <div class="relative lg:col-span-2">
          {{-- Light-only soft fade behind the heading (scoped to left col) --}}
          <div aria-hidden="true"
               class="pointer-events-none absolute inset-x-0 -top-2 h-12
                      rounded-t-2xl bg-gradient-to-b from-white/85 via-white/50 to-transparent
                      dark:from-transparent"></div>
        
          <header class="mb-4">
            <h2 class="text-center font-orbitron tracking-wide text-xl sm:text-2xl lg:text-3xl font-semibold">
              <span class="rounded-xl px-3 py-1 bg-white/80 ring-1 ring-black/5 backdrop-blur-md text-stone-900
                           dark:bg-transparent dark:ring-0 dark:text-stone-100">
                History Features
              </span>
            </h2>
            <p class="mt-1 text-center">
              <span class="text-sm rounded-full px-3 py-1 bg-white/70 ring-1 ring-black/5 backdrop-blur-md text-stone-700
                           dark:bg-transparent dark:ring-0 dark:text-stone-400">
                Iconic events, machines, and drivers that shaped rally
              </span>
            </p>
          </header>
        
          <div class="space-y-6">
            @if($event)
              <article class="rounded-2xl bg-white/90 backdrop-blur shadow p-6 ring-1 ring-black/5
                              dark:bg-stone-800/70 dark:ring-white/10">
                <h3 class="font-orbitron text-2xl font-bold text-center">{{ $event['title'] ?? 'Untitled Event' }}</h3>
                <p class="mt-3 text-center text-stone-700 dark:text-stone-300">{{ $event['bio'] ?? '' }}</p>
                <div class="mt-4 text-center">
                  <a href="{{ route('history.show', ['tab'=>'events','decade'=>$event['decade'],'id'=>$event       ['id']]) }}"
                     class="inline-flex items-center font-semibold text-blue-600 dark:text-rose-300 hover:underline">
                    Read more
                  </a>
                </div>
              </article>
            @endif
        
            @if($car)
              <article class="rounded-2xl bg-white/90 backdrop-blur shadow p-6 ring-1 ring-black/5
                              dark:bg-stone-800/70 dark:ring-white/10">
                <h3 class="font-orbitron text-2xl font-bold text-center">{{ $car['name'] ?? 'Unnamed Car' }}</h3>
                <p class="mt-3 text-center text-stone-700 dark:text-stone-300">{{ $car['bio'] ?? '' }}</p>
                <div class="mt-4 text-center">
                  <a href="{{ route('history.show', ['tab'=>'cars','decade'=>$car['decade'],'id'=>$car       ['id']]) }}"
                     class="inline-flex items-center font-semibold text-blue-600 dark:text-rose-300 hover:underline">
                    Read more
                  </a>
                </div>
              </article>
            @endif
        
            @if($driver)
              <article class="rounded-2xl bg-white/90 backdrop-blur shadow p-6 ring-1 ring-black/5
                              dark:bg-stone-800/70 dark:ring-white/10">
                <h3 class="font-orbitron text-2xl font-bold text-center">{{ $driver['name'] ?? 'Unnamed Driver' }}</h3>
                <p class="mt-3 text-center text-stone-700 dark:text-stone-300">{{ $driver['bio'] ?? '' }}</p>
                <div class="mt-4 text-center">
                  <a href="{{ route('history.show', ['tab'=>'drivers','decade'=>$driver['decade'],       'id'=>$driver['id']]) }}"
                     class="inline-flex items-center font-semibold text-blue-600 dark:text-rose-300 hover:underline">
                    Read more
                  </a>
                </div>
              </article>
            @endif
          </div>
        </div>
        
        {{-- RIGHT: Next rallies --}}
        <aside class="rounded-2xl bg-white/90 backdrop-blur shadow p-5 ring-1 ring-black/5
                       dark:bg-stone-800/70 dark:ring-white/10">
          <h3 class="font-orbitron text-lg font-bold">Next Rallies</h3>
          <ul class="mt-3 divide-y divide-stone-200 dark:divide-stone-600">
            @forelse($nextEvents ?? [] as $e)
              <li class="py-3">
                <div class="text-sm font-semibold">{{ $e->title }}</div>
                <div class="text-xs text-stone-700 dark:text-stone-400">
                  <time datetime="{{ $e->start_date?->toDateString() }}">
                    {{ optional($e->start_date)->format('M j') }}@if($e->end_date) – {{ $e->end_date->format('M j') }}       @endif
                  </time>
                  @if(!empty($e->location)) • {{ $e->location }} @endif
                </div>
                @if(!empty($e->slug))
                  <a href="{{ route('events.show', $e->slug) }}"
                     class="text-xs font-medium text-blue-600 dark:text-rose-300 hover:underline mt-1 inline-block">
                    Event details
                  </a>
                @endif
              </li>
            @empty
              <li class="py-3 text-sm text-stone-700 dark:text-stone-400">No upcoming events found.</li>
            @endforelse
          </ul>
          <a href="{{ route('calendar.index') }}"
             class="mt-3 inline-flex items-center text-sm font-semibold text-blue-600 dark:text-rose-300 hover:underline">
            Open full calendar
          </a>
        </aside>
      </section>

      {{-- Extend banner just a bit below the cards --}}
      <div class="h-8 md:h-10"></div>
    </div>
  </section>
  {{-- ===== /HERO + HISTORY/NEXT ===== --}}

  {{-- Home: Latest From the Blog --}}
  <section id="home-blog"
           class="relative z-10 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 mt-8 mb-16">

    {{-- light-only soft fade so text doesn’t sit directly on the photo --}}
    <div aria-hidden="true"
         class="pointer-events-none absolute inset-x-4 sm:inset-x-6 lg:inset-x-8 -top-4 h-14
                rounded-t-2xl bg-gradient-to-b from-white/85 via-white/50 to-transparent
                dark:from-transparent"></div>

    @if(($latestPosts ?? collect())->count())
      <div class="relative mb-4 sm:mb-6 flex items-center justify-between gap-3">
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-semibold tracking-wide">
          <span class="rounded-xl px-3 py-1
                       bg-white/80 ring-1 ring-black/5 backdrop-blur-md
                       text-stone-900
                       dark:bg-transparent dark:ring-0 dark:text-stone-100">
            Latest from the Blog
          </span>
        </h2>

        <a href="{{ route('blog.index') }}"
           class="hidden sm:inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium
                  bg-white/75 ring-1 ring-black/10 backdrop-blur-md text-stone-900 hover:text-stone-700
                  dark:bg-stone-800/60 dark:ring-white/10 dark:text-sky-300 dark:hover:text-sky-200">
          View all
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
               viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>

      @include('partials.blog-carousel', [
        'posts'    => $latestPosts,
        'variant'  => 'featured',
        'interval' => 6,
      ])

      {{-- Mobile “view all” link under the carousel --}}
      <div class="mt-4 sm:hidden">
        <a href="{{ route('blog.index') }}"
           class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-medium
                  bg-white/75 ring-1 ring-black/10 backdrop-blur-md text-stone-900 hover:text-stone-700
                  dark:bg-stone-800/60 dark:ring-white/10 dark:text-sky-300 dark:hover:text-sky-200">
          View all posts
        </a>
      </div>
    @endif
  </section>
</div>
@endsection