@extends('layouts.app')

@section('content')
@php
$tab = request()->route('tab') ?? 'events';
$decade = request()->route('decade');
@endphp

<div id="theme-wrapper" class="theme-wrapper decade-{{ $decade }}">
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- Title --}}
        <div class="max-w-fit mx-auto">
            <h1 class="text-3xl backdrop-blur-md bg-white/30 dark:bg-white/40 rounded-xl px-6 py-4 shadow-xl font-extrabold text-center mb-8">
                {{ $item['title'] ?? $item['name'] ?? 'Untitled' }}
            </h1>
        </div>

        {{-- Details --}}
        <div class="prose max-w-none text-gray-800 mt-10 text-lg leading-relaxed bg-white/45 backdrop-blur-md rounded-xl shadow-xl p-6">
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

        @php
        $label = match ($tab) {
        'drivers' => 'Driver',
        'cars' => 'Car',
        default => 'Event',
        };
        @endphp

        <div class="mt-10 relative z-10">
            <div class="
              backdrop-blur-md bg-white/30 dark:bg-white/40 rounded-xl px-6 py-4 shadow-xl 
              flex flex-col space-y-4 items-center 
              md:flex-row md:space-y-0 md:space-x-6 md:justify-between
              max-w-4xl mx-auto
            ">
                {{-- Previous --}}
                @if ($nextItem)
                <a href="{{ route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $nextItem['id']]) }}"
                    class="text-red-800 hover:text-red-950 transition font-medium">
                    ← Previous {{ $label }}
                </a>

                @else
                <div></div>
                @endif

                {{-- Back --}}
                <a href="{{ route('history.index') }}?decade={{ $decade }}&tab={{ $tab }}"
                    class="text-blue-600 hover:text-blue-800 underline transition font-medium">
                    Back to {{ ucfirst($label) }} Timeline
                </a>

                {{-- Next --}}
                @if ($previousItem)
                <a href="{{ route('history.show', ['tab' => $tab, 'decade' => $decade, 'id' => $previousItem['id']]) }}"
                    class="text-green-800 hover:text-green-950 transition font-medium">
                    Next {{ $label }} →
                </a>
                @else
                <div></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection