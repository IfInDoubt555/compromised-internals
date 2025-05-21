@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Events Calendar | Compromised Internals',
        'description' => 'Explore upcoming and past rally events in our interactive calendar. Find dates, locations & detailed info for every rally.',
        'url'         => url()->current(),
        'image'       => asset('images/calendar-og.png'), // put your own banner here
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
    <h1 class="text-3xl font-bold mb-6">Rally Calendar</h1>

    <div id="calendar" class="bg-white rounded shadow p-4"></div>

    <noscript class="text-red-500 text-center mt-4">
        Please enable JavaScript to view the event calendar.
    </noscript>
</div>
@endsection