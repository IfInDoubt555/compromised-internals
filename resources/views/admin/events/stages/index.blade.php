@extends('layouts.admin')

@section('content')
@php
  $next = (($event->stages->max('ss_number')) ?? 0) + 1;
@endphp

<div class="max-w-7xl mx-auto">
  <div class="mb-6 text-sm">
    <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">← Back to Events</a>
    <span class="text-gray-400 mx-2">•</span>
    <a href="{{ route('admin.events.days.index', $event) }}" class="text-blue-600 hover:underline">Manage Days</a>
  </div>

  <h1 class="text-2xl font-bold tracking-tight">Stages — {{ $event->name }}</h1>
  <p class="text-sm text-gray-500 mt-1 mb-8">
    Add stages below. Assign a Day or just set a start time — the Day will auto-select if the date matches.
  </p>

  <div class="grid xl:grid-cols-12 gap-8">
    {{-- LEFT: CREATE FORM --}}
    <div class="xl:col-span-8">
      <form method="POST" action="{{ route('admin.events.stages.store', $event) }}"
            class="bg-white rounded-2xl shadow-lg ring-1 ring-black/5 p-6 space-y-8">
        @csrf

        {{-- SECTION: DETAILS --}}
        <section>
          <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Details</h2>
          <div class="grid md:grid-cols-6 gap-5">
            {{-- STAGE TYPE --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Type</label>
              <select name="stage_type" id="stage_type"
                      class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
                <option value="SS" {{ old('stage_type','SS')==='SS' ? 'selected' : '' }}>SS</option>
                <option value="SD" {{ old('stage_type')==='SD' ? 'selected' : '' }}>SD (Shakedown)</option>
              </select>
            </div>

            {{-- SS NUMBER --}}
            <div id="ss_number_wrap" class="md:col-span-1">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">SS #</label>
              <input name="ss_number" type="number" min="1" value="{{ old('ss_number', $next) }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30"
                     required>
              @error('ss_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- NAME --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Name</label>
              <input name="name" value="{{ old('name') }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
                     placeholder="Cambyretá" required>
              @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- DISTANCE --}}
            <div class="md:col-span-1">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Distance (km)</label>
              <input name="distance_km" type="number" step="0.1" value="{{ old('distance_km') }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
              @error('distance_km') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>
        </section>

        {{-- SECTION: TIMING --}}
        <section>
          <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Timing</h2>
          <div class="grid md:grid-cols-6 gap-5">
            {{-- DAY --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Day</label>
              <select name="rally_event_day_id" id="day_select"
                      class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
                <option value="">— none —</option>
                @foreach($event->days as $d)
                  <option value="{{ $d->id }}"
                          data-date="{{ $d->date->toDateString() }}"
                          @selected(old('rally_event_day_id') == $d->id)>
                    {{ $d->label ?? $d->date->format('D j M') }}
                  </option>
                @endforeach
              </select>
              <p class="text-[11px] text-gray-500">If not selected, we’ll try to infer from Start date.</p>
              @error('rally_event_day_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- START --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Start</label>
              <input type="datetime-local" name="start_time_local" id="start_time_local"
                     value="{{ old('start_time_local') }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
                     placeholder="2025-08-29 08:03">
              @error('start_time_local') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- SECOND PASS --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second Pass</label>
              <input type="datetime-local" name="second_pass_time_local" id="second_pass_time_local"
                     value="{{ old('second_pass_time_local') }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
              @error('second_pass_time_local') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>
        </section>

        {{-- SECTION: SECOND RUN --}}
        <section>
          <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Second run (optional)</h2>
          <div class="grid md:grid-cols-6 gap-5">
            {{-- SECOND SS # --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second SS #</label>
              <input type="number" min="1" name="second_ss_number" value="{{ old('second_ss_number') }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
              @error('second_ss_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- SECOND DAY --}}
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Second Day</label>
              <select name="second_rally_event_day_id" id="second_day_select"
                      class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30">
                <option value="">— auto from time —</option>
                @foreach($event->days as $d)
                  <option value="{{ $d->id }}" data-date="{{ $d->date->toDateString() }}"
                          @selected(old('second_rally_event_day_id') == $d->id)>
                    {{ $d->label ?? $d->date->format('D j M') }}
                  </option>
                @endforeach
              </select>
              @error('second_rally_event_day_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>
        </section>

        {{-- SECTION: MEDIA --}}
        <section>
          <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Media</h2>
          <div class="grid md:grid-cols-6 gap-5">
            {{-- MAP IMAGE --}}
            <div class="md:col-span-6">
              <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Map image URL</label>
              <input name="map_image_url" value="{{ old('map_image_url') }}"
                     class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
                     placeholder="/images/maps/ss1.jpg">
              @error('map_image_url') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-between pt-2">
          <label class="inline-flex items-center space-x-2">
            <input type="checkbox" name="is_super_special" value="1" @checked(old('is_super_special'))>
            <span class="text-sm">Super special</span>
          </label>

          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Add Stage
          </button>
        </div>
      </form>
    </div>

    {{-- RIGHT: EXISTING STAGES (sticky on xl) --}}
    <aside class="xl:col-span-4">
      <div class="bg-white rounded-2xl shadow-lg ring-1 ring-black/5 xl:sticky xl:top-6">
        <div class="px-5 py-4 border-b flex items-center justify-between">
          <h2 class="font-semibold">Existing Stages</h2>
          <span class="text-xs text-gray-500">{{ $event->stages->count() }} total</span>
        </div>

        <div class="divide-y">
          @forelse($event->stages as $ss)
            <div class="px-5 py-3 flex items-start justify-between gap-4">
              <div>
                <div class="text-sm font-semibold">
                  @if($ss->stage_type === 'SD')
                    SD
                  @else
                    SS {{ $ss->ss_number }}@if($ss->second_ss_number)/{{ $ss->second_ss_number }}@endif
                  @endif
                  @if($ss->is_super_special)
                    <span class="ml-1 text-[11px] text-purple-700 font-semibold">/S</span>
                  @endif
                </div>
                <div class="text-sm text-gray-700">{{ $ss->name }}</div>
                <div class="text-xs text-gray-500">
                  {{ optional($ss->day)->label ?? '—' }} ·
                  {{ optional($ss->start_time_local)->format('M d H:i') ?? '—' }} ·
                  {{ $ss->distance_km ? number_format($ss->distance_km,1).' km' : '—' }}
                </div>
              </div>
              <div class="shrink-0 text-right space-x-3">
                <a href="{{ route('admin.events.stages.edit', [$event, $ss]) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                <form method="POST"
                      action="{{ route('admin.events.stages.destroy', [$event, $ss]) }}"
                      class="inline"
                      onsubmit="return confirm('Delete this stage?')">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:underline text-sm">Delete</button>
                </form>
              </div>
            </div>
          @empty
            <div class="px-5 py-6 text-center text-gray-500 text-sm">No stages yet. Add your first one.</div>
          @endforelse
        </div>
      </div>
    </aside>
  </div>
</div>

{{-- Small helper script --}}
<script>
  (function () {
    const typeSel     = document.getElementById('stage_type');
    const ssInput     = document.querySelector('input[name="ss_number"]');
    const ssWrap      = document.getElementById('ss_number_wrap');
    const daySelect   = document.getElementById('day_select');
    const startInput  = document.getElementById('start_time_local');
    const secondTime  = document.getElementById('second_pass_time_local');
    const secondDay   = document.getElementById('second_day_select');

    // Toggle SS input when Type = SD
    const toggleSS = () => {
      const isSD = typeSel?.value === 'SD';
      if (ssInput) {
        ssInput.disabled = !!isSD;
        if (isSD) ssInput.removeAttribute('required'); else ssInput.setAttribute('required','required');
      }
      if (ssWrap) ssWrap.style.opacity = isSD ? '0.6' : '1';
    };
    typeSel?.addEventListener('change', toggleSS);
    toggleSS();

    // Auto-select matching Day when Start changes
    startInput?.addEventListener('change', () => {
      const v = startInput.value;
      if (!v || !daySelect) return;
      const d = v.split('T')[0];
      const opt = [...daySelect.options].find(o => o.dataset.date === d);
      if (opt) daySelect.value = opt.value;
    });

    // If Day chosen and Start empty, seed sensible times
    daySelect?.addEventListener('change', () => {
      const opt = daySelect.options[daySelect.selectedIndex];
      if (!opt?.dataset?.date) return;
      const base = opt.dataset.date;
      if (!startInput.value) startInput.value = base + 'T08:00';
      if (!secondTime.value) secondTime.value = base + 'T13:00';
    });

    // Auto-select Second Day from Second Pass time
    secondTime?.addEventListener('change', () => {
      const v = secondTime.value;
      if (!v || !secondDay) return;
      const d = v.split('T')[0];
      const opt = [...secondDay.options].find(o => o.dataset.date === d);
      if (opt) secondDay.value = opt.value;
    });
  })();
</script>
@endsection