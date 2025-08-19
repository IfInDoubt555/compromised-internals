@extends('layouts.admin')

@section('content')
@php
  // Prefer the sorted list the controller passed; fall back to the relation.
  $daysList = isset($days) ? $days : $event->days()->orderBy('date')->get();
@endphp

<div class="mb-4 text-sm">
  <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">← Back to Events</a>
  <span class="text-gray-400 mx-2">•</span>
  <a href="{{ route('admin.events.stages.index', $event) }}" class="text-blue-600 hover:underline">Back to Stages</a>
</div>

<h1 class="text-2xl font-bold mb-4">Edit Stage — {{ $event->name }}</h1>

<form method="POST"
      action="{{ route('admin.events.stages.update', [$event, $stage]) }}"
      class="max-w-6xl bg-white/95 rounded-xl shadow-lg ring-1 ring-black/5 p-6 space-y-4">
  @csrf @method('PUT')

  <div class="grid md:grid-cols-6 gap-4">
    {{-- TYPE --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Type</label>
      <select name="stage_type" id="stage_type"
              class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
        <option value="SS" {{ old('stage_type', $stage->stage_type ?? 'SS') === 'SS' ? 'selected' : '' }}>SS</option>
        <option value="SD" {{ old('stage_type', $stage->stage_type ?? 'SS') === 'SD' ? 'selected' : '' }}>SD (Shakedown)</option>
      </select>
      @error('stage_type') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- SS # (disabled when Type=SD via JS) --}}
    <div id="ss_number_wrap">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">SS #</label>
      <input type="number" min="1" name="ss_number"
             value="{{ old('ss_number', $stage->ss_number) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30">
      @error('ss_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- NAME --}}
    <div class="md:col-span-2">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Name</label>
      <input name="name" value="{{ old('name', $stage->name) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="Cambyretá" required>
      @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- DISTANCE --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Distance (km)</label>
      <input type="number" step="0.1" name="distance_km"
             value="{{ old('distance_km', $stage->distance_km) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
      @error('distance_km') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- DAY --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Day</label>
      <select name="rally_event_day_id" id="day_select"
              class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
        <option value="">— none —</option>
        @foreach($daysList as $d)
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

    {{-- START --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Start</label>
      <input type="datetime-local" name="start_time_local" id="start_time_local"
             value="{{ old('start_time_local', optional($stage->start_time_local)->format('Y-m-d\TH:i')) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="2025-08-29 08:03">
      @error('start_time_local') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- SECOND PASS --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second Pass</label>
      <input type="datetime-local" name="second_pass_time_local" id="second_pass_time_local"
             value="{{ old('second_pass_time_local', optional($stage->second_pass_time_local)->format('Y-m-d\TH:i')) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
      @error('second_pass_time_local') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- SECOND SS # --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second SS #</label>
      <input type="number" min="1" name="second_ss_number"
             value="{{ old('second_ss_number', $stage->second_ss_number) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
      @error('second_ss_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- SECOND DAY --}}
    <div>
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second Day</label>
      <select name="second_rally_event_day_id" id="second_day_select"
              class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
        <option value="">— auto from time —</option>
        @foreach($daysList as $d)
          <option value="{{ $d->id }}"
                  data-date="{{ $d->date->toDateString() }}"
                  @selected(old('second_rally_event_day_id', $stage->second_rally_event_day_id) == $d->id)>
            {{ $d->label ?? $d->date->format('D j M') }}
          </option>
        @endforeach
      </select>
      @error('second_rally_event_day_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- MAP IMAGE --}}
    <div class="md:col-span-3">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Map image URL</label>
      <input name="map_image_url" value="{{ old('map_image_url', $stage->map_image_url) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="/images/maps/ss1.jpg">
      @error('map_image_url') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- MAP EMBED --}}
    <div class="md:col-span-3">
      <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Map embed URL</label>
      <input name="map_embed_url" value="{{ old('map_embed_url', $stage->map_embed_url) }}"
             class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
             placeholder="https://www.google.com/maps/d/embed?...">
      @error('map_embed_url') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div class="flex items-center justify-between pt-2">
    <label class="inline-flex items-center space-x-2">
      <input type="checkbox" name="is_super_special" value="1" @checked(old('is_super_special', $stage->is_super_special))>
      <span>Super special</span>
    </label>

    <div class="space-x-2">
      <a href="{{ route('admin.events.stages.index', $event) }}"
         class="px-4 py-2 rounded border border-gray-300 hover:bg-gray-50">Cancel</a>

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

{{-- Tiny helpers (fallback if stages.js isn’t loaded) --}}
<script>
  (function () {
    const typeSel    = document.getElementById('stage_type');
    const ssWrap     = document.getElementById('ss_number_wrap');
    const ssInput    = document.querySelector('input[name="ss_number"]');
    const daySelect  = document.getElementById('day_select');
    const startInput = document.getElementById('start_time_local');
    const secondDay  = document.getElementById('second_day_select');
    const secondTime = document.getElementById('second_pass_time_local');

    const toggleSS = () => {
      const isSD = typeSel && typeSel.value === 'SD';
      if (ssInput) ssInput.disabled = !!isSD;
      if (ssWrap)  ssWrap.style.opacity = isSD ? '0.6' : '1';
    };
    typeSel?.addEventListener('change', toggleSS);
    toggleSS();

    startInput?.addEventListener('change', () => {
      if (!daySelect || !startInput.value) return;
      const d = startInput.value.split('T')[0];
      const opt = [...daySelect.options].find(o => o.dataset.date === d);
      if (opt) daySelect.value = opt.value;
    });

    daySelect?.addEventListener('change', () => {
      const opt = daySelect.options[daySelect.selectedIndex];
      const base = opt?.dataset?.date;
      if (!base) return;
      if (startInput && !startInput.value) startInput.value = `${base}T08:00`;
      if (secondTime && !secondTime.value) secondTime.value = `${base}T13:00`;
    });

    secondTime?.addEventListener('change', () => {
      if (!secondDay || !secondTime.value) return;
      const d = secondTime.value.split('T')[0];
      const opt = [...secondDay.options].find(o => o.dataset.date === d);
      if (opt) secondDay.value = opt.value;
    });
  })();
</script>
@endsection