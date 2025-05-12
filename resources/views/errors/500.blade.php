@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-slate-200 px-4 text-center">
    <h1 class="text-7xl font-extrabold text-red-600 mb-2">500</h1>
    <h2 class="text-2xl font-semibold mb-4">💥 Something Blew a Gasket</h2>

    <p class="text-gray-600 mb-6 max-w-md">
        An unexpected error occurred under the hood. We're logging it and investigating so you can get back on track soon.
    </p>

    <a href="{{ route('home') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        🏠 Return Home
    </a>
</div>
@endsection