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
    <script type="application/ld+json">
        @json($ld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
    </script>
@endpush

@section('content')
<div
  class="min-h-screen antialiased text-stone-900 dark:text-stone-200
         bg-gradient-to-b from-stone-400 to-stone-500
         dark:from-stone-950 dark:to-stone-900
         selection:bg-rose-500/30">

  {{-- ===== HERO + HISTORY/NEXT (scoped banner behind) ===== --}}
  <section id="home-hero" class="relative isolate overflow-hidden">

    {{-- BACKGROUND (Light) --}}
    <picture class="absolute inset-0 -z-10 block dark:hidden pointer-events-none">
      <source media="(min-width: 1024px)" type="image/webp"
              srcset="{{ asset('images/homepage-banner-light/homepage-banner-light-desktop-1920.webp') }} 1920w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-desktop-2560.webp') }} 2560w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-desktop-3840.webp') }} 3840w"
              sizes="100vw">
      <source media="(max-width: 1023px)" type="image/webp"
              srcset="{{ asset('images/homepage-banner-light/homepage-banner-light-mobile-720.webp') }} 720w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-mobile-1080.webp') }} 1080w,
                      {{ asset('images/homepage-banner-light/homepage-banner-light-mobile-2160.webp') }} 2160w"
              sizes="100vw">
      <img src="{{ asset('images/homepage-banner-light/homepage-banner-light-desktop-1920.webp') }}"
           alt="" class="w-full h-full object-cover">
    </picture>

    {{-- BACKGROUND (Dark) --}}
    <picture class="absolute inset-0 -z-10 hidden dark:block pointer-events-none">
      <source media="(min-width: 1024px)" type="image/webp"
              srcset="{{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-1920.webp') }} 1920w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-2560.webp') }} 2560w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-3840.webp') }} 3840w"
              sizes="100vw">
      <source media="(max-width: 1023px)" type="image/webp"
              srcset="{{ asset('images/homepage-banner-dark/homepage-banner-dark-mobile-720.webp') }} 720w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-mobile-1080.webp') }} 1080w,
                      {{ asset('images/homepage-banner-dark/homepage-banner-dark-mobile-2160.webp') }} 2160w"
              sizes="100vw">
      <img src="{{ asset('images/homepage-banner-dark/homepage-banner-dark-desktop-1920.webp') }}"
           alt="" class="w-full h-full object-cover">
    </picture>

    {{-- Contrast overlay --}}
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-black/0 via-black/0 to-black/20
                dark:from-black/20 dark:via-black/30 dark:to-black/50 pointer-events-none"></div>

    {{-- CONTENT (centered container) --}}
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

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
        <div class="lg:col-span-2">
          <h2 class="text-center font-orbitron text-xl font-bold mb-2 text-stone-800 dark:text-stone-300">
            History Features
          </h2>
          <p class="text-center text-stone-600 dark:text-stone-400 text-sm mb-6">
            Iconic events, machines, and drivers that shaped rally
          </p>

          <div class="space-y-6">
            @if($event)
              <article class="rounded-2xl bg-white/90 backdrop-blur shadow p-6 ring-1 ring-black/5 dark:bg-stone-800/70 dark:ring-white/10">
                <h3 class="font-orbitron text-2xl font-bold text-center">{{ $event['title'] ?? 'Untitled Event' }}</h3>
                <p class="mt-3 text-center text-stone-700 dark:text-stone-300">
                  {{ $event['bio'] ?? 'No description available.' }}
                </p>
                <div class="mt-4 text-center">
                  <a href="{{ route('history.show', ['tab' => 'events', 'decade' => $event['decade'], 'id' => $event['id']]) }}"
                     class="inline-flex items-center font-semibold text-blue-600 dark:text-rose-300 hover:underline">
                    Read more
                  </a>
                </div>
              </article>
            @endif

            @if($car)
              <article class="rounded-2xl bg-white/90 backdrop-blur shadow p-6 ring-1 ring-black/5 dark:bg-stone-800/70 dark:ring-white/10">
                <h3 class="font-orbitron text-2xl font-bold text-center">{{ $car['name'] ?? 'Unnamed Car' }}</h3>
                <p class="mt-3 text-center text-stone-700 dark:text-stone-300">
                  {{ $car['bio'] ?? 'No description available.' }}
                </p>
                <div class="mt-4 text-center">
                  <a href="{{ route('history.show', ['tab' => 'cars', 'decade' => $car['decade'], 'id' => $car['id']]) }}"
                     class="inline-flex items-center font-semibold text-blue-600 dark:text-rose-300 hover:underline">
                    Read more
                  </a>
                </div>
              </article>
            @endif

            @if($driver)
              <article class="rounded-2xl bg-white/90 backdrop-blur shadow p-6 ring-1 ring-black/5 dark:bg-stone-800/70 dark:ring-white/10">
                <h3 class="font-orbitron text-2xl font-bold text-center">{{ $driver['name'] ?? 'Unnamed Driver' }}</h3>
                <p class="mt-3 text-center text-stone-700 dark:text-stone-300">
                  {{ $driver['bio'] ?? 'No description available.' }}
                </p>
                <div class="mt-4 text-center">
                  <a href="{{ route('history.show', ['tab' => 'drivers', 'decade' => $driver['decade'], 'id' => $driver['id']]) }}"
                     class="inline-flex items-center font-semibold text-blue-600 dark:text-rose-300 hover:underline">
                    Read more
                  </a>
                </div>
              </article>
            @endif
          </div>
        </div>

        {{-- RIGHT: Next rallies --}}
        <aside class="rounded-2xl bg-white/90 backdrop-blur shadow p-5 ring-1 ring-black/5 dark:bg-stone-800/70 dark:ring-white/10">
          <h3 class="font-orbitron text-lg font-bold">Next Rallies</h3>
          <ul class="mt-3 divide-y divide-stone-200 dark:divide-stone-600">
            @forelse($nextEvents ?? [] as $e)
              <li class="py-3">
                <div class="text-sm font-semibold">{{ $e->title }}</div>
                <div class="text-xs text-stone-700 dark:text-stone-400">
                  <time datetime="{{ $e->start_date?->toDateString() }}">
                    {{ optional($e->start_date)->format('M j') }}
                    @if($e->end_date) – {{ $e->end_date->format('M j') }} @endif
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

  {{-- ===== BLOG FEATURED + LATEST ===== --}}
  <section class="mt-14 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
    <h2 class="text-center font-orbitron text-2xl font-bold">
      Latest From the Blog
    </h2>

    @php
      $featured = $posts->first();
      $rest     = $posts->slice(1);
    @endphp

    @if($featured)
      <article class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
        <a href="{{ route('posts.show', $featured->slug) }}"
           class="aspect-[16/9] w-full overflow-hidden rounded-2xl ring-1 ring-black/10 bg-stone-100
                  dark:ring-white/10 dark:bg-stone-800">
          <img
            src="{{ $featured->image_path && Storage::disk('public')->exists($featured->image_path) ? Storage::url($featured->image_path) : asset('images/default-post.png') }}"
            alt="{{ $featured->title }}"
            class="h-full w-full object-cover" />
        </a>

        <div class="rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 p-6
                    dark:bg-stone-900/70 dark:ring-white/10">
          <div class="text-xs text-stone-600 dark:text-stone-400">
            <span class="font-medium">{{ $featured->user?->name ?? 'Unknown' }}</span>
            <span>•</span>
            <time datetime="{{ $featured->created_at->toDateString() }}">{{ $featured->created_at->format('M j, Y') }}</time>
          </div>
          <h3 class="mt-2 font-orbitron text-2xl font-bold">
            <a href="{{ route('posts.show', $featured->slug) }}" class="hover:underline">{{ $featured->title }}</a>
          </h3>
          <p class="mt-2 text-stone-700 dark:text-stone-300 line-clamp-3">{{ $featured->excerpt }}</p>
          <a href="{{ route('posts.show', $featured->slug) }}"
             class="mt-4 inline-flex items-center font-semibold text-blue-600 hover:text-blue-500
                    dark:text-rose-300 dark:hover:text-rose-200">
            Read post
          </a>
        </div>
      </article>
    @endif

    <div class="mt-8 rounded-2xl bg-white/90 backdrop-blur ring-1 ring-black/5 shadow
          dark:bg-stone-900/70 dark:ring-white/10 overflow-hidden">
      <ul class="divide-y divide-stone-200/70 dark:divide-white/10">
        @foreach($rest as $post)
          <li class="p-5">
            <a href="{{ route('posts.show', $post->slug) }}" class="grid sm:grid-cols-[160px_1fr] gap-5 items-center group">
              <div class="aspect-[16/10] w-full sm:w-40 overflow-hidden rounded-lg ring-1 ring-black/10 bg-stone-100
                          dark:ring-white/10 dark:bg-stone-800">
                <img
                  src="{{ $post->image_path && Storage::disk('public')->exists($post->image_path) ? Storage::url($post->image_path) : asset('images/default-post.png') }}"
                  alt="{{ $post->title }}"
                  class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
              </div>
              <div>
                <div class="text-xs text-stone-600 dark:text-stone-400">
                  <span class="font-medium">{{ $post->user?->name ?? 'Unknown' }}</span>
                  <span>•</span>
                  <time datetime="{{ $post->created_at->toDateString() }}">{{ $post->created_at->format('M j, Y') }}</time>
                </div>
                <h4 class="mt-1 font-orbitron text-xl font-bold group-hover:underline">
                  {{ $post->title }}
                </h4>
                <p class="mt-1 text-stone-700 dark:text-stone-300 line-clamp-2">{{ $post->excerpt }}</p>
              </div>
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    @if(method_exists($posts, 'links'))
      <div class="mt-6">{{ $posts->links() }}</div>
    @endif

    <div class="py-10"></div>
  </section>
  {{-- ===== /BLOG FEATURED + LATEST ===== --}}
</div>
@endsection
