@extends('layouts.app')

@section('content')
@php
    $tab = request()->route('tab') ?? 'events';
    $decade = request()->route('decade');
@endphp

<div id="theme-wrapper" class="theme-wrapper decade-{{ $decade }}">
    <div class="max-w-4xl mx-auto px-4 py-10">
        
        {{-- Title --}}
        <h1 class="text-3xl font-extrabold text-center mb-8">
            {{ $item['title'] ?? $item['name'] ?? 'Untitled' }}
        </h1>

        {{-- Image --}}
        @if (!empty($item['image']))
            <div class="flex justify-center">
                <img 
                    src="{{ asset($item['image']) }}" 
                    alt="{{ $item['title'] ?? $item['name'] ?? 'Image' }}"
                    class="rounded-xl shadow-lg hover:scale-105 transition-transform duration-300 ease-in-out max-h-[600px] object-cover"
                >
            </div>
        @endif

        {{-- Details --}}
        <div class="prose max-w-none text-gray-700 mt-10 text-lg leading-relaxed">
            {!! $item['details_html'] ?? $item['summary'] ?? $item['description'] ?? $item['bio'] ?? 'No additional details available.' !!}
        </div>

        {{-- Back Link --}}
        <div class="mt-10 text-center">
            <a 
                href="{{ route('history.index') }}?decade={{ $decade }}&tab={{ $tab }}" 
                class="inline-block text-blue-600 hover:text-blue-800 underline transition"
            >
                ← Back to History Timeline
            </a>
        </div>
    </div>
</div>
@endsection
