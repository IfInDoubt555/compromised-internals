@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-2">Manage Rally Events</h1>
<p class="mb-4 ci-muted">
  Here you can add, edit, or delete rally events for the calendar system.
</p>

<a href="{{ route('admin.events.create') }}" class="ci-btn-success mb-6 inline-block">
  ➕ Add New Event
</a>

@if ($events->count())
  <div class="ci-table-wrap">
    <table class="ci-table text-left">
      <colgroup>
        <col class="w-[28%]">
        <col class="w-[24%]">
        <col class="w-[12%]">
        <col class="w-[14%]">
        <col class="w-[14%]">
        <col class="w-[8%]">
      </colgroup>

      <thead class="ci-thead">
        <tr>
          <th class="ci-th">Event Name</th>
          <th class="ci-th">Location</th>
          <th class="ci-th">Championship</th>
          <th class="ci-th">Start Date</th>
          <th class="ci-th">End Date</th>
          <th class="ci-th text-right">Actions</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($events as $event)
          <tr class="ci-tr">
            <td class="ci-td max-w-[28ch] truncate" title="{{ $event->name }}">
              {{ $event->name }}
            </td>

            <td class="ci-td max-w-[26ch] truncate" title="{{ $event->location }}">
              {{ $event->location }}
            </td>

            <td class="ci-td">
              {{ $event->championship ?? '—' }}
            </td>

            <td class="ci-td whitespace-nowrap">
              {{ optional($event->start_date)->format('M j, Y') }}
            </td>

            <td class="ci-td whitespace-nowrap">
              {{ optional($event->end_date)->format('M j, Y') }}
            </td>

            <td class="ci-td text-right whitespace-nowrap">
              <a href="{{ route('admin.events.days.index', $event) }}" class="ci-link">Days</a>
              <span class="ci-sep">·</span>
              <a href="{{ route('admin.events.stages.index', $event) }}" class="ci-link">Stages</a>
              <span class="ci-sep">·</span>
              <a href="{{ route('admin.events.edit', $event) }}" class="ci-link">Edit</a>
              <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                    class="inline-block ml-2"
                    onsubmit="return confirm('Are you sure you want to delete this event?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="ci-pagination">
    {{ $events->links() }}
  </div>
@else
  <p class="ci-muted mt-6">No rally events found.</p>
@endif
@endsection