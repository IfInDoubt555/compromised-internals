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
        <div class="prose max-w-none text-gray-800 mt-10 text-lg leading-relaxed bg-white/80 backdrop-blur-md rounded-xl shadow-xl p-6">
            @if (!empty($item['details_html']))
                {!! $item['details_html'] !!}
            @elseif (!empty($item['description']))
                {!! $item['description'] !!}
            @else
                @php
                    $details = $item['summary'] ?? $item['bio'] ?? null;
                    $paragraphs = $details ? explode("\n", $details) : [];
                @endphp

                @if (count($paragraphs))
                    @foreach ($paragraphs as $para)
                        @if (trim($para) !== '')
                            <p class="mb-4">{{ $para }}</p>
                        @endif
                    @endforeach
                @else
                    <p>No additional details available.</p>
                @endif
            @endif
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

        <div class="mt-4 flex justify-between items-center text-center">
            {{-- Previous Button --}}
            @if ($previousItem)
                <a 
                    href="{{ route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $previousItem['id']]) }}" 
                    class="inline-flex items-center gap-2 text-red-600 hover:text-red-800 transition font-medium"
                >
                    ← Previous Event
                </a>
            @else
                <div></div>
            @endif

            {{-- Next Button --}}
            @if ($nextItem)
                <a 
                    href="{{ route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $nextItem['id']]) }}" 
                    class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 transition font-medium"
                >
                    Next Event →
                </a>
            @else
                <div></div>
            @endif
        </div>
    </div>
</div>
@endsection
