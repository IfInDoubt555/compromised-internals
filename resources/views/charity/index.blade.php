@extends('layouts.app')

@section('title', 'Charity Work')

@section('content')
<div class="max-w-4xl mx-auto bg-gray-300 rounded-xl py-12 px-4 mt-4 text-center">
    <h1 class="text-4xl font-bold mb-4">❤️ Rally for Mental Health</h1>

    <p class="text-lg text-gray-700 mb-6">
        At Compromised Internals, we donate a portion of all proceeds to trusted mental health organizations.
        This mission is close to our hearts — because pushing limits isn’t just for cars, it’s about people too.
    </p>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2">💰 Community Impact</h2>
        <p class="text-gray-600">
            As we grow, we’ll share running totals of donations made and the organizations we support.
        </p>
        <p class="mt-2 text-sm text-gray-500 italic">(Tracker or chart coming soon)</p>
    </div>

    <p class="mb-4 text-gray-700">
        Know a great charity you'd like to see us support?
    </p>

    <a href="/contact" class="inline-block px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-semibold">
        📬 Suggest a Charity
    </a>
</div>
@endsection