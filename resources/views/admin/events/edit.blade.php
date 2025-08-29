@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <a href="{{ route('admin.events.index') }}" class="ci-link">← Back to Events</a>
    <div class="text-sm space-x-3">
      @if($event->slug)
        <a href="{{ route('calendar.show', $event->slug) }}" class="ci-link" target="_blank">View public page ↗</a>
      @endif
      <a href="{{ route('admin.events.days.index', $event) }}" class="ci-link">Manage Days</a>
      <a href="{{ route('admin.events.stages.index', $event) }}" class="ci-link">Manage Stages</a>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Main form --}}
    <div class="md:col-span-2 ci-admin-card">
      <h1 class="text-2xl font-bold mb-6">Edit Rally Event</h1>

      <form action="{{ route('admin.events.update', $event) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Championship --}}
        <div>
          <label for="championship" class="block text-sm font-medium mb-1">Championship</label>
          @php $champ = old('championship', $event->championship); @endphp
          <select id="championship" name="championship" class="ci-select">
            <option value="">Select…</option>
            <option value="WRC" @selected($champ === 'WRC')>WRC</option>
            <option value="ERC" @selected($champ === 'ERC')>ERC</option>
            <option value="ARA" @selected($champ === 'ARA')>ARA</option>
          </select>
          @error('championship') <p class="ci-error">{{ $message }}</p> @enderror
        </div>

        {{-- Name --}}
        <div>
          <label class="block text-sm font-medium mb-1">Event Name</label>
          <input type="text" name="name" value="{{ old('name', $event->name) }}" class="ci-input" required>
          @error('name') <p class="ci-error">{{ $message }}</p> @enderror
        </div>

        {{-- Location --}}
        <div>
          <label class="block text-sm font-medium mb-1">Location</label>
          <input type="text" name="location" value="{{ old('location', $event->location) }}" class="ci-input">
          @error('location') <p class="ci-error">{{ $message }}</p> @enderror
        </div>

        {{-- Dates --}}
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Start Date</label>
            <input type="date" name="start_date"
                   value="{{ old('start_date', optional($event->start_date)->format('Y-m-d')) }}"
                   class="ci-input" required>
            <p class="ci-help">Used to auto-generate event days.</p>
            @error('start_date') <p class="ci-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">End Date</label>
            <input type="date" name="end_date"
                   value="{{ old('end_date', optional($event->end_date)->format('Y-m-d')) }}"
                   class="ci-input" required>
            @error('end_date') <p class="ci-error">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Official Website --}}
        <div>
          <label class="block text-sm font-medium mb-1">Official Website</label>
          <input type="url" name="official_url"
                 value="{{ old('official_url', $event->official_url) }}"
                 class="ci-input" placeholder="https://example.com">
          @error('official_url') <p class="ci-error">{{ $message }}</p> @enderror
        </div>

        {{-- EVENT MAP (Google My Maps embed URL) --}}
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Event map embed URL</label>
          <input name="map_embed_url"
                 value="{{ old('map_embed_url', $event->map_embed_url) }}"
                 class="ci-input" placeholder="https://www.google.com/maps/d/embed?mid=...">
          <p class="ci-help">
            Paste the <code class="ci-code">src</code> value from Google “My Maps” → Share → Embed.
          </p>

          @error('map_embed_url') <p class="ci-error">{{ $message }}</p> @enderror

          @if($event->map_embed_url)
            <div class="mt-3 ci-admin-card overflow-hidden p-0">
              <iframe src="{{ $event->map_embed_url }}" loading="lazy" class="w-full h-72 border-0"></iframe>
            </div>
          @endif
        </div>

        {{-- Description --}}
        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea name="description" rows="5" class="ci-textarea"
                    placeholder="Short overview, surface, character, notable stages…">{{ old('description', $event->description) }}</textarea>
          @error('description') <p class="ci-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between pt-2">
          <button type="submit" class="ci-btn-success">
            Update Event
          </button>

          {{-- One-click generate days --}}
          <form action="{{ route('admin.events.days.store', $event) }}" method="POST"
                onsubmit="return confirm('Generate/refresh event days from the start/end dates?')">
            @csrf
            <button class="ci-btn-primary">
              Generate Days from Dates
            </button>
          </form>
        </div>
      </form>
    </div>

    {{-- Side info card --}}
    <aside class="ci-admin-card">
      <h2 class="font-semibold mb-4">Event Summary</h2>
      <dl class="text-sm space-y-2">
        <div class="flex justify-between">
          <dt class="ci-muted">Championship</dt>
          <dd class="font-medium">{{ $event->championship ?: '—' }}</dd>
        </div>
        <div class="flex justify-between">
          <dt class="ci-muted">Days</dt>
          <dd class="font-medium">{{ $event->days()->count() }}</dd>
        </div>
        <div class="flex justify-between">
          <dt class="ci-muted">Stages</dt>
          <dd class="font-medium">{{ $event->stages()->count() }}</dd>
        </div>
        @if($event->slug)
        <div class="flex justify-between">
          <dt class="ci-muted">Slug</dt>
          <dd class="font-mono">{{ $event->slug }}</dd>
        </div>
        @endif
      </dl>

      <div class="mt-4 space-y-2">
        <a href="{{ route('admin.events.days.index', $event) }}" class="ci-btn-ghost w-full text-center">
          Manage Days
        </a>
        <a href="{{ route('admin.events.stages.index', $event) }}" class="ci-btn-ghost w-full text-center">
          Manage Stages
        </a>
      </div>
    </aside>
  </div>
</div>
@endsection