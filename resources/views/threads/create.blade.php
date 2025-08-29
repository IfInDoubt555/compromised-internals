@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <a href="{{ route('boards.show', $board->slug) }}"
       class="text-sm text-red-600 dark:text-rose-300 hover:underline">
       ← {{ $board->name }}
    </a>

    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-stone-100">
        Start a New Thread
    </h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700
                    dark:bg-red-900/50 dark:border-red-400/30 dark:text-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-4 rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm text-gray-700
                dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-200">
        Posting to board:
        <a href="{{ route('boards.show', $board->slug) }}"
           class="font-semibold underline text-gray-900 dark:text-stone-100">
            {{ $board->name }}
        </a>
    </div>

    <form action="{{ route('threads.store', $board->slug) }}" method="POST" class="mt-6 space-y-4">
        @csrf

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-semibold mb-1 text-gray-800 dark:text-stone-300">Title</label>
            <input
                id="title"
                name="title"
                value="{{ old('title') }}"
                required
                maxlength="140"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none focus:ring
                       dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100
                       dark:placeholder-stone-500 dark:focus:ring-sky-400/30 dark:focus:border-sky-400/30"
            >
        </div>

        {{-- Optional: slug (component) --}}
        <x-form.slug-field :value="old('slug')" />

        {{-- Body --}}
        <div>
            <label for="body" class="block text-sm font-semibold mb-1 text-gray-800 dark:text-stone-300">Body</label>
            <textarea
                id="body"
                name="body"
                rows="10"
                required
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none focus:ring
                       dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100
                       dark:placeholder-stone-500 dark:focus:ring-sky-400/30 dark:focus:border-sky-400/30"
            >{{ old('body') }}</textarea>
        </div>

        <div class="flex justify-end">
            <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">
                Post Thread
            </button>
        </div>
    </form>
</div>
@endsection