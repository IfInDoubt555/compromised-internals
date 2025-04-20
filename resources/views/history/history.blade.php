@extends('layouts.app')

@section('content')
<div id="theme-wrapper" class="min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto text-center mb-8">
        <h1 class="text-4xl font-bold mb-2">🏁 Rally History Archive</h1>
        <p class="text-lg text-gray-700">
            Explore rally legends, iconic cars, and pivotal events from the 1960s to today.
            Use the slider below to pick a decade and switch between timelines.
        </p>
    </div>

    <div id="slider" class="w-full sm:w-2/3 max-w-xl mx-auto my-6"></div>
    <div id="selected-decade-title" class="text-xl font-semibold text-center mb-4"></div>

    <div class="flex justify-center gap-4 mb-6">
        <button class="tab-btn px-4 py-2 bg-gray-300 rounded hover:bg-gray-400" data-tab="events">Events</button>
        <button class="tab-btn px-4 py-2 bg-gray-300 rounded hover:bg-gray-400" data-tab="cars">Cars</button>
        <button class="tab-btn px-4 py-2 bg-gray-300 rounded hover:bg-gray-400" data-tab="drivers">Drivers</button>
    </div>

    <div class="text-center">
        <button id="view-button" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">View History</button>
    </div>

    <div id="history-content" class="mt-10 space-y-6"></div>

    <noscript>
        <p class="text-center text-red-600 font-medium mt-8">
            ⚠️ JavaScript is required to view the interactive timeline. Please enable it in your browser.
        </p>
    </noscript>
</div>

@vite('resources/js/history.js')
@endsection