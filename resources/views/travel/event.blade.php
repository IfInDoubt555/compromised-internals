@extends('layouts.app')

@push('head')
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $seo['url'] }}">
@endpush

@section('content')
{{-- Hero --}}
<section class="relative overflow-hidden">
  <div class="max-w-6xl mx-auto px-4 pt-8">
    <a href="{{ route('travel.index') }}" class="text-blue-300 hover:text-blue-200 inline-flex items-center gap-1">
      <span>←</span><span>Back to Plan Your Trip</span>
    </a>
  </div>

  <div class="max-w-6xl mx-auto px-4">
      <div class="relative scrim rounded-2xl px-6 py-8 mb-6
                  bg-gradient-to-r from-slate-900/80 via-slate-800/75 to-slate-900/80
                  ring-1 ring-white/10 shadow-lg backdrop-blur-sm text-white">
        <h1 class="text-3xl md:text-4xl font-bold tracking-wide text-glow">{{ $event->name }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-2">
          @if($event->location)
            <span class="pill">📍 {{ $event->location }}</span>
          @endif
          @if($event->start_date && $event->end_date)
            <span class="pill">🗓 {{ $event->start_date->toFormattedDateString() }} – {{    $event->end_date->toFormattedDateString() }}</span>
          @elseif($event->start_date)
            <span class="pill">🗓 {{ $event->start_date->toFormattedDateString() }}</span>
          @endif
          @if($event->official_url)
            <a href="{{ $event->official_url }}" target="_blank" rel="noopener" class="pill     pill-primary">🔗 Official site</a>
          @endif
        </div>

        {{-- mobile-only anchors --}}
        <nav class="mt-5 flex flex-wrap gap-2 md:hidden">
          @foreach (['hotels'=>'Hotels','camping'=>'Camping','flights'=>'Flights','cars'=>'Car Rentals',    'map'=>'Map'] as $id => $label)
            <a href="#{{ $id }}" class="pill pill-hover">{{ $label }}</a>
          @endforeach
        </nav>
      </div>
    </div>
</section>

{{-- Content grid --}}
<div class="max-w-6xl mx-auto px-4 py-8 grid md:grid-cols-2 gap-6">

  {{-- Hotels --}}
  <section id="hotels" class="rounded-xl bg-white/70 shadow-lg ring-1 ring-black/5 p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Find the Best Hotels</h2>
      <span class="text-xs text-gray-500">near HQ & popular stages</span>
    </header>
    <div class="mt-3 rounded-lg bg-gray-50 p-4 text-sm text-gray-600 min-h-[220px] flex items-center justify-center">
      Hotels widget will render here.
    </div>
  </section>

  {{-- Camping --}}
  <section id="camping" class="rounded-xl bg-white/70 shadow-lg ring-1 ring-black/5 p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Camping Near the Rally</h2>
      <span class="text-xs text-gray-500">tents, cabins & van sites</span>
    </header>
    <div class="mt-3 rounded-lg bg-gray-50 p-4 text-sm text-gray-600 min-h-[220px] flex items-center justify-center">
      Hipcamp widget / links will render here.
    </div>
  </section>

  {{-- Flights --}}
  <section id="flights" class="rounded-xl bg-white/70 shadow-lg ring-1 ring-black/5 p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Book Flights</h2>
      <span class="text-xs text-gray-500">closest airports</span>
    </header>
    <div class="mt-3 rounded-lg bg-gray-50 p-4 text-sm text-gray-600 min-h-[220px] flex items-center justify-center">
      Flights widget will render here.
    </div>
  </section>

  {{-- Cars --}}
  <section id="cars" class="rounded-xl bg-white/70 shadow-lg ring-1 ring-black/5 p-5">
    <header class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Car Rentals</h2>
      <span class="text-xs text-gray-500">reach remote stages</span>
    </header>
    <div class="mt-3 rounded-lg bg-gray-50 p-4 text-sm text-gray-600 min-h-[220px] flex items-center justify-center">
      Car rental widget will render here.
    </div>
  </section>
</div>

{{-- Map (same card shell as above) --}}
@if($event->map_embed_url)
  <section id="map" class="max-w-6xl mx-auto px-4 pb-12">
    <div class="rounded-xl bg-white/70 shadow-lg ring-1 ring-black/5 p-5">
      <h2 class="text-lg font-semibold mb-3">Map</h2>
      <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden ring-1 ring-black/5">
        <iframe src="{{ $event->map_embed_url }}" class="w-full h-full" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      </div>
    </div>
  </section>
@endif
@endsection
