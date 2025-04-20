@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg text-center">
    <h1 class="text-4xl font-bold mb-4 text-red-600">💸 Checkout Unavailable</h1>

    <p class="text-lg mb-6 text-gray-700">
        We’ve blown the budget gasket! Payments are temporarily offline while we get back on track.
        Please check back soon or reach out if you need assistance.
    </p>

    <a href="{{ route('shop.index') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        🛍️ Return to Shop
    </a>
</div>
@endsection