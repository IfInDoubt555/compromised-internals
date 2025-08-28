@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-6">Plan Your Trip – Highlights</h1>

<a href="{{ route('admin.travel-highlights.create') }}"
   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">➕ Add Highlight</a>

@if (session('status'))
  <p class="mt-4 text-green-700">{{ session('status') }}</p>
@endif

<div class="mt-6 bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 text-left">Title</th>
        <th class="px-4 py-2 text-left">URL</th>
        <th class="px-4 py-2 text-center">Sort</th>
        <th class="px-4 py-2 text-center">Active</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($highlights as $h)
        <tr class="border-t">
          <td class="px-4 py-2">{{ $h->title }}</td>
          <td class="px-4 py-2">
            <a href="{{ $h->url }}" class="text-blue-600 underline" target="_blank" rel="noopener">{{ $h->url }}</a>
          </td>
          <td class="px-4 py-2 text-center">{{ $h->sort_order }}</td>
          <td class="px-4 py-2 text-center">
            <span class="px-2 py-1 rounded text-white {{ $h->is_active ? 'bg-green-600' : 'bg-gray-400' }}">
              {{ $h->is_active ? 'Yes' : 'No' }}
            </span>
          </td>
          <td class="px-4 py-2 text-right">
            <a href="{{ route('admin.travel-highlights.edit', $h) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
            <form action="{{ route('admin.travel-highlights.destroy', $h) }}" method="POST" class="inline">
              @csrf @method('DELETE')
              <button class="text-red-600 hover:underline" onclick="return confirm('Delete highlight?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="px-4 py-6 text-gray-500" colspan="5">No highlights yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection