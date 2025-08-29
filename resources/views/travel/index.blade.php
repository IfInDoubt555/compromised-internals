@extends('layouts.app')

@push('head')
    <title>Plan Your Trip | Compromised Internals</title>
    <meta name="description" content="Plan rally trips with hotels, camping, flights, and car rentals in one place.">
@endpush

@section('content')
{{-- Hero --}}
<section class="max-w-5xl mx-auto px-4 pt-8">
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

  <div class="mt-10 grid md:grid-cols-2 gap-6">
    @if (!empty($tipsLines))
      <section>
        <h2 class="text-xl font-bold mb-3 dark:text-stone-100">Travel Tips for Rally Fans</h2>
        <ul class="list-disc list-inside text-gray-700 space-y-1 dark:text-stone-300">
          @foreach ($tipsLines as $line)
            <li>{{ $line }}</li>
          @endforeach
        </ul>
      </section>
    @endif

    <section class="text-xs text-gray-500 self-end dark:text-stone-400">
      <p>
        Disclosure: Some links on this page are affiliate links. If you book through them, we may earn a small commission
        at no extra cost to you. This helps keep Compromised Internals running. Thanks for the support! ❤️
      </p>
    </section>
  </div>
</section>
@endsection