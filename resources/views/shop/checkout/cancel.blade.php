{{-- resources/views/shop/cancel.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-white shadow rounded p-8 text-center
            ring-1 ring-black/5
            dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">
    <h1 class="text-3xl font-bold mb-4 dark:text-stone-100">
        Payment Cancelled 😔
    </h1>

    <p class="text-gray-700 dark:text-stone-300 mb-6">
        Don't worry, you can try again or continue shopping!
    </p>

    <a href="{{ route('shop.index') }}"
       class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded
              dark:bg-sky-600 dark:hover:bg-sky-500">
        Back to Shop
    </a>
</div>
@endsection