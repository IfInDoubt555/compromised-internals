@extends('layouts.admin')

@section('content')
  <a href="{{ route('admin.travel-highlights.index') }}" class="text-blue-600 hover:underline">← Back to Highlights</a>

  <h1 class="text-2xl font-bold mt-3 mb-4">Travel Tips (Plan Your Trip)</h1>

  @if (session('status'))
    <div class="mb-4 px-3 py-2 rounded bg-green-50 text-green-800">
      {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 px-3 py-2 rounded bg-red-50 text-red-800">
      <strong>Fix the following:</strong>
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ONE form for editor + selection so the checkboxes post --}}
  <form action="{{ route('admin.travel-highlights.tips.update') }}" method="POST" class="grid md:grid-cols-2 gap-6">
    @csrf
    @method('PUT')

    {{-- LEFT: editor --}}
    <div class="bg-white rounded shadow p-6">
      <label class="block font-semibold mb-2">Tips (one per line)</label>
      <p class="text-xs text-gray-500 mb-3">
        Plain text works great. Markdown is okay (e.g., <code>**bold**</code>, links) — it’ll be rendered safely on the public page.
      </p>

      <textarea name="tips_md" rows="12"
                class="w-full border rounded px-3 py-2 font-mono text-sm"
                placeholder="Book early for Monte-Carlo and Finland — hotels & camping fill fast.&#10;Consider car rentals for Portugal or Sardinia — many stages are remote.&#10;Check official event sites for shuttles and restricted roads.">{{ old('tips_md', $tips->tips_md) }}</textarea>

      <div class="mt-4">
        <label class="inline-flex items-center gap-2">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tips->is_active) ? 'checked' : '' }}>
          <span>Show tips on the /travel page</span>
        </label>
      </div>
    </div>

    {{-- RIGHT: selection / preview --}}
    @php
      $raw     = (string) old('tips_md', $tips->tips_md);
      $lines   = collect(preg_split('/\R/', $raw))->map(fn($t) => trim($t))->filter()->values();
      $default = $lines->isEmpty() ? [] : range(0, $lines->count() - 1);    // default to all on first save
      $enabled = collect(old('enabled', $tips->tips_selection ?? $default))
                  ->map(fn($i) => (int) $i)->values()->all();
    @endphp

    <div class="bg-white rounded shadow p-6">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Choose which tips to display</h2>
        <span class="text-xs px-2 py-1 rounded
                     {{ old('is_active', $tips->is_active) ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
          {{ old('is_active', $tips->is_active) ? 'Active' : 'Hidden' }}
        </span>
      </div>

      @if ($lines->isEmpty())
        <p class="mt-3 text-sm text-gray-500">Add tips on the left and save to select them.</p>
      @else
        <div class="mt-3 flex items-center justify-between text-xs">
          <div class="text-gray-600">Tick the items you want to show on the public page.</div>
          <div class="space-x-3">
            <button type="button" class="underline"
                    onclick="this.form.querySelectorAll('input[name=&quot;enabled[]&quot;]').forEach(c=>c.checked=true)">
              Select all
            </button>
            <button type="button" class="underline"
                    onclick="this.form.querySelectorAll('input[name=&quot;enabled[]&quot;]').forEach(c=>c.checked=false)">
              None
            </button>
          </div>
        </div>

        <div class="mt-3 space-y-2">
          @foreach ($lines as $i => $line)
            <label class="flex items-start gap-2">
              <input type="checkbox" name="enabled[]" value="{{ $i }}" class="mt-1"
                     {{ in_array($i, $enabled, true) ? 'checked' : '' }}>
              <span class="text-sm text-gray-800">{{ $line }}</span>
            </label>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Save bar (full width) --}}
    <div class="md:col-span-2 flex items-center gap-3">
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save Tips</button>
      <a href="{{ route('travel.index') }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">
        View public page
      </a>
    </div>
  </form>
@endsection