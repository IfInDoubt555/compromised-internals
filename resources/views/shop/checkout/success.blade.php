@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-6 bg-white shadow rounded p-8 text-center">
    <h1 class="text-3xl font-bold mb-4 text-green-600">Payment Successful 🎉</h1>
    <p class="text-gray-700 mb-6">Thank you for your purchase! Your order is being processed.</p>

    <a href="{{ route('shop.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded">
        Continue Shopping
    </a>
</div>
@endsection
