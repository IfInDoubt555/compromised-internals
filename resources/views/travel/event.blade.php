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
<div class="max-w-6xl mx-auto px-4 py-8">
    <a href="{{ route('travel.index') }}" class="text-blue-600 hover:underline">← Back to Plan Your Trip</a>

    <h1 class="text-3xl font-bold mt-4">{{ $event->name }}</h1>
    <p class="text-gray-600 mt-1">
        {{ $event->location }}
        @if($event->start_date && $event->end_date)
            • {{ $event->start_date->toFormattedDateString() }} – {{ $event->end_date->toFormattedDateString() }}
        @elseif($event->start_date)
            • {{ $event->start_date->toFormattedDateString() }}
        @endif
    </p>

    @if($event->official_url)
        <p class="mt-2">
            Official site: <a href="{{ $event->official_url }}" class="text-blue-600 underline" rel="noopener" target="_blank">{{ $event->official_url }}</a>
        </p>
    @endif

    <div class="grid md:grid-cols-2 gap-6 mt-8">
        <!-- Hotels -->
        <div class="bg-white/70 rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold">Find the Best Hotels</h2>
            <p class="text-xs text-gray-500 mb-3">Compare prices near rally HQ and popular stages.</p>
            <div class="bg-gray-100 rounded p-4 text-sm text-gray-600">
                <!-- Drop your affiliate widget/embed here -->
                Hotels widget will render here.
            </div>
        </div>

        <!-- Camping -->
        <div class="bg-white/70 rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold">Camping Near the Rally</h2>
            <p class="text-xs text-gray-500 mb-3">Prefer tents, cabins, or van sites close to stages?</p>
            <div class="bg-gray-100 rounded p-4 text-sm text-gray-600">
                Hipcamp widget / links will render here.
            </div>
        </div>

        <!-- Flights -->
        <div class="bg-white/70 rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold">Book Flights</h2>
            <p class="text-xs text-gray-500 mb-3">Fly into the closest airports for this rally weekend.</p>
            <div class="bg-gray-100 rounded p-4 text-sm text-gray-600">
                Flights widget will render here.
            </div>
        </div>

        <!-- Car Rentals -->
        <div class="bg-white/70 rounded-lg shadow p-5">
            <h2 class="text-lg font-semibold">Car Rentals</h2>
            <p class="text-xs text-gray-500 mb-3">Get to remote stages and service parks with ease.</p>
            <div class="bg-gray-100 rounded p-4 text-sm text-gray-600">
                Car rental widget will render here.
            </div>
        </div>
    </div>

    @if($event->map_embed_url)
        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-3">Map</h2>
            <div class="aspect-video bg-gray-200 rounded overflow-hidden">
                <iframe src="{{ $event->map_embed_url }}" class="w-full h-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        </div>
    @endif
</div>
@endsection