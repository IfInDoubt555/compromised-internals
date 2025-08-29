{{-- resources/views/shop/success.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-6 bg-white shadow rounded p-8 text-center
            ring-1 ring-black/5
            dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
    <h1 class="text-3xl font-bold mb-4 text-green-600 dark:text-emerald-300">
        Payment Successful 🎉
    </h1>

    <p class="text-gray-700 dark:text-stone-300 mb-6">
        Thank you for your purchase! Your order is being processed.
    </p>

    <a href="{{ route('shop.index') }}"
       class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded
              dark:bg-emerald-600 dark:hover:bg-emerald-500">
        Continue Shopping
    </a>
</div>
@endsection