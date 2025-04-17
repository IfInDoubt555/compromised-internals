@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-red-600">500</h1>
        <h2 class="text-2xl mt-4 mb-6">Server Error</h2>

        <a href="{{ route('home') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Go Home
        </a>
    </div>
</div>
@endsection
