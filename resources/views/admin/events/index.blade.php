@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">Manage Rally Events</h1>

    <p class="mb-4 text-gray-600">Here you can add, edit, or delete rally events for the calendar system.</p>

    <a href="{{ route('admin.events.create') }}" class="inline-block mb-6 px-4 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
        ➕ Add New Event
    </a>

    <!-- Placeholder for event list -->
    <div class="bg-white shadow rounded p-4">
        <p class="text-center text-gray-500">Events will be listed here soon...</p>
    </div>
</div>
@endsection