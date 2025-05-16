@extends('layouts.app')

@section('content')
<div class="prose max-w-3xl text-gray-800 mt-10 mb-10 text-lg leading-relaxed bg-white/45 backdrop-blur-md rounded-xl shadow-xl p-6 mx-auto">
    <h1 class="text-3xl font-bold mb-4">{{ $event->name }}</h1>

    @if ($event->championship)
    <p class="text-indigo-600 font-semibold mb-2">
        🏆 Part of the {{ strtoupper($event->championship) }} Championship
    </p>
    @endif

    <p class="text-gray-700 italic mb-2">
        📍 {{ $event->location ?? 'Location TBD' }}
    </p>

    <p class="text-gray-600 mb-4">
        📅 {{ $event->start_date->format('F j, Y') }}
        @if ($event->end_date)
        - {{ $event->end_date->format('F j, Y') }}
        @endif
    </p>

    <section class="text-lg leading-relaxed text-gray-800">
        {!! nl2br(e($event->description ?? 'No additional information available.')) !!}
    </section>

</div>
<div class="text-center mt-10 mb-10">
    <a href="{{ route('calendar') }}"
        class="inline-block text-blue-600 hover:text-blue-800 transition font-semibold">
        ← Back to Calendar
    </a>
</div>
@endsection