@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-4">Edit Thread</h1>

  <form method="POST" action="{{ route('admin.threads.update', $thread) }}" class="ci-card p-4 space-y-6">
    @csrf @method('PUT')

    <label class="block">
      <span class="block font-semibold mb-1">Title</span>
      <input name="title" class="w-full border rounded px-3 py-2"
             value="{{ old('title',$thread->title) }}" required>
    </label>

    <label class="block">
      <span class="block font-semibold mb-1">Slug (optional)</span>
      <input name="slug" class="w-full border rounded px-3 py-2"
             value="{{ old('slug',$thread->slug) }}">
    </label>

    <label class="block">
      <span class="block font-semibold mb-1">Board</span>
      <select name="board_id" class="w-full border rounded px-3 py-2" required>
        @foreach($boards as $b)
          <option value="{{ $b->id }}" @selected(old('board_id',$thread->board_id)===$b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
    </label>

    <label class="block">
      <span class="block font-semibold mb-1">Body</span>
      <textarea name="body" rows="8" class="w-full border rounded px-3 py-2" required>{{ old('body',$thread->body) }}</textarea>
    </label>

    @include('admin.partials.scheduling', ['model' => $thread])

    <div class="flex justify-end">
      <button class="btn-primary">Save</button>
    </div>
  </form>
</div>
@endsection