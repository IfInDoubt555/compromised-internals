@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Events Calendar | Compromised Internals',
        'description' => 'Explore upcoming and past rally events in our interactive calendar. Find dates, locations & detailed info for every rally.',
        'url'         => url()->current(),
        'image'       => asset('images/calendar-og.png'),
    ];

    // Year + optional championship param carried through to feed/download links
    $year      = now()->year;
    $champ     = request('champ'); // 'WRC' | 'ERC' | 'ARA' | null
    $feedRoute = route('calendar.feed.year', array_filter(['year' => $year, 'champ' => $champ]));
    $dlRoute   = route('calendar.download.year', array_filter(['year' => $year, 'champ' => $champ]));

    // webcal:// variant for native calendar apps
    $webcal = preg_replace('#^https?://#', 'webcal://', $feedRoute);
@endphp

@push('head')
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

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

    {{-- ICS: subscribe/import controls --}}
    <div class="mb-3 flex flex-col gap-2">
        <div class="flex flex-wrap items-stretch gap-2">
            <input
                type="text"
                readonly
                value="{{ $feedRoute }}"
                class="w-[30rem] max-w-full px-3 py-1.5 rounded border border-gray-300 text-sm bg-white"
                id="icsFeedUrl"
                aria-label="ICS feed URL (copy for Google Calendar / Outlook web)">
            <button
                type="button"
                class="px-3 py-1.5 rounded bg-gray-700 text-white text-sm"
                onclick="navigator.clipboard.writeText(document.getElementById('icsFeedUrl').value)">
                Copy URL
            </button>

            {{-- One-click subscribe for clients supporting webcal:// (Apple/Outlook variants) --}}
            <a href="{{ $webcal }}" class="px-3 py-1.5 rounded bg-blue-600 text-white text-sm">
                Subscribe (open in calendar app)
            </a>

            {{-- Static snapshot download (.ics file import) --}}
            <a href="{{ $dlRoute }}" class="px-3 py-1.5 rounded bg-gray-200 text-gray-900 text-sm">
                Download {{ $year }} (.ics)
            </a>
        </div>

        <p class="text-xs text-gray-600">
            Tip: For <strong>Google Calendar</strong>, click <em>Copy URL</em> and paste it in
            <em>Settings → Add calendar → From URL</em>. For <strong>Outlook</strong>, use
            <em>Add calendar → From Internet</em>. The <strong>Subscribe</strong> button uses
            <code>webcal://</code> for apps that support one-click subscriptions.
            @if($champ)
                <span class="ml-1">(Filtered for <strong>{{ $champ }}</strong>.)</span>
            @endif
        </p>
    </div>

    {{-- Legend + Filters (JS wires these via #cal-controls [data-champ]) --}}
    <div id="cal-controls" class="mb-3 flex flex-wrap items-center gap-3 text-sm">
        <div class="flex items-center gap-2">
            <span class="inline-block h-3 w-3 rounded-full bg-blue-700"></span> WRC
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block h-3 w-3 rounded-full bg-amber-500"></span> ERC
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block h-3 w-3 rounded-full bg-green-600"></span> ARA
        </div>

        <div class="ml-auto flex gap-2">
            <button data-champ=""    class="px-3 py-1 rounded bg-gray-700 text-white">All</button>
            <button data-champ="WRC" class="px-3 py-1 rounded bg-gray-200 text-gray-900">WRC</button>
            <button data-champ="ERC" class="px-3 py-1 rounded bg-gray-200 text-gray-900">ERC</button>
            <button data-champ="ARA" class="px-3 py-1 rounded bg-gray-200 text-gray-900">ARA</button>
        </div>
    </div>

    <div id="calendar" class="bg-white rounded shadow p-4"></div>

    <noscript class="text-red-500 text-center mt-4">
        Please enable JavaScript to view the event calendar.
    </noscript>
</div>
@endsection