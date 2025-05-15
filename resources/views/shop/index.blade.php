@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-bold mb-8">Shop</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        @forelse($products as $product)
        <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-xl transition-shadow">
            @if ($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}"
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
    @if (app()->environment('local') || app()->environment('development'))
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded mt-4 max-w-3xl mx-auto">
        <p class="font-bold text-lg mb-2">⚠️ Test Mode Enabled – No Real Charges</p>
        <p>This shop is currently in testing. You will <strong>not</strong> be charged. Use the following test card during checkout:</p>
        <ul class="list-disc list-inside mt-2 text-sm">
            <li><strong>Card Number:</strong> <code>4242 4242 4242 4242</code></li>
            <li><strong>Expiration Date:</strong> Any future date (e.g., 12/34)</li>
            <li><strong>CVC:</strong> Any 3 digits (e.g., 123)</li>
            <li><strong>ZIP:</strong> Any 5-digit ZIP (e.g., 12345)</li>
        </ul>
        <p class="mt-2">⚠️ Attempting to use a real card will result in an error or no charge.</p>
        <p class="mt-4">
            💬 <strong>Help Wanted:</strong> I'm testing the dashboard’s purchase history system. Please feel free to "buy" one or more items so I can confirm everything works correctly behind the scenes.
            Your help is appreciated!
        </p>
    </div>
    @endif

</div>
@endsection