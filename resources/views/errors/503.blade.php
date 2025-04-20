@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 px-4 text-center">
    <h1 class="text-7xl font-extrabold text-red-600 mb-2">503</h1>
    <h2 class="text-2xl font-semibold mb-4">🛠️ Scheduled Maintenance</h2>

    <p class="text-gray-600 mb-6 max-w-md">
        We're currently refueling the engine and tuning up the backend. Hang tight — we'll be back on the road shortly.
    </p>

    <a href="{{ route('home') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        🏠 Return Home
    </a>
</div>
@endsection