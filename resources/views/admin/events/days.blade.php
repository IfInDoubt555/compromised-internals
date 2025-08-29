@extends('layouts.admin')

@section('content')
<div class="mb-4 flex items-center justify-between">
  <div class="text-sm space-x-3">
    <a href="{{ route('admin.events.index') }}" class="ci-link">← Back to Events</a>
    <span class="ci-sep">•</span>
    <a href="{{ route('admin.events.stages.index', $event) }}" class="ci-link">Manage Stages</a>
    @if($event->slug)
      <span class="ci-sep">•</span>
      <a href="{{ route('calendar.show', $event->slug) }}" target="_blank" class="ci-link">View public page ↗</a>
    @endif
  </div>

  {{-- One-click generate/refresh days from event dates --}}
  <form method="POST" action="{{ route('admin.events.days.store', $event) }}"
        onsubmit="return confirm('Generate/refresh event days from the start/end dates?')">
    @csrf
    <button class="ci-btn-primary">
      Generate Days from Dates
    </button>
  </form>
</div>

<div class="ci-admin-card p-0">
  <div class="px-5 py-3 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
    <h1 class="text-xl font-semibold">Days — {{ $event->name }}</h1>
    <span class="text-xs ci-muted">{{ $event->days->count() }} total</span>
  </div>

  <div class="overflow-x-auto">
    <table class="ci-table">
      <thead class="ci-thead">
        <tr>
          <th class="ci-th">Date</th>
          <th class="ci-th">Label</th>
          <th class="ci-th text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($event->days as $day)
          <tr class="ci-tr">
            <td class="ci-td">{{ $day->date->toFormattedDateString() }}</td>
            <td class="ci-td">{{ $day->label }}</td>
            <td class="ci-td text-right">
              <form method="POST" action="{{ route('admin.events.days.destroy', [$event, $day]) }}"
                    class="inline"
                    onsubmit="return confirm('Delete this day? Any stages linked to it will become unassigned.');">
                @csrf @method('DELETE')
                <button class="ci-btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="ci-td text-center">
              <p class="ci-muted py-4">No days yet. Click “Generate Days from Dates”.</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection