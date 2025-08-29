@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  {{-- Back link --}}
  <a href="{{ route('boards.show', $board->slug) }}"
     class="text-sm text-red-600 dark:text-rose-300 hover:underline">
     ← {{ $board->name }}
  </a>

  {{-- Card --}}
  <div
    class="mt-4 rounded-2xl p-8 shadow-xl bg-white/90 ring-1 ring-black/5 backdrop-blur
           transition-shadow
           hover:shadow-[0_0_60px_rgba(2,132,199,0.18)]
           focus-within:shadow-[0_0_60px_rgba(2,132,199,0.18)]
           dark:bg-stone-900/80 dark:ring-white/10
           dark:hover:shadow-[0_0_60px_rgba(56,189,248,0.20)]
           dark:focus-within:shadow-[0_0_60px_rgba(56,189,248,0.20)]">

    <h1 class="text-2xl font-bold text-stone-900 dark:text-stone-100">Start a New Thread</h1>

    {{-- Posting to --}}
    <div class="mt-3 rounded-lg bg-stone-50 border border-stone-200 p-3 text-sm text-stone-700
                dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
      Posting to board:
      <a href="{{ route('boards.show', $board->slug) }}"
         class="font-semibold underline text-stone-900 dark:text-stone-100">
        {{ $board->name }}
      </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
      <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700
                  dark:bg-red-900/50 dark:border-red-400/30 dark:text-red-200">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('threads.store', $board->slug) }}" method="POST" class="mt-6 space-y-5">
      @csrf

      {{-- Title --}}
      <div>
        <label for="title" class="block text-sm font-semibold mb-1 text-stone-800 dark:text-stone-300">
          Title
        </label>
        <input
          id="title"
          name="title"
          value="{{ old('title') }}"
          required
          maxlength="140"
          class="w-full rounded-xl bg-white/90 dark:bg-stone-800/60
                 ring-1 ring-black/10 dark:ring-white/10
                 px-3 py-2 text-stone-900 dark:text-stone-100 placeholder-stone-400
                 outline-none focus:ring-2 focus:ring-sky-400/40"
        >
      </div>

      {{-- Optional: slug (component) --}}
      <x-form.slug-field :value="old('slug')" />

      {{-- Body --}}
      <div>
        <label for="body" class="block text-sm font-semibold mb-1 text-stone-800 dark:text-stone-300">
          Body
        </label>
        <textarea
          id="body"
          name="body"
          rows="10"
          required
          class="w-full rounded-xl bg-white/90 dark:bg-stone-800/60
                 ring-1 ring-black/10 dark:ring-white/10
                 px-3 py-2 text-stone-900 dark:text-stone-100 placeholder-stone-400
                 outline-none focus:ring-2 focus:ring-sky-400/40"
        >{{ old('body') }}</textarea>
      </div>

      <div class="flex justify-end">
        <button
          class="rounded-xl bg-red-600 px-5 py-2.5 font-semibold text-white
                 hover:bg-red-700 active:translate-y-px transition"
        >
          Post Thread
        </button>
      </div>
    </form>
  </div>
</div>
@endsection