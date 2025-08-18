@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-12 p-8 bg-white shadow-xl rounded-2xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Edit Thread ✏️</h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
            <ul class="list-disc pl-6 space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('threads.update', $thread) }}" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- Discussion Board (optional move) --}}
        @php
            $boards = \App\Models\Board::orderBy('position')->get();
            $selectedBoardId = old('board_id', $thread->board_id);
        @endphp
        <div>
            <label for="board_id" class="block text-sm font-medium text-gray-700 mb-1">
                Move to Board (optional)
            </label>
            <select
                id="board_id"
                name="board_id"
                class="w-full px-4 py-2 rounded-xl border border-gray-300 bg-white focus:ring focus:ring-blue-200 focus:border-blue-400"
            >
                @foreach($boards as $b)
                    <option value="{{ $b->id }}" @selected($selectedBoardId == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
            @error('board_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title', $thread->title) }}"
                required
                class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
            >
        </div>

        {{-- Slug (same component you use for posts) --}}
        @if (View::exists('components.form.slug-field'))
            <x-form.slug-field :slug="$thread->slug" :defaultMode="'manual'" />
        @else
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (optional)</label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    value="{{ old('slug', $thread->slug) }}"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                    placeholder="auto-generated from title if left blank"
                >
            </div>
        @endif

        {{-- Body --}}
        <div>
            <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
            <textarea
                name="body"
                id="body"
                rows="8"
                required
                class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
            >{{ old('body', $thread->body) }}</textarea>
        </div>

        {{-- Submit --}}
        <div class="text-center">
            <button
                type="submit"
                class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow hover:bg-red-700 transition"
            >
                Update Thread 🏁
            </button>
            <a href="{{ route('threads.show', $thread) }}" class="ml-4 text-sm text-gray-600 underline">Cancel</a>
        </div>
    </form>
</div>
@endsection