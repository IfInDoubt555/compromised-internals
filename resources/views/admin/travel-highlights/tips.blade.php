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

  <div class="grid md:grid-cols-2 gap-6">
    {{-- Editor --}}
    <form action="{{ route('admin.travel-highlights.tips.update') }}" method="POST"
          class="bg-white rounded shadow p-6">
      @csrf
      @method('PUT')

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
          <input type="checkbox" name="is_active" value="1"
                 {{ old('is_active', $tips->is_active) ? 'checked' : '' }}>
          <span>Show tips on the /travel page</span>
        </label>
      </div>

      <div class="mt-6 flex items-center gap-3">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save Tips</button>
        <a href="{{ route('travel.index') }}" target="_blank" rel="noopener"
           class="text-blue-600 hover:underline">View public page</a>
      </div>
    </form>

    {{-- Preview --}}
    @php
      $previewLines = collect(preg_split('/\R/', (string) old('tips_md', $tips->tips_md)))
          ->map(fn($t) => trim($t))
          ->filter()
          ->values();
    @endphp
    <div class="bg-white rounded shadow p-6">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold">Preview</h2>
        <span class="text-xs px-2 py-1 rounded
                     {{ old('is_active', $tips->is_active) ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
          {{ old('is_active', $tips->is_active) ? 'Active' : 'Hidden' }}
        </span>
      </div>

      @if ($previewLines->isEmpty())
        <p class="mt-3 text-sm text-gray-500">Start typing tips on the left to see a preview.</p>
      @else
        <ul class="mt-3 list-disc list-inside text-sm text-gray-800 space-y-1">
          @foreach ($previewLines as $line)
            <li>{{ $line }}</li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
@endsection