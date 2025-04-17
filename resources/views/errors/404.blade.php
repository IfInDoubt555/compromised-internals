@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
    <div class="text-center">
        <p class="text-gray-700 mb-6">
            The page <strong>{{ request()->path() }}</strong> could not be found.
        </p>
        <h1 class="text-6xl font-bold text-red-600">404</h1>
        <h2 class="text-2xl mt-4 mb-6">Page Not Found</h2>

        <a href="{{ route('home') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            Go Home
        </a>
    </div>
</div>
@endsection
