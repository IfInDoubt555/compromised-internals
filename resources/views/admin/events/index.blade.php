@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">Manage Rally Events</h1>
<p class="mb-4 text-gray-600">Here you can add, edit, or delete rally events for the calendar system.</p>

<a href="{{ route('admin.events.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-6 inline-block">
    ➕ Add New Event
</a>

@if ($events->count())
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm text-left border border-gray-200">
        <thead class="bg-gray-100 font-semibold text-gray-800">
            <tr>
                <th class="px-4 py-2 border-b">Event Name</th>
                <th class="px-4 py-2 border-b">Location</th>
                <th class="px-4 py-2 border-b">Championship</th>
                <th class="px-4 py-2 border-b">Start Date</th>
                <th class="px-4 py-2 border-b">End Date</th>
                <th class="px-4 py-2 border-b text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b">{{ $event->name }}</td>
                <td class="px-4 py-2 border-b">{{ $event->location }}</td>
                <td class="px-4 py-2 border-b">{{ $event->championship ?? '-' }}</td>
                <td class="px-4 py-2 border-b">{{ $event->start_date->format('M j, Y') }}</td>
                <td class="px-4 py-2 border-b">{{ $event->end_date->format('M j, Y') }}</td>
                <td class="px-4 py-2 border-b text-right space-x-2">
                    <a href="{{ route('admin.events.edit', $event->id) }}"
                        class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                        class="inline-block"
                        onsubmit="return confirm('Are you sure you want to delete this event?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $events->links() }}
</div>
@else
<p class="text-gray-500 mt-6">No rally events found.</p>
@endif
@endsection