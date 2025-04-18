@extends('layouts.app')

@section('content')
@php
    $tab = request()->query('tab', 'events'); // default to events if missing
    $decade = request()->route('decade');     // pulled from the route
@endphp

<div id="theme-wrapper" class="theme-wrapper decade-{{ $decade }}">
<div class="max-w-4xl mx-auto px-4 py-10">
    <!-- Title -->
    <h1 class="text-4xl font-extrabold text-center mb-8">
        {{ $event['title'] ?? $event['name'] ?? 'Untitled' }}
    </h1>

    <!-- Image -->
    @if (!empty($event['image']))
        <div class="flex justify-center">
            <img src="{{ asset($event['image']) }}" alt="{{ $event['title'] ?? $event['name'] ?? 'Image' }}" 
                class="rounded-2xl shadow-lg hover:scale-105 transition-transform duration-300 ease-in-out max-h-[600px] object-cover">
        </div>
    @endif

    <!-- Details / Summary -->
    <div class="prose max-w-none text-gray-700 mt-10 text-lg leading-relaxed">
        {!! $event['details_html'] ?? $event['summary'] ?? $event['bio'] ?? 'No additional details available.' !!}
    </div>

    <!-- Back Link -->
    <div class="mt-10 text-center">
        <a href="{{ route('history.index') }}?decade={{ $decade }}&tab={{ $tab }}" 
            class="inline-block text-blue-600 hover:text-blue-800 underline transition">
            ← Back to History Timeline
        </a>
    </div>
</div>
@endsection
