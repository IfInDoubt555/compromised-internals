@extends('layouts.admin')

@section('content')
<div class="mb-4 flex items-center justify-between">
  <div class="text-sm space-x-3">
    <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">← Back to Events</a>
    <span class="text-gray-400">•</span>
    <a href="{{ route('admin.events.stages.index', $event) }}" class="text-blue-600 hover:underline">Manage Stages</a>
    @if($event->slug)
      <span class="text-gray-400">•</span>
      <a href="{{ route('calendar.show', $event->slug) }}" target="_blank" class="text-blue-600 hover:underline">View public page ↗</a>
    @endif
  </div>

  {{-- One-click generate/refresh days from event dates --}}
  <form method="POST" action="{{ route('admin.events.days.store', $event) }}"
        onsubmit="return confirm('Generate/refresh event days from the start/end dates?')">
    @csrf
    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded">
      Generate Days from Dates
    </button>
  </form>
</div>

<div class="bg-white rounded-xl shadow">
  <div class="px-5 py-3 border-b flex items-center justify-between">
    <h1 class="text-xl font-semibold">Days — {{ $event->name }}</h1>
    <span class="text-xs text-gray-500">{{ $event->days->count() }} total</span>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Date</th>
          <th class="px-4 py-2 text-left">Label</th>
          <th class="px-4 py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($event->days as $day)
          <tr class="border-t">
            <td class="px-4 py-2">{{ $day->date->toFormattedDateString() }}</td>
            <td class="px-4 py-2">{{ $day->label }}</td>
            <td class="px-4 py-2 text-right">
              <form method="POST" action="{{ route('admin.events.days.destroy', [$event, $day]) }}"
                    class="inline" onsubmit="return confirm('Delete this day? Any stages linked to it will become unassigned.');">
                @csrf @method('DELETE')
                <button class="text-red-600 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-4 py-6 text-center text-gray-500">
              No days yet. Click “Generate Days from Dates”.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection