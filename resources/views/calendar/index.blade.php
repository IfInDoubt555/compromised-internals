@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Events Calendar | Compromised Internals',
        'description' => 'Explore upcoming and past rally events in our interactive calendar. Find dates, locations & detailed info for every rally.',
        'url'         => url()->current(),
        'image'       => asset('images/calendar-og.png'),
    ];
@endphp

@push('head')
    <!-- Primary Meta Tags -->
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

    <!-- Open Graph / Link Preview Tags -->
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Compromised Internals">
    <meta property="og:url"         content="{{ $seo['url'] }}">
    <meta property="og:title"       content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image"       content="{{ $seo['image'] }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:url"         content="{{ $seo['url'] }}">
    <meta name="twitter:title"       content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image"       content="{{ $seo['image'] }}">
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Rally Calendar</h1>

    {{-- ICS actions --}}
    <div class="mb-4 flex flex-wrap items-center gap-2">
        {{-- Subscribe via URL (inline ICS) --}}
        <a href="{{ route('calendar.feed.year', ['year' => now()->year]) }}"
           target="_blank" rel="noopener"
           class="px-3 py-1.5 rounded bg-gray-700 text-white text-sm">
            Subscribe (Google Calendar via URL)
        </a>

        {{-- Download .ics snapshot --}}
        <a href="{{ route('calendar.download.year', ['year' => now()->year]) }}"
           class="px-3 py-1.5 rounded bg-gray-200 text-gray-900 text-sm">
            Download {{ now()->year }} (.ics)
        </a>

        {{-- Optional: subscribe only WRC --}}
        <a href="{{ route('calendar.feed.year', ['year' => now()->year, 'champ' => 'WRC']) }}"
           target="_blank" rel="noopener"
           class="px-3 py-1.5 rounded bg-red-700 text-white text-sm">
            Subscribe WRC
        </a>
    </div>

    <div id="calendar" class="bg-white rounded shadow p-4"></div>

    <noscript class="text-red-500 text-center mt-4">
        Please enable JavaScript to view the event calendar.
    </noscript>
</div>
@endsection