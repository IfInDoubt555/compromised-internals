@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4 mt-8 bg-white rounded shadow w-full sm:w-auto
            ring-1 ring-black/5
            dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
    <h1 class="text-3xl font-bold mb-8 text-center dark:text-stone-100">Shopping Cart</h1>

    @if (count($cart) > 0)
        @if (session('error'))
            <div class="bg-red-500 text-white px-4 py-3 rounded mt-4 text-center dark:bg-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">
            @foreach ($cart as $item)
                <div class="bg-white p-4 rounded shadow-md
                            ring-1 ring-black/5
                            dark:bg-stone-900/70 dark:ring-white/10">
                    <h2 class="text-xl font-semibold dark:text-stone-100">{{ $item['name'] }}</h2>
                    <p class="text-gray-600 dark:text-stone-300 mb-4">
                        ${{ number_format($item['price'], 2) }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-stone-300 mb-2">
                        Size: {{ $item['options']['size'] ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-stone-300 mb-4">
                        Color: {{ ucfirst($item['options']['color'] ?? 'N/A') }}
                    </p>

                    <form action="{{ route('shop.cart.update', ['id' => $item['id']]) }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <input
                            type="number"
                            name="quantity"
                            value="{{ $item['quantity'] }}"
                            min="1"
                            class="w-16 text-center border rounded-md
                                   dark:bg-stone-800 dark:text-stone-100 dark:border-white/10"
                        />
                        <button
                            onclick="updateCartBadge()"
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded
                                   dark:bg-sky-600 dark:hover:bg-sky-500">
                            Update
                        </button>
                        <a href="{{ route('shop.cart.remove', ['id' => $item['id']]) }}"
                           onclick="updateCartBadge()"
                           class="text-red-500 hover:underline ml-4 dark:text-rose-300">
                           Remove
                        </a>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('checkout.index') }}"
               class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded inline-block
                      dark:bg-emerald-600 dark:hover:bg-emerald-500">
                Checkout
            </a>
        </div>
    @else
        <p class="text-center text-gray-600 dark:text-stone-400 mb-8">Your cart is empty.</p>
    @endif

    <div class="text-center mt-8">
        <a href="{{ route('shop.index') }}" class="text-blue-600 hover:underline dark:text-sky-300">
            ← Back to Shop
        </a>
    </div>
</div>
@endsection