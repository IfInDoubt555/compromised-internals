@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">{{ $event->name }}</h1>
    <p class="text-gray-700 italic mb-2">{{ $event->location ?? 'Location TBD' }}</p>
    <p class="text-gray-600 mb-4">
        {{ \Carbon\Carbon::parse($event->start_date)->format('F j, Y') }}
        @if($event->end_date)
            - {{ \Carbon\Carbon::parse($event->end_date)->format('F j, Y') }}
        @endif
    </p>
    <p class="text-lg leading-relaxed">{{ $event->description ?? 'No additional info.' }}</p>
</div>
@endsection
