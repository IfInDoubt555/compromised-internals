@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  {{-- Card container --}}
  <div
    class="rounded-2xl p-8 shadow-xl bg-white/90 ring-1 ring-black/5 backdrop-blur
           transition-shadow
           hover:shadow-[0_0_60px_rgba(2,132,199,0.18)]
           focus-within:shadow-[0_0_60px_rgba(2,132,199,0.18)]
           dark:bg-stone-900/80 dark:ring-white/10
           dark:hover:shadow-[0_0_60px_rgba(56,189,248,0.20)]
           dark:focus-within:shadow-[0_0_60px_rgba(56,189,248,0.20)]">

    <h1 class="text-2xl font-bold text-stone-900 dark:text-stone-100 mb-6 text-center">
      Edit Thread
    </h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
      <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 mb-6
                  dark:bg-red-900/50 dark:border-red-400/30 dark:text-red-200">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('threads.update', $thread) }}" class="space-y-5">
      @csrf
      @method('PATCH')

      {{-- Discussion Board (optional move) --}}
      @php
        $boards = \App\Models\Board::orderBy('position')->get();
        $selectedBoardId = old('board_id', $thread->board_id);
      @endphp
      <div>
        <label for="board_id" class="block text-sm font-semibold mb-1 text-stone-800 dark:text-stone-300">
          Move to Board (optional)
        </label>
        <select
          id="board_id"
          name="board_id"
          class="w-full rounded-xl bg-white/90 dark:bg-stone-800/60
                 ring-1 ring-black/10 dark:ring-white/10
                 px-3 py-2 text-stone-900 dark:text-stone-100 placeholder-stone-400
                 outline-none focus:ring-2 focus:ring-sky-400/40">
          @foreach($boards as $b)
            <option value="{{ $b->id }}" @selected($selectedBoardId == $b->id)>{{ $b->name }}</option>
          @endforeach
        </select>
        @error('board_id')
          <p class="mt-1 text-xs text-red-600 dark:text-red-300">{{ $message }}</p>
        @enderror
      </div>

      {{-- Title --}}
      <div>
        <label for="title" class="block text-sm font-semibold mb-1 text-stone-800 dark:text-stone-300">
          Title
        </label>
        <input
          type="text"
          name="title"
          id="title"
          value="{{ old('title', $thread->title) }}"
          required
          class="w-full rounded-xl bg-white/90 dark:bg-stone-800/60
                 ring-1 ring-black/10 dark:ring-white/10
                 px-3 py-2 text-stone-900 dark:text-stone-100 placeholder-stone-400
                 outline-none focus:ring-2 focus:ring-sky-400/40">
      </div>

      {{-- Slug (same component you use for posts) --}}
      @if (View::exists('components.form.slug-field'))
        <x-form.slug-field :slug="$thread->slug" :defaultMode="'manual'" />
      @else
        <div>
          <label for="slug" class="block text-sm font-semibold mb-1 text-stone-800 dark:text-stone-300">
            Slug (optional)
          </label>
          <input
            type="text"
            name="slug"
            id="slug"
            value="{{ old('slug', $thread->slug) }}"
            placeholder="auto-generated from title if left blank"
            class="w-full rounded-xl bg-white/90 dark:bg-stone-800/60
                   ring-1 ring-black/10 dark:ring-white/10
                   px-3 py-2 text-stone-900 dark:text-stone-100 placeholder-stone-400
                   outline-none focus:ring-2 focus:ring-sky-400/40">
        </div>
      @endif

      {{-- Body --}}
      <div>
        <label for="body" class="block text-sm font-semibold mb-1 text-stone-800 dark:text-stone-300">
          Body
        </label>
        <textarea
          name="body"
          id="body"
          rows="10"
          required
          class="w-full rounded-xl bg-white/90 dark:bg-stone-800/60
                 ring-1 ring-black/10 dark:ring-white/10
                 px-3 py-2 text-stone-900 dark:text-stone-100 placeholder-stone-400
                 outline-none focus:ring-2 focus:ring-sky-400/40">{{ old('body', $thread->body) }}</textarea>
      </div>

      {{-- Submit --}}
      <div class="flex items-center justify-center gap-4 pt-2">
        <button
          type="submit"
          class="rounded-xl bg-red-600 px-5 py-2.5 font-semibold text-white
                 hover:bg-red-700 active:translate-y-px transition">
          Update Thread
        </button>
        <a href="{{ route('threads.show', $thread) }}"
           class="text-sm text-stone-600 dark:text-stone-400 underline">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>
@endsection