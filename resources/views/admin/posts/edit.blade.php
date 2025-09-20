@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
  <h1 class="ci-title-lg">Edit Post</h1>

  <form
    method="POST"
    action="{{ route('admin.posts.update', $post) }}"
    enctype="multipart/form-data"
    class="ci-admin-card p-6 space-y-6"
    x-data="{ excerptCount: {{ strlen(old('excerpt', $post->excerpt ?? '')) }} }"
  >
    @csrf
    @method('PUT')

    {{-- Title --}}
    <div>
      <label for="title" class="ci-label mb-1">Title</label>
      <input id="title" name="title" class="ci-input" value="{{ old('title', $post->title) }}" required>
      @error('title') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    {{-- Slug + Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="slug" class="ci-label mb-1">Slug (optional)</label>
        <input id="slug" name="slug" class="ci-input" value="{{ old('slug', $post->slug) }}">
        @error('slug') <p class="ci-error">{{ $message }}</p> @enderror
      </div>
      <div>
        <label for="board_id" class="ci-label mb-1">Board (optional)</label>
        <select id="board_id" name="board_id" class="ci-select">
          <option value="">— None —</option>
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected(old('board_id', $post->board_id) === $b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
        @error('board_id') <p class="ci-error">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Excerpt --}}
    <div>
      <label for="excerpt" class="ci-label mb-1">Excerpt</label>
      <textarea
        id="excerpt"
        name="excerpt"
        rows="2"
        maxlength="160"
        class="ci-textarea"
        x-on:input="excerptCount = $event.target.value.length"
      >{{ old('excerpt', $post->excerpt) }}</textarea>
      <div class="mt-1 flex items-center justify-between">
        <p class="text-xs text-gray-500 dark:text-stone-400">Max 160 characters.</p>
        <p class="text-xs text-gray-500 dark:text-stone-400" x-text="`${excerptCount}/160`"></p>
      </div>
      @error('excerpt') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    {{-- Body (Markdown) --}}
    <div>
      <label for="body" class="ci-label mb-1">Body</label>
      <textarea id="body" name="body" rows="12" class="ci-textarea" required>{{ old('body', $post->body) }}</textarea>
      @error('body') <p class="ci-error">{{ $message }}</p> @enderror
    </div>

    {{-- Feature Image --}}
    <div class="mb-6">
      <span class="ci-label mb-2">Feature Image (optional)</span>
      <input id="image_path" type="file" name="image_path" accept="image/*" class="block">
      <p class="ci-help mt-2">JPG/PNG/WebP • up to 5 MB</p>
      @error('image_path') <p class="ci-error mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Scheduling (status + published_at) --}}
    @include('admin.partials.scheduling', [
      'model'     => $post,
      'field'     => 'status',
      'dateField' => 'published_at',
    ])

    <div class="flex justify-end gap-3">
      <a href="{{ route('admin.publish.index') }}" class="ci-btn-secondary">Cancel</a>
      <button type="submit" class="ci-btn-primary">Save</button>
    </div>
  </form>
</div>
@endsection