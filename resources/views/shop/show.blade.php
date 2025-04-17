@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
    
    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="rounded-lg mb-6 w-full h-auto">

    <p class="text-gray-700 mb-4">{{ $product->description }}</p>

    <p class="text-2xl font-semibold mb-4">${{ number_format($product->price, 2) }}</p>

    <a href="{{ route('shop.cart.add', ['product' => $product->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Add to Cart
    </a>

    <div class="mt-6">
        <a href="{{ route('shop.index') }}" class="text-blue-600 hover:underline">← Back to Shop</a>
    </div>
</div>
@endsection