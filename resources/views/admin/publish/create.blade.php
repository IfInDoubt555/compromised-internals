@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
  <h1 class="text-2xl font-bold">Create Content</h1>

  <form method="POST" action="{{ route('admin.publish.store') }}" enctype="multipart/form-data" x-data="{ type: '{{ old('type','blog') }}' }" class="ci-card p-4 space-y-6">
    @csrf

    {{-- Type toggle --}}
    <div>
      <label class="block font-semibold mb-2">Publish As</label>
      <div class="flex gap-6">
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="type" value="blog" x-model="type">
          <span>Blog Post</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="radio" name="type" value="thread" x-model="type">
          <span>Board Thread</span>
        </label>
      </div>
    </div>

    {{-- Shared fields --}}
    <div>
      <label class="block font-semibold mb-1">Title</label>
      <input name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold mb-1">Slug (optional)</label>
        <input name="slug" value="{{ old('slug') }}" class="w-full border rounded px-3 py-2">
      </div>

      {{-- Blog: optional board association; Thread: required board --}}
      <div x-show="type==='blog'">
        <label class="block font-semibold mb-1">Associate to Board (optional)</label>
        <select name="board_id" class="w-full border rounded px-3 py-2">
          <option value="">— None —</option>
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected(old('board_id')==$b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>

      <div x-show="type==='thread'">
        <label class="block font-semibold mb-1">Board</label>
        <select name="thread_board_id" class="w-full border rounded px-3 py-2">
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected(old('thread_board_id')==$b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div>
      <label class="block font-semibold mb-1">Body</label>
      <textarea name="body" rows="10" class="w-full border rounded px-3 py-2" required>{{ old('body') }}</textarea>
    </div>

    {{-- Blog-only image upload --}}
    <div x-show="type==='blog'">
      <label class="block font-semibold mb-1">Feature Image (optional)</label>
      <input type="file" name="image_path" accept="image/*" class="w-full border rounded px-3 py-2">
    </div>

    {{-- Scheduling --}}
    <div x-show="type==='blog'">
      @include('admin.partials.scheduling', ['model' => new \App\Models\Post(), 'field' => 'publish_status'])
    </div>
    <div x-show="type==='thread'">
      @include('admin.partials.scheduling', ['model' => new \App\Models\Thread(), 'field' => 'status'])
    </div>

    <div class="flex justify-end">
      <button class="btn-primary">Create</button>
    </div>
  </form>
</div>
@endsection