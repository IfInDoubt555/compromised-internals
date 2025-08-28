@extends('layouts.admin')
@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Travel Highlight</h1>
<form action="{{ route('admin.travel-highlights.update', $h) }}" method="POST" class="bg-white rounded shadow p-6 max-w-2xl">
  @csrf @method('PUT')
  <label class="block mb-4">
    <span class="block font-semibold mb-1">Title</span>
    <input name="title" class="w-full border rounded px-3 py-2" value="{{ old('title',$h->title) }}" required>
  </label>
  <label class="block mb-4">
    <span class="block font-semibold mb-1">URL</span>
    <input name="url" class="w-full border rounded px-3 py-2" value="{{ old('url',$h->url) }}" required>
  </label>

  <div class="grid grid-cols-3 gap-4 mb-4">
    <label class="block">
      <span class="block font-semibold mb-1">Event ID (optional)</span>
      <input name="event_id" type="number" class="w-full border rounded px-3 py-2" value="{{ old('event_id',$h->event_id) }}">
    </label>
    <label class="block">
      <span class="block font-semibold mb-1">Sort</span>
      <input name="sort_order" type="number" class="w-full border rounded px-3 py-2" value="{{ old('sort_order',$h->sort_order) }}" min="0">
    </label>
    <label class="block">
      <span class="block font-semibold mb-1">Active</span>
      <select name="is_active" class="w-full border rounded px-3 py-2">
        <option value="1" @selected($h->is_active)>Yes</option>
        <option value="0" @selected(!$h->is_active)>No</option>
      </select>
    </label>
  </div>

  <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
</form>
@endsection