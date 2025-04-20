@extends('layouts.app')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            const calendar = new window.FullCalendar.Calendar(calendarEl, {
                plugins: [window.FullCalendar.dayGridPlugin, window.FullCalendar.interactionPlugin],
                initialView: 'dayGridMonth',
                events: '/calendar/events',
            });

            calendar.render();
        });
    </script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Rally Calendar</h1>

    <div id="calendar" class="bg-white rounded shadow p-4"></div>

    <noscript class="text-red-500 text-center mt-4">
        Please enable JavaScript to view the event calendar.
    </noscript>
</div>
@endsection