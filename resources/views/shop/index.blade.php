@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold mb-8">Shop</h1>

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