@extends('layouts.app')

@push('head')
    <title>Plan Your Trip | Compromised Internals</title>
    <meta name="description" content="Plan rally trips with hotels, camping, flights, and car rentals in one place.">
@endpush

@section('content')
{{-- Hero --}}
<section class="max-w-5xl mx-auto px-4 pt-8">
  <div class="rounded-2xl px-6 py-8
              bg-gradient-to-r from-slate-800/70 via-slate-700/60 to-slate-800/70
              ring-1 ring-white/10 shadow-lg mb-6">
    <h1 class="text-3xl md:text-4xl font-bold">Plan Your Trip</h1>
    <p class="text-slate-200/90 mt-2">
      Heading to a rally? Compare <span class="font-semibold">hotels, camping, flights, and car rentals</span> in one place.
    </p>

    {{-- Quick picks (manual highlights or next 3) --}}
    <div class="mt-5">
      <h3 class="text-lg font-semibold mb-2">Find Travel by Rally</h3>
      <ul class="grid sm:grid-cols-2 gap-2">
        @forelse($items as $it)
          <li>
            <a href="{{ $it['url'] }}"
               class="pill pill-hover w-full justify-center">
              {{ $it['title'] }}
            </a>
          </li>
        @empty
          <li class="text-gray-300">No upcoming events yet. Check back soon.</li>
        @endforelse
      </ul>
    </div>
  </div>
</section>

{{-- Tools --}}
<section class="max-w-5xl mx-auto px-4 py-8">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Hotels --}}
    <article class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition">
      <h2 class="text-xl font-bold">Find the Best Hotels</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4">Compare prices near rally HQ and popular stages.</p>
      <div id="tp-hotels-widget"
           class="min-h-[220px] bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 ring-1 ring-black/5">
        Hotels widget will render here.
      </div>
    </article>

    {{-- Camping --}}
    <article class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition">
      <h2 class="text-xl font-bold">Camping Near the Rally</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4">Prefer tents, cabins, or van sites close to stages?</p>
      <div id="hipcamp-widget"
           class="min-h-[220px] bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 ring-1 ring-black/5">
        Hipcamp widget / links will render here.
      </div>
    </article>

    {{-- Flights --}}
    <article class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition">
      <h2 class="text-xl font-bold">Book Flights</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4">Fly into the closest airports for each event weekend.</p>
      <div id="tp-flights-widget"
           class="min-h-[220px] bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 ring-1 ring-black/5">
        Flights widget will render here.
      </div>
    </article>

    {{-- Cars --}}
    <article class="rounded-xl border border-white/10 bg-white/70 p-5 shadow-lg hover:shadow-xl transition">
      <h2 class="text-xl font-bold">Car Rentals</h2>
      <p class="text-gray-600 text-sm mt-1 mb-4">Get to remote stages and service parks with ease.</p>
      <div id="tp-cars-widget"
           class="min-h-[220px] bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 ring-1 ring-black/5">
        Car rental widget will render here.
      </div>
    </article>
  </div>

  {{-- Tips + disclosure --}}
  <div class="mt-10 grid md:grid-cols-2 gap-6">
    <section>
      <h2 class="text-xl font-bold mb-3">Travel Tips for Rally Fans</h2>
      <ul class="list-disc list-inside text-gray-700 space-y-1">
        <li>Book early for Monte-Carlo and Finland — hotels & camping fill fast.</li>
        <li>Consider car rentals for Portugal or Sardinia — many stages are remote.</li>
        <li>Check official event sites for shuttles and restricted roads.</li>
      </ul>
    </section>
    <section class="text-xs text-gray-500 self-end">
      <p>
        Disclosure: Some links on this page are affiliate links. If you book through them, we may earn a small commission
        at no extra cost to you. This helps keep Compromised Internals running. Thanks for the support! ❤️
      </p>
    </section>
  </div>
</section>
@endsection