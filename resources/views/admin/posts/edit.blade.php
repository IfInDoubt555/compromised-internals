@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
  <h1 class="ci-title-lg">Edit Post</h1>

  <form method="POST" action="{{ route('admin.posts.update', $post) }}" class="ci-admin-card p-6 space-y-6">
    @csrf @method('PUT')

    {{-- Title --}}
    <div>
      <label class="ci-label mb-1">Title</label>
      <input name="title" class="ci-input"
             value="{{ old('title', $post->title) }}" required>
    </div>

    {{-- Slug + Board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="ci-label mb-1">Slug (optional)</label>
        <input name="slug" class="ci-input"
               value="{{ old('slug', $post->slug) }}">
      </div>
      <div>
        <label class="ci-label mb-1">Board (optional)</label>
        <select name="board_id" class="ci-select">
          <option value="">— None —</option>
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected(old('board_id',$post->board_id)===$b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Excerpt --}}
    <div>
      <label class="ci-label mb-1">Excerpt</label>
      <textarea name="excerpt" rows="2" class="ci-textarea">{{ old('excerpt',$post->excerpt) }}</textarea>
    </div>

    {{-- Body --}}
    <div>
      <label class="ci-label mb-1">Body</label>
      <textarea name="body" rows="10" class="ci-textarea" required>{{ old('body',$post->body) }}</textarea>
    </div>

    {{-- Scheduling UI --}}
    @include('admin.partials.scheduling', ['model' => $post, 'field' => 'publish_status'])

    <div class="flex justify-end">
      <button class="ci-btn-primary">Save</button>
    </div>
  </form>
</div>
@endsection