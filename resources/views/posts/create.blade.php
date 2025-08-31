@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto my-12 px-4">
  <div
    class="rounded-2xl p-8 shadow-xl bg-white/90 ring-1 ring-black/5 backdrop-blur
           transition-shadow
           hover:shadow-[0_0_60px_rgba(14,165,233,0.18)]
           focus-within:shadow-[0_0_60px_rgba(14,165,233,0.18)]
           focus-within:ring-2 focus-within:ring-sky-400/40 focus-within:ring-offset-2 focus-within:ring-offset-white
           dark:bg-stone-900/80 dark:ring-white/10
           dark:hover:shadow-[0_0_60px_rgba(52,211,153,0.25)]
           dark:focus-within:shadow-[0_0_60px_rgba(52,211,153,0.25)]
           dark:focus-within:ring-2 dark:focus-within:ring-emerald-400/30 dark:focus-within:ring-offset-stone-900">
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-stone-100">
      Create a New Rally Post
    </h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
      <div class="mb-6 rounded-lg p-4 border
                  bg-red-50 text-red-800 border-red-200
                  dark:bg-rose-900/30 dark:text-rose-100 dark:border-rose-700/40">
        <ul class="list-disc pl-6 space-y-1 text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- One universal post form --}}
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf

      {{-- If a board context is provided (controller passes $board OR ?board=slug) --}}
      @php
        $ctxBoard = isset($board) ? $board : \App\Models\Board::where('slug', request('board'))->first();
      @endphp

      @if($ctxBoard)
        <input type="hidden" name="board_id" value="{{ $ctxBoard->id }}">
        <div class="rounded-lg p-3 text-sm border bg-gray-50/80 border-gray-200 text-gray-800
                    dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
          Posting to board:
          <a href="{{ route('boards.show', $ctxBoard->slug) }}"
             class="font-semibold underline text-blue-700 hover:text-blue-800
                    dark:text-sky-400 dark:hover:text-sky-300">
            {{ $ctxBoard->name }}
          </a>
          <a href="{{ route('posts.create') }}"
             class="ml-2 text-xs underline text-gray-600 hover:text-gray-800
                    dark:text-stone-400 dark:hover:text-stone-200">
            change
          </a>
        </div>
      @else
        {{-- Optional board selector --}}
        <div>
          <label for="board_id" class="block text-sm font-medium mb-1 text-gray-800 dark:text-stone-200">
            Optional: Post to a Board
          </label>
          <select
            id="board_id"
            name="board_id"
            class="w-full rounded-xl border bg-white text-gray-900 placeholder-gray-400
                   border-gray-300 px-4 py-2
                   focus:outline-none focus:ring-4 focus:ring-sky-200/60 focus:border-sky-400
                   dark:bg-stone-800/70 dark:text-stone-100 dark:placeholder-stone-500 dark:border-white/10
                   dark:focus:ring-emerald-300/20 dark:focus:border-emerald-400"
          >
            <option value="">— No board —</option>
            @foreach(\App\Models\Board::orderBy('position')->get() as $b)
              <option value="{{ $b->id }}" @selected(old('board_id') == $b->id)>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
      @endif

      {{-- Title --}}
      <div>
        <label for="title" class="block text-sm font-medium mb-1 text-gray-800 dark:text-stone-200">Title</label>
        <input
          type="text"
          name="title"
          id="title"
          value="{{ old('title') }}"
          required
          class="w-full rounded-xl border bg-white text-gray-900 placeholder-gray-400
                 border-gray-300 px-4 py-2
                 focus:outline-none focus:ring-4 focus:ring-sky-200/60 focus:border-sky-400
                 dark:bg-stone-800/70 dark:text-stone-100 dark:placeholder-stone-500 dark:border-white/10
                 dark:focus:ring-emerald-300/20 dark:focus:border-emerald-400"
        >
      </div>

      {{-- Slug Field --}}
      <x-form.slug-field :value="old('slug')" />

      {{-- Body --}}
      <div>
        <label for="body" class="block text-sm font-medium mb-1 text-gray-800 dark:text-stone-200">Body</label>
        <textarea
          name="body"
          id="body"
          rows="8"
          required
          placeholder="Write your full rally story here..."
          class="w-full rounded-xl border bg-white text-gray-900 placeholder-gray-400
                 border-gray-300 px-4 py-2
                 focus:outline-none focus:ring-4 focus:ring-sky-200/60 focus:border-sky-400
                 dark:bg-stone-800/70 dark:text-stone-100 dark:placeholder-stone-500 dark:border-white/10
                 dark:focus:ring-emerald-300/20 dark:focus:border-emerald-400"
        >{{ old('body') }}</textarea>
      </div>

      {{-- Image --}}
      <div>
        <label for="image_path" class="block text-sm font-medium mb-1 text-gray-800 dark:text-stone-200">Upload Image</label>
        <input
          type="file"
          name="image_path"
          id="image_path"
          class="w-full rounded-xl border bg-white text-gray-900
                 border-gray-300 px-3 py-2
                 file:mr-4 file:rounded-md file:border-0 file:px-3 file:py-2
                 file:bg-gray-100 file:text-gray-900 file:hover:bg-gray-200
                 focus:outline-none focus:ring-4 focus:ring-sky-200/60 focus:border-sky-400
                 dark:bg-stone-800/70 dark:text-stone-100 dark:border-white/10
                 dark:file:bg-stone-700 dark:file:text-stone-100 dark:file:hover:bg-stone-600
                 dark:focus:ring-emerald-300/20 dark:focus:border-emerald-400"
        >
      </div>

      {{-- Submit --}}
      <div class="text-center">
        <button
          type="submit"
          class="px-6 py-3 font-semibold rounded-xl shadow transition
                 bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300/40
                 dark:bg-rose-600 dark:hover:bg-rose-500 dark:focus:ring-rose-400/25"
        >
          Publish Post
        </button>
      </div>
    </form>
  </div>
</div>
@endsection