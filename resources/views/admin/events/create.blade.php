@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">➕ Add New Rally Event</h1>

    <form method="POST" action="{{ route('admin.events.store') }}">
        @csrf

        <div class="mt-4">
            <label for="championship" class="block font-medium">Championship</label>
            <select id="championship" name="championship" class="w-full border rounded px-3 py-2">
                <option value="">Select...</option>
                <option value="WRC">WRC</option>
                <option value="ARA">ARA</option>
                <option value="ERC">ERC</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Event Name</label>
            <input type="text" name="name" class="w-full border rounded p-2" required value="{{ old('name') }}">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Location</label>
            <input type="text" name="location" class="w-full border rounded p-2" required value="{{ old('location') }}">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Start Date</label>
            <input type="date" name="start_date" class="w-full border rounded p-2" required value="{{ old('start_date') }}">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">End Date</label>
            <input type="date" name="end_date" class="w-full border rounded p-2" required value="{{ old('end_date') }}">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="description" class="w-full border rounded p-2" rows="4">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="px-4 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700">
            Save Event
        </button>
    </form>
</div>
@endsection