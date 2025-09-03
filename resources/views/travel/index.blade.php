@extends('layouts.app')

@php
$seo = [
    'title'       => 'Plan Your Rally Trip | Hotels, Flights & Travel Guides – Compromised Internals',
    'description' => 'Plan your rally adventure with Compromised Internals: find hotels, flights, car rentals, and camping options near WRC, ERC, and ARA events worldwide.',
    'url'         => url()->current(),
    'image'       => asset('images/travel-og.png'),
];
@endphp

@push('head')
    <link rel="canonical" href="{{ $seo['url'] }}">
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Compromised Internals">
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
@endpush

@section('content')
{{-- Hero --}}
<section class="max-w-5xl mx-auto px-4 pt-14 lg:pt-18">
  <div
    class="relative scrim rounded-2xl px-6 py-8 mb-6
           bg-gradient-to-r from-slate-900/80 via-slate-800/75 to-slate-900/80
           ring-1 ring-white/10 shadow-lg backdrop-blur-sm text-white
           dark:from-amber-900/35 dark:via-yellow-900/25 dark:to-amber-900/35
           dark:ring-amber-400/20">
    <h1
      class="text-3xl md:text-4xl font-bold text-glow
             dark:bg-gradient-to-r dark:from-amber-200 dark:via-amber-300 dark:to-yellow-200
             dark:bg-clip-text dark:text-transparent">
      Plan Your Trip
    </h1>
    <p class="mt-2 text-white/90 dark:text-amber-50/90">
      Heading to a rally? Compare <span class="font-semibold">hotels, camping, flights, and car rentals</span> in one place.
    </p>

    {{-- Find Travel by Rally --}}
    <div class="mt-5">
      <h3 class="text-lg font-semibold mb-2 dark:text-amber-100">Find Travel by Rally</h3>
      <ul class="grid sm:grid-cols-2 gap-2">
        @forelse($items as $it)
          <li>
            <a href="{{ $it['url'] }}"
               class="pill pill-hover w-full justify-center
                      dark:bg-amber-200/10 dark:ring-amber-400/25 dark:hover:bg-amber-200/20">
              {{ $it['title'] }}
            </a>
          </li>
        @empty
          <li class="text-white/70 dark:text-amber-100/80">No upcoming events yet. Check back soon.</li>
        @endforelse
      </ul>
    </div>
  </div>
</section>

{{-- Tools --}}
<section class="max-w-5xl mx-auto px-4 py-8">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Hotels --}}
    <article
      class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition
             dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:border-white/10">
      <h2 class="text-xl font-bold dark:text-stone-100">Find the Best Hotels</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4 dark:text-stone-400">Compare prices near rally HQ and popular stages.</p>
      <div id="tp-hotels-widget"
           class="min-h-[220px] rounded-lg flex items-center justify-center
                  bg-gray-50 text-gray-500 ring-1 ring-black/5
                  dark:bg-stone-800/60 dark:text-stone-300 dark:ring-white/10">
        Hotels coming soon!
      </div>
    </article>

    {{-- Camping --}}
    <article
      class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition
             dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:border-white/10">
      <h2 class="text-xl font-bold dark:text-stone-100">Camping Near the Rally</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4 dark:text-stone-400">Prefer tents, cabins, or van sites close to stages?</p>
      <div id="hipcamp-widget"
           class="min-h-[220px] rounded-lg flex items-center justify-center
                  bg-gray-50 text-gray-500 ring-1 ring-black/5
                  dark:bg-stone-800/60 dark:text-stone-300 dark:ring-white/10">
        Camping coming soon!
      </div>
    </article>

    {{-- Flights --}}
    <article
      class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition
             dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:border-white/10">
      <h2 class="text-xl font-bold dark:text-stone-100">Book Flights</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4 dark:text-stone-400">Fly into the closest airports for each event weekend.</p>
      <div id="tp-flights-widget"
           class="min-h-[220px] rounded-lg flex items-center justify-center
                  bg-gray-50 text-gray-500 ring-1 ring-black/5
                  dark:bg-stone-800/60 dark:text-stone-300 dark:ring-white/10">
        Flights coming soon!
      </div>
    </article>

    {{-- Cars --}}
    <article
      class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition
             dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:border-white/10">
      <h2 class="text-xl font-bold dark:text-stone-100">Car Rentals</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4 dark:text-stone-400">Get to remote stages and service parks with ease.</p>
      <div id="tp-cars-widget"
           class="min-h-[220px] rounded-lg flex items-center justify-center
                  bg-gray-50 text-gray-500 ring-1 ring-black/5
                  dark:bg-stone-800/60 dark:text-stone-300 dark:ring-white/10">
        Car rental coming soon!
      </div>
    </article>
  </div>

  {{-- Tips + disclosure --}}
@php
  $tipsLines = [];
  if (!empty($tips) && $tips->is_active) {
      $all = collect(preg_split('/\R/', (string) $tips->tips_md))
            ->map(fn($t) => trim($t))->filter()->values();
      $selection = $tips->tips_selection;
      $tipsLines = is_null($selection)
          ? $all->all()
          : collect($selection)->map(fn($i) => (int)$i)
            ->filter(fn($i) => $i >= 0 && $i < $all->count())
            ->map(fn($i) => $all[$i])->all();
  }
@endphp

<section class="max-w-5xl mx-auto mt-10">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Tips card (spans 2 cols on md+) --}}
    <article class="md:col-span-2 rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:border-white/10">
      <h2 class="text-xl font-bold mb-3 dark:text-stone-100">Travel Tips for Rally Fans</h2>

      @if (!empty($tipsLines))
        <ul class="space-y-2">
          @foreach ($tipsLines as $line)
            <li class="flex items-start gap-2 text-gray-700 dark:text-stone-300">
              <svg class="mt-1 h-4 w-4 shrink-0 text-gray-500 dark:text-stone-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm.75-11.5a.75.75 0 10-1.5 0v4a.75.75 0 001.5 0v-4Zm0 7a.75.75 0 10-1.5 0 .75.75 0 001.5 0Z" clip-rule="evenodd"/>
              </svg>
              <span>{{ $line }}</span>
            </li>
          @endforeach
        </ul>
      @else
        <p class="text-gray-600 dark:text-stone-400">We’ll add event-specific tips soon. Check back before your rally weekend!</p>
      @endif
    </article>

    {{-- Disclosure card --}}
    <aside class="rounded-xl border border-white/10 bg-white/70 p-4 shadow-md self-start
                   dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:border-white/10">
      <div class="text-xs leading-relaxed text-gray-700 dark:text-stone-300">
        <span class="inline-flex items-center gap-2 font-semibold text-gray-800 dark:text-stone-100">
          <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 .5a9.5 9.5 0 1 0 0 19 9.5 9.5 0 0 0 0-19Zm0 5a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm1 9H9v-6h2v6Z"/>
          </svg>
          Disclosure
        </span>
        <p class="mt-2">
          Some links on this page are affiliate links. If you book through them, we may earn a small commission at no extra cost to you. This helps keep Compromised Internals running. Thanks for the support! ❤️
        </p>
      </div>
    </aside>
  </div>
</section>
@endsection