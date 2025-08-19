@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">← Back to Events</a>
    <div class="text-sm space-x-3">
      @if($event->slug)
        <a href="{{ route('calendar.show', $event->slug) }}" class="text-blue-600 hover:underline" target="_blank">View public page ↗</a>
      @endif
      <a href="{{ route('admin.events.days.index', $event) }}" class="text-blue-600 hover:underline">Manage Days</a>
      <a href="{{ route('admin.events.stages.index', $event) }}" class="text-blue-600 hover:underline">Manage Stages</a>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Main form --}}
    <div class="md:col-span-2 bg-white rounded-xl shadow p-6">
      <h1 class="text-2xl font-bold mb-6">Edit Rally Event</h1>

      <form action="{{ route('admin.events.update', $event) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Championship --}}
        <div>
          <label for="championship" class="block text-sm font-medium mb-1">Championship</label>
          @php $champ = old('championship', $event->championship); @endphp
          <select id="championship" name="championship" class="form-select w-full">
            <option value="">Select…</option>
            <option value="WRC" @selected($champ === 'WRC')>WRC</option>
            <option value="ERC" @selected($champ === 'ERC')>ERC</option>
            <option value="ARA" @selected($champ === 'ARA')>ARA</option>
          </select>
          @error('championship') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Name --}}
        <div>
          <label class="block text-sm font-medium mb-1">Event Name</label>
          <input type="text" name="name" value="{{ old('name', $event->name) }}" class="form-input w-full" required>
          @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Location --}}
        <div>
          <label class="block text-sm font-medium mb-1">Location</label>
          <input type="text" name="location" value="{{ old('location', $event->location) }}" class="form-input w-full">
          @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Dates --}}
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Start Date</label>
            <input type="date" name="start_date"
                   value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}"
                   class="form-input w-full" required>
            <p class="text-[11px] text-gray-500 mt-1">Used to auto-generate event days.</p>
            @error('start_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">End Date</label>
            <input type="date" name="end_date"
                   value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}"
                   class="form-input w-full" required>
            @error('end_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- EVENT MAP (Google My Maps embed URL) --}}
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Event map embed URL</label>
          <input name="map_embed_url"
                 value="{{ old('map_embed_url', $event->map_embed_url) }}"
                 class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-600/30"
                 placeholder="https://www.google.com/maps/d/embed?mid=...">
          <p class="text-[11px] text-gray-500 mt-1">
            Paste the <code>src</code> value from Google “My Maps” → Share → Embed.
          </p>
        
          @error('map_embed_url') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        
          @if($event->map_embed_url)
            <div class="mt-3 ring-1 ring-black/5 rounded-xl overflow-hidden">
              <iframe src="{{ $event->map_embed_url }}" loading="lazy"
                      class="w-full h-72 border-0"></iframe>
            </div>
          @endif
        </div>


        {{-- Description --}}
        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea name="description" rows="5" class="form-textarea w-full"
                    placeholder="Short overview, surface, character, notable stages…">{{ old('description', $event->description) }}</textarea>
          @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between pt-2">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            Update Event
          </button>

          {{-- One-click generate days --}}
          <form action="{{ route('admin.events.days.store', $event) }}" method="POST"
                onsubmit="return confirm('Generate/refresh event days from the start/end dates?')">
            @csrf
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded">
              Generate Days from Dates
            </button>
          </form>
        </div>
      </form>
    </div>

    {{-- Side info card --}}
    <aside class="bg-white rounded-xl shadow p-6">
      <h2 class="font-semibold mb-4">Event Summary</h2>
      <dl class="text-sm space-y-2">
        <div class="flex justify-between">
          <dt class="text-gray-500">Championship</dt>
          <dd class="font-medium">{{ $event->championship ?: '—' }}</dd>
        </div>
        <div class="flex justify-between">
          <dt class="text-gray-500">Days</dt>
          <dd class="font-medium">{{ $event->days()->count() }}</dd>
        </div>
        <div class="flex justify-between">
          <dt class="text-gray-500">Stages</dt>
          <dd class="font-medium">{{ $event->stages()->count() }}</dd>
        </div>
        @if($event->slug)
        <div class="flex justify-between">
          <dt class="text-gray-500">Slug</dt>
          <dd class="font-mono">{{ $event->slug }}</dd>
        </div>
        @endif
      </dl>

      <div class="mt-4 space-y-2">
        <a href="{{ route('admin.events.days.index', $event) }}"
           class="block w-full text-center border rounded py-2 hover:bg-gray-50">Manage Days</a>
        <a href="{{ route('admin.events.stages.index', $event) }}"
           class="block w-full text-center border rounded py-2 hover:bg-gray-50">Manage Stages</a>
      </div>
    </aside>
  </div>
</div>
@endsection