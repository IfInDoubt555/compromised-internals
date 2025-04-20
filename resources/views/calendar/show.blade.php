@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">{{ $event->name }}</h1>

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
        {{ $event->description ?? 'No additional information available.' }}
    </section>
</div>
@endsection