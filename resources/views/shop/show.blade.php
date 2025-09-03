{{-- resources/views/shop/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-8 pb-16">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

    {{-- IMAGE CARD --}}
    <div class="rounded-2xl bg-white/90 ring-1 ring-stone-900/10 shadow-sm
                dark:bg-stone-900/70 dark:ring-white/10 p-4">
      <x-product-image
        id="productImage"
        :image="$product->image_path"
        :colors="[
            'black' => 'images/products/skull-design-black.png',
            'white' => 'images/products/skull-design-white.png',
            'red'   => 'images/products/skull-design-red.png'
        ]" />
    </div>

    {{-- DETAILS CARD --}}
    <div class="rounded-2xl bg-white/90 ring-1 ring-stone-900/10 shadow-sm
                dark:bg-stone-900/70 dark:ring-white/10 p-6 lg:p-8">
      <h1 class="font-orbitron text-2xl sm:text-3xl font-extrabold tracking-tight text-stone-900 dark:text-stone-100">
        {{ $product->name }}
      </h1>

      <p class="mt-3 text-stone-700 dark:text-stone-300">
        {{ $product->description }}
      </p>

      <div class="mt-5 flex items-baseline gap-3">
        <span class="text-3xl font-orbitron font-extrabold text-stone-900 dark:text-stone-100">
          ${{ number_format($product->price, 2) }}
        </span>
        @if(!empty($product->msrp) && $product->msrp > $product->price)
          <span class="text-sm line-through text-stone-400 dark:text-stone-500">
            ${{ number_format($product->msrp, 2) }}
          </span>
        @endif
      </div>

      <form class="mt-6" action="{{ route('shop.cart.add', $product) }}" method="POST">
        @csrf

        @if ($product->has_variants)
          {{-- SIZE (chips) --}}
          <div class="mb-5">
            <label class="block text-sm font-semibold text-stone-900 dark:text-stone-100 mb-2">Size</label>
            <div class="flex flex-wrap gap-2">
              @foreach (['S'=>'Small','M'=>'Medium','L'=>'Large','XL'=>'XL'] as $val=>$label)
                <label class="cursor-pointer">
                  <input type="radio" name="size" value="{{ $val }}" class="peer sr-only" required>
                  <span
                    class="inline-flex items-center justify-center min-w-10 h-10 px-3 rounded-full text-sm font-semibold
                           ring-1 ring-stone-900/10 bg-white text-stone-900
                           peer-checked:bg-stone-900 peer-checked:text-white
                           dark:bg-stone-800/60 dark:text-stone-100 dark:ring-white/10
                           dark:peer-checked:bg-stone-100 dark:peer-checked:text-stone-900">
                    {{ $label }}
                  </span>
                </label>
              @endforeach
            </div>
          </div>

          {{-- COLOR (swatches) --}}
          <div class="mb-6">
            <span class="block text-sm font-semibold text-stone-900 dark:text-stone-100 mb-2">Color</span>
                  
            @php
              $swatches = ['black' => '#000000', 'white' => '#ffffff', 'red' => '#dc2626'];
            @endphp
          
            <div class="flex items-center gap-3">
              @foreach ($swatches as $name => $hex)
                <label class="relative cursor-pointer">
                  <input
                    type="radio"
                    name="color"
                    value="{{ $name }}"
                    class="sr-only peer"
                    required
                    onchange="changeShirtColor('{{ $name }}')">
                  
                  <span
                    class="block w-8 h-8 rounded-full ring-1 ring-stone-900/10 shadow-sm"
                    style="background-color: {{ $hex }};"></span>
                  
                  <span
                    class="pointer-events-none absolute inset-1 rounded-full ring-2 ring-transparent
                           peer-checked:ring-stone-900 dark:peer-checked:ring-white"></span>
                </label>
              @endforeach
            </div>
          </div>
        @endif

        {{-- QTY + CTA --}}
        <div class="flex items-center gap-3">
          <label for="qty" class="sr-only">Quantity</label>
          <input id="qty" name="quantity" type="number" min="1" value="1"
                 class="w-20 rounded-md border border-stone-300 bg-white px-3 py-2 text-stone-900 shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-sky-400/40
                        dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100"
          >
          <button
            type="submit"
            class="ci-btn bg-indigo-600 text-white hover:bg-indigo-700 flex-1 sm:flex-none px-5 py-2 rounded-lg">
            Add to Cart
          </button>
        </div>

        {{-- Microcopy --}}
        <p class="mt-3 text-xs text-stone-500 dark:text-stone-400">
          Ships in 2–4 business days. 30-day returns.
        </p>
      </form>

      <div class="mt-8">
        <a href="{{ route('shop.index') }}" class="ci-link">← Back to Shop</a>
      </div>
    </div>

  </div>
</div>

{{-- Color Swap Script --}}
<script>
  function changeShirtColor(color) {
    const img = document.getElementById('productImage');
    if (!img) return;
    const mapped = img.dataset?.[color];
    if (mapped) img.src = mapped;
  }
</script>
@endsection