@extends('layouts.admin')

@section('content')
<div class="mb-4 text-sm">
  <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">← Back to Events</a>
  <span class="text-gray-400 mx-2">•</span>
  <a href="{{ route('admin.events.stages.index', $event) }}" class="text-blue-600 hover:underline">Back to Stages</a>
</div>

<h1 class="text-2xl font-bold mb-4">Edit Stage — {{ $event->name }}</h1>

<form method="POST"
      action="{{ route('admin.events.stages.update', [$event, $stage]) }}"
      class="max-w-4xl bg-white/95 rounded-xl shadow-lg ring-1 ring-black/5 p-6 space-y-6">
  @csrf @method('PUT')

  <div class="grid md:grid-cols-6 gap-4">
    {{-- SS # --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">SS #</label>
      <input type="number" min="1" name="ss_number"
             value="{{ old('ss_number', $stage->ss_number) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30"
             required>
      @error('ss_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Name --}}
    <div class="md:col-span-2">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Name</label>
      <input name="name" value="{{ old('name', $stage->name) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="Cambyretá" required>
      @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Distance --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Distance (km)</label>
      <input type="number" step="0.1" name="distance_km"
             value="{{ old('distance_km', $stage->distance_km) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
      @error('distance_km') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Day --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Day</label>
      <select name="rally_event_day_id" id="day_select"
              class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
        <option value="">— none —</option>
        @foreach($event->days as $d)
          <option value="{{ $d->id }}"
                  data-date="{{ $d->date->toDateString() }}"
                  @selected(old('rally_event_day_id', $stage->rally_event_day_id) == $d->id)>
            {{ $d->label ?? $d->date->format('D j M') }}
          </option>
        @endforeach
      </select>
      <p class="text-[11px] text-gray-500">If not selected, we’ll try to infer from Start date.</p>
      @error('rally_event_day_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Start --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Start</label>
      <input type="datetime-local" name="start_time_local" id="start_time_local"
             value="{{ old('start_time_local', optional($stage->start_time_local)->format('Y-m-d\TH:i')) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
      @error('start_time_local') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Second pass --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second Pass</label>
      <input type="datetime-local" name="second_pass_time_local" id="second_pass_time_local"
             value="{{ old('second_pass_time_local', optional($stage->second_pass_time_local)->format('Y-m-d\TH:i')) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
      @error('second_pass_time_local') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Map image --}}
    <div class="md:col-span-3">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Map image URL</label>
      <input name="map_image_url" value="{{ old('map_image_url', $stage->map_image_url) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="/images/maps/ss1.jpg">
      @error('map_image_url') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Map embed --}}
    <div class="md:col-span-3">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Map embed URL</label>
      <input name="map_embed_url" value="{{ old('map_embed_url', $stage->map_embed_url) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="https://www.google.com/maps/d/embed?...">
      @error('map_embed_url') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Super special --}}
    <div class="md:col-span-6">
      <label class="inline-flex items-center space-x-2">
        <input type="checkbox" name="is_super_special" value="1" @checked(old('is_super_special', $stage->is_super_special))>
        <span>Super special</span>
      </label>
    </div>
  </div>

  <div class="flex items-center justify-between pt-2">
    <a href="{{ route('admin.events.stages.index', $event) }}"
       class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>

    <div class="space-x-2">
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save</button>

      <form method="POST" action="{{ route('admin.events.stages.destroy', [$event, $stage]) }}"
            class="inline" onsubmit="return confirm('Delete this stage?');">
        @csrf @method('DELETE')
        <button type="submit"
                class="px-4 py-2 rounded border border-red-300 text-red-700 hover:bg-red-50">
          Delete
        </button>
      </form>
    </div>
  </div>
</form>

{{-- Tiny helper: auto-pick Day from Start date --}}
<script>
  (function () {
    const daySelect = document.getElementById('day_select');
    const startInput = document.getElementById('start_time_local');
    startInput?.addEventListener('change', () => {
      const v = startInput.value;
      if (!v) return;
      const d = v.split('T')[0];
      const opt = [...daySelect.options].find(o => o.dataset.date === d);
      if (opt) daySelect.value = opt.value;
    });
  })();
</script>
@endsection