@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-bold mb-4">Stages — {{ $event->name }}</h1>

{{-- Create --}}
<form method="POST" action="{{ route('admin.events.stages.store', $event) }}" class="space-y-3 bg-white rounded shadow p-4">
  @csrf
  <div class="grid md:grid-cols-6 gap-3">
    <div class="md:col-span-1">
      <label class="block text-sm">SS #</label>
      <input name="ss_number" type="number" min="1" class="form-input w-full" required>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm">Name</label>
      <input name="name" class="form-input w-full" required>
    </div>
    <div>
      <label class="block text-sm">Distance (km)</label>
      <input name="distance_km" type="number" step="0.1" class="form-input w-full">
    </div>
    <div>
      <label class="block text-sm">Day</label>
      <select name="rally_event_day_id" class="form-select w-full">
        <option value="">— none —</option>
        @foreach($event->days as $d)
          <option value="{{ $d->id }}">{{ $d->label ?? $d->date->format('D j M') }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm">Start</label>
      <input type="datetime-local" name="start_time_local" class="form-input w-full">
    </div>
    <div>
      <label class="block text-sm">Second Pass</label>
      <input type="datetime-local" name="second_pass_time_local" class="form-input w-full">
    </div>

    <div class="md:col-span-3">
      <label class="block text-sm">Map image URL</label>
      <input name="map_image_url" class="form-input w-full">
    </div>
    <div class="md:col-span-3">
      <label class="block text-sm">Map embed URL</label>
      <input name="map_embed_url" class="form-input w-full">
    </div>
  </div>

  <label class="inline-flex items-center space-x-2">
    <input type="checkbox" name="is_super_special" value="1">
    <span>Super special</span>
  </label>

  <div>
    <button class="btn btn-primary">Add Stage</button>
  </div>
</form>

{{-- List --}}
<table class="table mt-6">
  <thead>
    <tr>
      <th>SS</th><th>Name</th><th>Day</th><th>Start</th><th>Distance</th><th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($event->stages as $ss)
      <tr>
        <td>{{ $ss->ss_number }}@if($ss->is_super_special)/S @endif</td>
        <td>{{ $ss->name }}</td>
        <td>{{ optional($ss->day)->label ?? '—' }}</td>
        <td>{{ optional($ss->start_time_local)->format('M d H:i') }}</td>
        <td>{{ $ss->distance_km ? number_format($ss->distance_km,1) . ' km' : '—' }}</td>
        <td class="text-right space-x-2">
          <a class="text-blue-600 hover:underline" href="{{ route('admin.events.stages.edit', [$event,$ss]) }}">Edit</a>
          <form method="POST" action="{{ route('admin.events.stages.destroy', [$event,$ss]) }}" class="inline">
            @csrf @method('DELETE')
            <button class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
@endsection