@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Days — {{ $event->name }}</h1>

<form method="POST" action="{{ route('admin.events.days.store', $event) }}">
  @csrf
  <button class="btn btn-primary">Generate from start/end dates</button>
</form>

<table class="table mt-4">
  <thead><tr><th>Date</th><th>Label</th><th></th></tr></thead>
  <tbody>
    @forelse($event->days as $day)
      <tr>
        <td>{{ $day->date->toFormattedDateString() }}</td>
        <td>{{ $day->label }}</td>
        <td class="text-right">
          <form method="POST" action="{{ route('admin.events.days.destroy', [$event,$day]) }}">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="3" class="text-gray-500">No days yet.</td></tr>
    @endforelse
  </tbody>
</table>
@endsection