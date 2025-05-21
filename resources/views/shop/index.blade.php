@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Shop | Compromised Internals Rally Gear & Merch',
        'description' => 'Browse Compromised Internals’ rally racing shop for premium gear, apparel, and collectibles. (Currently in development mode—test purchases only.)',
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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold mb-8">Shop</h1>
        <div class="mb-6 p-4 rounded-lg border border-yellow-400 bg-yellow-100 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-100 w-fit shadow-md animate-fade-in">
            ⚠️ <strong>Notice:</strong> This shop is currently in <span class="font-semibold">development mode</span>. Purchases will not be    processed.
            <br class="hidden sm:block" />
            You can use the following Stripe test card:
            <ul class="list-disc list-inside mt-2 text-sm">
                <li><strong>Card:</strong> 4242 4242 4242 4242</li>
                <li><strong>Exp:</strong> Any future date (e.g., 12/34)</li>
                <li><strong>CVC:</strong> Any 3 digits (e.g., 123)</li>
                <li><strong>ZIP:</strong> Any 5 digits (e.g., 12345)</li>
            </ul>
        </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        @forelse($products as $product)
        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition-shadow">
            @if ($product->image_path)
            <img src="{{ asset('images/products/' . $product->image_path) }}"
                alt="{{ $product->name }}"
                class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                No Image
            </div>
            @endif

            <div class="p-4">
                <h2 class="text-lg font-semibold mb-2">{{ $product->name }}</h2>
                <p class="text-sm text-gray-600 mb-2">{{ $product->type }}</p>
                <p class="text-gray-800 font-bold mb-4">${{ number_format($product->price, 2) }}</p>
                <a href="{{ route('shop.show', $product->slug) }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    View Product
                </a>
            </div>
        </div>
        @empty
        <p>No products available yet.</p>
        @endforelse
    </div>
</div>
@endsection