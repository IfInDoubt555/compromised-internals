@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
  <h1 class="text-2xl font-bold">Edit Post</h1>

  <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="ci-card p-4 space-y-6">
    @csrf @method('PUT')

    <div>
      <label class="block font-semibold mb-1">Title</label>
      <input name="title" class="w-full border rounded px-3 py-2"
             value="{{ old('title', $post->title) }}" required>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block font-semibold mb-1">Slug (optional)</label>
        <input name="slug" class="w-full border rounded px-3 py-2"
               value="{{ old('slug', $post->slug) }}">
      </div>
      <div>
        <label class="block font-semibold mb-1">Board (optional)</label>
        <select name="board_id" class="w-full border rounded px-3 py-2">
          <option value="">— None —</option>
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected(old('board_id',$post->board_id)===$b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div>
      <label class="block font-semibold mb-1">Excerpt</label>
      <textarea name="excerpt" rows="2" class="w-full border rounded px-3 py-2">{{ old('excerpt',$post->excerpt) }}</textarea>
    </div>

    <div>
      <label class="block font-semibold mb-1">Body</label>
      <textarea name="body" rows="10" class="w-full border rounded px-3 py-2" required>{{ old('body',$post->body) }}</textarea>
    </div>

    {{-- Scheduling UI (use publish_status field for Posts) --}}
    @include('admin.partials.scheduling', ['model' => $post, 'field' => 'publish_status'])

    <div class="flex justify-end">
      <button class="btn-primary">Save</button>
    </div>
  </form>
</div>
@endsection