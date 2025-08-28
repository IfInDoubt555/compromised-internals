@extends('layouts.admin')
@section('content')
<h1 class="text-2xl font-bold mb-6">Add Travel Highlight</h1>
<form action="{{ route('admin.travel-highlights.store') }}" method="POST" class="bg-white rounded shadow p-6 max-w-2xl">
  @csrf
  <label class="block mb-4">
    <span class="block font-semibold mb-1">Title</span>
    <input name="title" class="w-full border rounded px-3 py-2" required>
  </label>

  <label class="block mb-4">
    <span class="block font-semibold mb-1">URL</span>
    <input name="url" class="w-full border rounded px-3 py-2" placeholder="https://compromisedinternals.com/travel/monte-carlo" required>
  </label>

  <div class="grid grid-cols-3 gap-4 mb-4">
    <label class="block">
      <span class="block font-semibold mb-1">Event ID (optional)</span>
      <input name="event_id" type="number" class="w-full border rounded px-3 py-2" placeholder="123">
    </label>
    <label class="block">
      <span class="block font-semibold mb-1">Sort</span>
      <input name="sort_order" type="number" class="w-full border rounded px-3 py-2" value="0" min="0">
    </label>
    <label class="block">
      <span class="block font-semibold mb-1">Active</span>
      <select name="is_active" class="w-full border rounded px-3 py-2">
        <option value="1">Yes</option>
        <option value="0">No</option>
      </select>
    </label>
  </div>

  <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save</button>
</form>
@endsection