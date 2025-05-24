@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Shop | Coming Soon - Compromised Internals',
        'description' => 'Our rally gear store is on the way! Stay tuned for premium apparel, collectibles, and more. Sign up for updates or support development on Buy Me a Coffee.',
        'url'         => url()->current(),
        'image'       => asset('images/shop-og.png'),
    ];
@endphp

@push('head')
    {{-- Primary Meta Tags --}}
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    {{-- Open Graph / Link Preview --}}
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Compromised Internals">
    <meta property="og:url"         content="{{ $seo['url'] }}">
    <meta property="og:title"       content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image"       content="{{ $seo['image'] }}">
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:url"         content="{{ $seo['url'] }}" />
    <meta name="twitter:title"       content="{{ $seo['title'] }}" />
    <meta name="twitter:description" content="{{ $seo['description'] }}" />
    <meta name="twitter:image"       content="{{ $seo['image'] }}" />
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <h1 class="text-4xl font-bold mb-6">Rally Shop Coming Soon!</h1>
    <p class="text-lg text-gray-700 mb-8">
        This project is a labor of love, and I’m focused on growing it steadily.<br>
        Join me on this journey by signing up for updates or supporting the site on Buy Me a Coffee.
    </p>
    <a href="https://buymeacoffee.com/CompromisedInternals" target="_blank" rel="noopener"
       class="inline-block px-6 py-3 bg-yellow-500 text-white font-semibold rounded-lg shadow hover:bg-yellow-600 transition">
        Any support is appreciated!
    </a>

    <div class="mt-12 text-gray-500">
        <p>No products are available yet. Check back soon!</p>
    </div>
</div>
@endsection