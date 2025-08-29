@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto my-12">
  <div
    class="rounded-2xl p-8 shadow-xl bg-white/90 ring-1 ring-black/5 backdrop-blur
           transition-shadow
           hover:shadow-[0_0_60px_rgba(16,185,129,0.22)]
           focus-within:shadow-[0_0_60px_rgba(16,185,129,0.22)]
           dark:bg-stone-900/80 dark:ring-white/10
           dark:hover:shadow-[0_0_60px_rgba(52,211,153,0.25)]
           dark:focus-within:shadow-[0_0_60px_rgba(52,211,153,0.25)]"
  >
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-800 dark:text-stone-100">
      Edit Post ✏️
    </h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
      <div class="mb-6 rounded-lg p-4 bg-red-100 text-red-700 dark:bg-rose-900/30 dark:text-rose-200">
        <ul class="list-disc pl-6 space-y-1 text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PATCH')

      {{-- Discussion Board (optional) --}}
      @php
        $boards = \App\Models\Board::orderBy('position')->get();
        $selectedBoardId = old('board_id', $post->board_id);
      @endphp
      <div>
        <label for="board_id" class="block text-sm font-medium mb-1 text-gray-700 dark:text-stone-300">
          Discussion Board (optional)
        </label>
        <select
          id="board_id"
          name="board_id"
          class="w-full px-4 py-2 rounded-xl border bg-white border-gray-300
                 focus:ring focus:ring-blue-200 focus:border-blue-400
                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10"
        >
          <option value="">— No board —</option>
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected($selectedBoardId == $b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
        @error('board_id')
          <p class="mt-1 text-xs text-red-600 dark:text-rose-300">{{ $message }}</p>
        @enderror
      </div>

      {{-- Title --}}
      <div>
        <label for="title" class="block text-sm font-medium mb-1 text-gray-700 dark:text-stone-300">Title</label>
        <input
          type="text"
          name="title"
          id="title"
          value="{{ old('title', $post->title) }}"
          required
          class="w-full px-4 py-2 rounded-xl border bg-white border-gray-300
                 focus:ring focus:ring-blue-200 focus:border-blue-400
                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500"
        >
      </div>

      {{-- Slug Field --}}
      <x-form.slug-field :slug="$post->slug" :defaultMode="'manual'" />

      {{-- Excerpt --}}
      <div>
        <label for="excerpt" class="block text-sm font-medium mb-1 text-gray-700 dark:text-stone-300">Excerpt</label>
        <textarea
          name="excerpt"
          id="excerpt"
          class="w-full px-4 py-2 rounded-xl border bg-white border-gray-300
                 focus:ring focus:ring-blue-200 focus:border-blue-400
                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500"
        >{{ old('excerpt', $post->excerpt) }}</textarea>
      </div>

      {{-- Body --}}
      <div>
        <label for="body" class="block text-sm font-medium mb-1 text-gray-700 dark:text-stone-300">Body</label>
        <textarea
          name="body"
          id="body"
          rows="8"
          required
          class="w-full px-4 py-2 rounded-xl border bg-white border-gray-300
                 focus:ring focus:ring-blue-200 focus:border-blue-400
                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500"
        >{{ old('body', $post->body) }}</textarea>
      </div>

      {{-- Image Upload --}}
      <div>
        <label for="image_path" class="block text-sm font-medium mb-1 text-gray-700 dark:text-stone-300">Upload Image</label>
        <input
          type="file"
          name="image_path"
          id="image_path"
          accept="image/*"
          class="w-full px-4 py-2 rounded-xl border bg-white border-gray-300
                 focus:ring focus:ring-blue-200 focus:border-blue-400
                 dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10"
        >
      </div>

      {{-- Submit --}}
      <div class="text-center">
        <button
          type="submit"
          class="px-6 py-3 font-semibold rounded-xl shadow transition
                 bg-red-600 text-white hover:bg-red-700
                 dark:bg-rose-600 dark:hover:bg-rose-500"
        >
          Update Post
        </button>
      </div>
    </form>
  </div>
</div>
@endsection