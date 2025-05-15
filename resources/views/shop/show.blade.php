@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
        <!-- Image -->
        <div class="flex justify-center">
            <x-product-image
                :image="$product->image_path"
                :colors="[
                    'black' => 'products/skull-design-black.png',
                    'white' => 'products/skull-design-white.png',
                    'red' => 'products/skull-design-red.png'
                ]"
            />
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            <p class="text-gray-600 mb-6">{{ $product->description }}</p>
            <p class="text-2xl font-semibold mb-6">${{ number_format($product->price, 2) }}</p>

            <form action="{{ route('shop.cart.add', $product) }}" method="POST">
                @csrf

                @if ($product->has_variants)
                <!-- Size -->
                <div class="mb-4 w-32">
                    <label for="size" class="block font-medium mb-1">Size</label>
                    <select name="size" id="size" class="w-full border rounded px-4 py-2">
                        <option value="S">Small</option>
                        <option value="M">Medium</option>
                        <option value="L">Large</option>
                        <option value="XL">XL</option>
                    </select>
                </div>

                <!-- Color -->
                <div class="mb-6">
                    <label class="block font-medium mb-2">Color</label>
                    <div class="flex gap-3">
                        @foreach (['black', 'white', 'red'] as $color)
                        <label>
                            <input
                                type="radio"
                                name="color"
                                value="{{ $color }}"
                                class="sr-only peer"
                                required
                                onchange="changeShirtColor('{{ $color }}')">
                            <div class="w-8 h-8 rounded-full border-2 cursor-pointer peer-checked:ring-2 peer-checked:ring-gray-800 transition"
                                style="background-color: {{ $color }};">
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                    Add to Cart
                </button>
            </form>

            <div class="mt-6">
                <a href="{{ route('shop.index') }}" class="text-blue-600 hover:underline">← Back to Shop</a>
            </div>
        </div>
    </div>
</div>

{{-- Color Swap Script --}}
<script>
    function changeShirtColor(color) {
        const img = document.getElementById('productImage');
        if (!img) return;

        const newSrc = img.dataset[color];
        if (newSrc) {
            img.src = newSrc;
        }
    }
</script>
@endsection