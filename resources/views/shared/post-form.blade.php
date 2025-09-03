{{-- resources/views/components/post-form.blade.php --}}
@props([
  'action',
  'title' => 'Create Post',
  'submitLabel' => 'Publish',
  'board' => null,        // \App\Models\Board when posting inside a board
  'model' => null,        // Post model when editing
  'method' => 'POST',     // 'POST' | 'PATCH' | 'PUT'
  'boards' => null,       // Collection of boards for the selector (when no $board)
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

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @if ($verb !== 'POST')
        @method($verb)
      @endif

      {{-- Fixed board context (hidden) --}}
      @if ($board)
        <input type="hidden" name="board_id" value="{{ $board->id }}">
        <div class="ci-badge mb-2">
          Posting to: <span class="ml-1 font-semibold">{{ $board->name }}</span>
        </div>
      @endif

      {{-- Optional board selector when no board is pre-set --}}
      @if (!$board && $boards)
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
          @error('board_id')
            <p class="ci-error mt-1">{{ $message }}</p>
          @enderror
        </div>
      @endif

      {{-- Title --}}
      <div>
        <label for="title" class="block text-sm font-medium mb-1">Title</label>
        <input id="title" name="title" type="text" required
               value="{{ old('title', optional($model)->title) }}"
               class="ci-input">
      </div>

      {{-- Slug (your existing component) --}}
      <x-form.slug-field :value="old('slug', optional($model)->slug)" />

      {{-- Body --}}
      <div>
        <label for="body" class="block text-sm font-medium mb-1">Body</label>
        <textarea id="body" name="body" rows="8" required
                  placeholder="Write your post..."
                  class="ci-textarea">{{ old('body', optional($model)->body) }}</textarea>
      </div>

      {{-- Image (optional) --}}
      <div>
        <label for="image_path" class="block text-sm font-medium mb-1">Upload image</label>
        <input id="image_path" name="image_path" type="file" class="ci-input">
        @error('image_path')
          <p class="ci-error mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="text-center pt-2">
        <button type="submit" class="ci-btn-primary">{{ $submitLabel }}</button>
      </div>
    </form>
  </div>
</div>