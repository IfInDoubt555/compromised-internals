@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-white shadow rounded p-8 text-center">
    <h1 class="text-3xl font-bold mb-4">Payment Cancelled 😔</h1>
    <p class="text-gray-700 mb-6">Don't worry, you can try again or continue shopping!</p>

    <a href="{{ route('shop.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded">
        Back to Shop
    </a>
</div>
@endsection
