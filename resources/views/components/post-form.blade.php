{{-- resources/views/components/post-form.blade.php --}}
@props([
  'action',
  'title' => 'Create Post',
  'submitLabel' => 'Publish',
  'board' => null,        // fixed board context (hidden)
  'model' => null,        // Post when editing
  'method' => 'POST',     // 'POST' | 'PATCH' | 'PUT'
  'boards' => collect(),  // optional list for selector
])

@php $verb = strtoupper($method ?? 'POST'); @endphp

<div class="max-w-4xl mx-auto px-4 pt-6 pb-16">
  <div class="ci-admin-card">
    <h1 class="ci-title-lg text-center mb-6">{{ $title }}</h1>

    @if ($errors->any())
      <div class="ci-alert ci-alert-warn mb-6">
        <ul class="list-disc pl-5 space-y-1 text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data"
          class="space-y-5"
          x-data="{ exCount: 0 }"
          x-init="exCount = $refs.excerpt?.value.length || 0">
      @csrf
      @if ($verb !== 'POST') @method($verb) @endif

      {{-- Fixed board (hidden) --}}
      @if ($board)
        <input type="hidden" name="board_id" value="{{ $board->id }}">
        <div class="ci-badge mb-2">Posting to: <span class="ml-1 font-semibold">{{ $board->name }}</span></div>
      @elseif($boards->count())
        {{-- Optional board selector --}}
        <div>
          <label for="board_id" class="block text-sm font-medium mb-1">Post to a board (optional)</label>
          <select id="board_id" name="board_id" class="ci-select">
            <option value="">— No board —</option>
            @foreach ($boards as $b)
              <option value="{{ $b->id }}"
                @selected(old('board_id', optional($model)->board_id) == $b->id)>
                {{ $b->name }}
              </option>
            @endforeach
          </select>
          @error('board_id') <p class="ci-error mt-1">{{ $message }}</p> @enderror
        </div>
      @endif

      {{-- Title --}}
      <div>
        <label for="title" class="block text-sm font-medium mb-1">Title</label>
        <input id="title" name="title" type="text" required
               value="{{ old('title', optional($model)->title) }}"
               class="ci-input">
      </div>

      {{-- Slug (existing component) --}}
      <x-form.slug-field :value="old('slug', optional($model)->slug)" />

      {{-- Excerpt (with live counter) --}}
      <div>
        <label for="excerpt" class="block text-sm font-medium mb-1">Excerpt</label>
        <textarea id="excerpt" x-ref="excerpt" name="excerpt" rows="2" maxlength="160"
                  class="ci-textarea"
                  x-on:input="exCount = $event.target.value.length">{{ old('excerpt', optional($model)->excerpt) }}</textarea>
        <div class="mt-1 flex items-center justify-between">
          <p class="text-xs ci-muted">Max 160 characters.</p>
          <p class="text-xs ci-muted"><span x-text="exCount"></span>/160</p>
        </div>
        @error('excerpt') <p class="ci-error">{{ $message }}</p> @enderror
      </div>

      {{-- Body --}}
      <div>
        <label for="body" class="block text-sm font-medium mb-1">Body</label>
        <textarea id="body" name="body" rows="8" required
                  placeholder="Write your post..."
                  class="ci-textarea">{{ old('body', optional($model)->body) }}</textarea>
        @error('body') <p class="ci-error">{{ $message }}</p> @enderror
      </div>

      {{-- Feature image --}}
      <div>
        <label for="image_path" class="block text-sm font-medium mb-1">Feature image (optional)</label>
        <input id="image_path" name="image_path" type="file" accept="image/*" class="ci-input">
        @error('image_path') <p class="ci-error mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="text-center pt-2">
        <button type="submit" class="ci-btn-primary">{{ $submitLabel }}</button>
      </div>
    </form>
  </div>
</div>