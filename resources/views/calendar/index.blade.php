@extends('layouts.app')


@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Rally Calendar</h1>

    <div id="calendar" class="bg-white rounded shadow p-4"></div>

    <noscript class="text-red-500 text-center mt-4">
        Please enable JavaScript to view the event calendar.
    </noscript>
</div>
@endsection