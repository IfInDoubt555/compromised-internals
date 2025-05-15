@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Edit Rally Event</h1>

    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="mt-4">
            <label for="championship" class="block font-medium">Championship</label>
            <select id="championship" name="championship" class="w-full border rounded px-3 py-2">
                <option value="">Select...</option>
                <option value="WRC">WRC</option>
                <option value="ARA">ARA</option>
                <option value="ERC">ERC</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold mb-1">Event Name</label>
            <input type="text" name="name" value="{{ old('name', $event->name) }}"
                class="w-full border rounded p-2" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Location</label>
            <input type="text" name="location" value="{{ old('location', $event->location) }}"
                class="w-full border rounded p-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}"
                class="w-full border rounded p-2" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}"
                class="w-full border rounded p-2" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="description" rows="4"
                class="w-full border rounded p-2">{{ old('description', $event->description) }}</textarea>
        </div>

        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            Update Event
        </button>
    </form>
    @endsection