@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Events Calendar | Compromised Internals',
        'description' => 'Explore upcoming and past rally events in our interactive calendar. Find dates, locations & detailed info for every rally.',
        'url'         => url()->current(),
        'image'       => asset('images/calendar-og.png'),
    ];

    $year  = now()->year;
    $champ = request('champ'); // 'WRC' | 'ERC' | 'ARA' | null

    $feedRoute = route('calendar.feed.year', array_filter(['year' => $year, 'champ' => $champ]));
    $dlRoute   = route('calendar.download.year', array_filter(['year' => $year, 'champ' => $champ]));

    $webcal     = preg_replace('#^https?://#', 'webcal://', $feedRoute);
    $gcalAddUrl = 'https://calendar.google.com/calendar/r?cid=' . urlencode($feedRoute);
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

    {{-- Provide URL templates to app.js so it can swap year & champ --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // IMPORTANT: use url('/...') so {year} stays literal (route() would encode it)
        document.body.dataset.feedTpl     = "{{ url('/calendar/feed/{year}.ics') }}";
        document.body.dataset.downloadTpl = "{{ url('/calendar/download/{year}.ics') }}";
      });
    </script>

@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Rally Calendar</h1>

    {{-- Legend + Filters (wired by app.js via #cal-controls [data-champ]) --}}
    <div id="cal-controls" class="mb-4 flex flex-wrap items-center gap-3 text-sm">
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

    {{-- Calendar --}}
    <div id="calendar" class="ci-card p-4"></div>

    {{-- Subscribe / Download (hidden in a drawer) --}}
    <details class="mt-6 group">
        <summary class="cursor-pointer select-none flex items-center justify-between rounded-lg ring-1 ring-stone-900/5 dark:ring-white/10 bg-stone-100/70 dark:bg-stone-800/40 hover:bg-stone-100/90 dark:hover:bg-stone-800/60 px-4 py-3 text-sm font-semibold">
            <span>Subscribe / Download calendar options</span>
            <span class="ml-3 text-gray-500 group-open:rotate-180 transition-transform">▾</span>
        </summary>

        <div class="mt-4 grid gap-4 md:grid-cols-3">
            {{-- Google --}}
            <div class="ci-card p-4">
                <h3 class="text-sm font-bold mb-2">Google Calendar (Web / Android / iOS)</h3>
                <a id="ics-gcal-btn" href="{{ $gcalAddUrl }}" target="_blank" rel="noopener"
                   class="inline-block w-full text-center px-3 py-2 rounded bg-[#1a73e8] text-white text-sm font-semibold">
                    Open in Google Calendar
                </a>
                <p class="mt-2 text-xs text-gray-600">
                    If that doesn’t open, copy the feed URL below and paste it in
                    <em>Settings → Add calendar → From URL</em>.
                </p>
            </div>

            {{-- Apple --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-bold mb-2">Apple Calendar (iPhone / iPad / Mac)</h3>
                <a id="ics-apple-btn" href="{{ $webcal }}"
                   class="inline-block w-full text-center px-3 py-2 rounded bg-black text-white text-sm font-semibold">
                    Subscribe via Apple Calendar
                </a>
                <p class="mt-2 text-xs text-gray-600">
                    Taps the <code>webcal://</code> link and opens the Calendar app to subscribe.
                </p>
                <a id="ics-download-btn" href="{{ $dlRoute }}"
                   class="mt-2 inline-block w-full text-center px-3 py-2 rounded bg-gray-200 text-gray-900 text-sm">
                    Or download {{ $year }} (.ics)
                </a>
            </div>

            {{-- Outlook --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="text-sm font-bold mb-2">Outlook (Windows / Mac / Web)</h3>
                <a id="ics-outlook-btn" href="{{ $webcal }}"
                   class="inline-block w-full text-center px-3 py-2 rounded bg-[#2563eb] text-white text-sm font-semibold">
                    Subscribe in Outlook
                </a>
                <p class="mt-2 text-xs text-gray-600">
                    On Outlook Web: <em>My Calendars → Add calendar → From Internet</em>, then paste the feed URL.
                </p>
                <a href="{{ $dlRoute }}"
                   class="mt-2 inline-block w-full text-center px-3 py-2 rounded bg-gray-200 text-gray-900 text-sm">
                    Or download {{ $year }} (.ics)
                </a>
            </div>
        </div>

        {{-- Feed URL + copy --}}
        <div class="mt-4 flex flex-wrap items-stretch gap-2">
            <input
                type="text"
                readonly
                value="{{ $feedRoute }}"
                class="w-[32rem] max-w-full px-3 py-2 rounded border border-stone-300 dark:border-white/10 text-sm bg-white dark:bg-stone-800/60 text-stone-900 dark:text-stone-100 placeholder-stone-400 dark:placeholder-stone-500"
                id="icsFeedUrl"
                aria-label="ICS feed URL (copy for manual subscribe)">
            <button
                type="button"
                class="px-3 py-2 rounded bg-gray-700 text-white text-sm"
                onclick="navigator.clipboard.writeText(document.getElementById('icsFeedUrl').value)">
                Copy URL
            </button>
        </div>

        <p class="mt-2 text-xs text-gray-600">
            <strong>What to use?</strong>
            Google users: click <em>Open in Google Calendar</em>. Apple/Outlook users: click <em>Subscribe</em> to open your calendar app automatically.
            You can always import a one-time snapshot by downloading the <code>.ics</code> file instead.
            @if($champ)
                <span class="ml-1">(Links are currently filtered for <strong>{{ $champ }}</strong>.)</span>
            @endif
        </p>
    </details>

    <noscript class="text-red-500 text-center mt-4">
        Please enable JavaScript to view the event calendar.
    </noscript>
</div>
@endsection
