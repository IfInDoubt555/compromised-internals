@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">

    <!-- Image -->
    <div class="flex justify-center dark:bg-stone-900/60 dark:ring-1 dark:ring-white/10 dark:rounded-2xl dark:p-4">
      <x-product-image
        :image="$product->image_path"
        :colors="[
            'black' => 'images/products/skull-design-black.png',
            'white' => 'images/products/skull-design-white.png',
            'red'   => 'images/products/skull-design-red.png'
        ]" />
    </div>

    <!-- Product Info -->
    <div class="dark:bg-stone-900/70 dark:ring-1 dark:ring-white/10 dark:rounded-2xl dark:p-6">
      <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-stone-100">{{ $product->name }}</h1>
      <p class="text-gray-600 dark:text-stone-300 mb-6">{{ $product->description }}</p>
      <p class="text-2xl font-semibold mb-6 text-gray-900 dark:text-stone-100">
        ${{ number_format($product->price, 2) }}
      </p>

      <form action="{{ route('shop.cart.add', $product) }}" method="POST">
        @csrf

        @if ($product->has_variants)
          <!-- Size -->
          <div class="mb-4 w-32">
            <label for="size" class="block font-medium mb-1 text-gray-900 dark:text-stone-100">Size</label>
            <select
              name="size" id="size"
              class="w-full rounded px-4 py-2 border border-gray-300 bg-gray-300 text-gray-900
                     dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100 dark:placeholder-stone-500">
              <option value="S">Small</option>
              <option value="M">Medium</option>
              <option value="L">Large</option>
              <option value="XL">XL</option>
            </select>
          </div>

          <!-- Color -->
          <div class="mb-6">
            <label class="block font-medium mb-2 text-gray-900 dark:text-stone-100">Color</label>
            <div class="flex gap-3">
              @foreach (['black', 'white', 'red'] as $color)
                <label class="inline-block">
                  <input
                    type="radio"
                    name="color"
                    value="{{ $color }}"
                    class="sr-only peer"
                    required
                    onchange="changeShirtColor('{{ $color }}')">
                  <div
                    class="w-8 h-8 rounded-full border-2 cursor-pointer transition
                           peer-checked:ring-2 peer-checked:ring-gray-800 dark:peer-checked:ring-white"
                    style="background-color: {{ $color }};">
                  </div>
                </label>
              @endforeach
            </div>
          </div>
        @endif

        <button
          type="submit"
          class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded
                 dark:bg-sky-500/90 dark:hover:bg-sky-500">
          Add to Cart
        </button>
      </form>

      <div class="mt-6">
        <a href="{{ route('shop.index') }}" class="text-blue-600 hover:underline dark:text-sky-300">
          ← Back to Shop
        </a>
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
    if (newSrc) img.src = newSrc;
  }
</script>
@endsection