@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8" x-data="{ type: @js(old('type','blog')) }">
  <h1 class="ci-title-lg mb-6">Create Content</h1>

  <form action="{{ route('admin.publish.store') }}" method="POST" enctype="multipart/form-data" class="ci-admin-card">
    @csrf

    {{-- Publish As --}}
    <div class="mb-6">
      <p class="ci-label mb-2">Publish As</p>
      <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="type" value="blog" x-model="type">
          <span>Blog Post</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="type" value="thread" x-model="type">
          <span>Board Thread</span>
        </label>
      </div>
      @error('type') <p class="ci-error mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Title --}}
    <label class="block mb-4">
      <span class="ci-label mb-1">Title</span>
      <input name="title" class="ci-input" value="{{ old('title') }}" placeholder="Write a clear, descriptive title" required>
      @error('title') <p class="ci-error mt-1">{{ $message }}</p> @enderror
    </label>

    {{-- Slug + Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <label class="block">
        <span class="ci-label mb-1">Slug (optional)</span>
        <input name="slug" class="ci-input" value="{{ old('slug') }}" placeholder="auto-generated if left blank">
        @error('slug') <p class="ci-error mt-1">{{ $message }}</p> @enderror
      </label>

      {{-- Board selector (blog optional / thread required) --}}
      <label class="block">
        <span class="ci-label mb-1">
          <span x-show="type==='blog'">Associate to Board (optional)</span>
          <span x-show="type==='thread'">Select Board (required)</span>
        </span>

        {{-- For BLOG: name="board_id" --}}
        <select class="ci-select" name="board_id"
                x-show="type==='blog'" x-cloak>
          <option value="">— None —</option>
          @foreach($boards ?? [] as $board)
            <option value="{{ $board->id }}" @selected(old('board_id')==$board->id)>{{ $board->name }}</option>
          @endforeach
        </select>

        {{-- For THREAD: name="thread_board_id" --}}
        <select class="ci-select" name="thread_board_id"
                x-show="type==='thread'" x-cloak>
          <option value="">— Choose Board —</option>
          @foreach($boards ?? [] as $board)
            <option value="{{ $board->id }}" @selected(old('thread_board_id')==$board->id)>{{ $board->name }}</option>
          @endforeach
        </select>

        @error('board_id') <p class="ci-error mt-1">{{ $message }}</p> @enderror
        @error('thread_board_id') <p class="ci-error mt-1">{{ $message }}</p> @enderror
      </label>
    </div>

    {{-- Body --}}
    <label class="block mb-6">
      <span class="ci-label mb-1">Body</span>
      <textarea name="body" class="ci-textarea h-56" placeholder="Write your content…">{{ old('body') }}</textarea>
      @error('body') <p class="ci-error mt-1">{{ $message }}</p> @enderror
    </label>

    {{-- Feature Image --}}
    <div class="mb-6">
      <span class="ci-label mb-2">Feature Image (optional)</span>
      {{-- controller expects image_path --}}
      <input type="file" name="image_path" class="block">
      <p class="ci-help mt-2">JPG/PNG/WebP • up to 5 MB</p>
      @error('image_path') <p class="ci-error mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Publish Status --}}
    <fieldset class="mb-6">
      <legend class="ci-label mb-2">Publish Status</legend>
      <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="status" value="draft" {{ old('status','draft')==='draft' ? 'checked' : '' }}>
          <span>Draft</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="status" value="scheduled" {{ old('status')==='scheduled' ? 'checked' : '' }}>
          <span>Scheduled</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="status" value="now" {{ old('status')==='now' ? 'checked' : '' }}>
          <span>Publish now</span>
        </label>
      </div>

      <div class="mt-4">
        <label class="ci-label mb-1">Publish at (your local time)</label>
        {{-- controller expects scheduled_for --}}
        <input type="datetime-local" name="scheduled_for" class="ci-input" value="{{ old('scheduled_for') }}">
        <p class="ci-help mt-1">Stored in UTC.</p>
        @error('scheduled_for') <p class="ci-error mt-1">{{ $message }}</p> @enderror
      </div>
    </fieldset>

    <div class="text-right">
      <button class="ci-btn-primary">Create</button>
    </div>
  </form>
</div>
@endsection