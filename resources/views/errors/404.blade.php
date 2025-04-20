@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 px-4 text-center">
    <h1 class="text-7xl font-extrabold text-red-600 mb-2">404</h1>
    <h2 class="text-2xl font-semibold mb-4">🧭 Off Course</h2>

    <p class="text-gray-600 mb-4 max-w-md">
        The page <strong>{{ request()->path() }}</strong> could not be found. You might have taken a wrong turn somewhere.
    </p>

    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ url()->previous() }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
            ⬅️ Go Back
        </a>

        <a href="{{ route('home') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            🏠 Home
        </a>
    </div>
</div>
@endsection