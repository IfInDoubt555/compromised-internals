@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-12 mb-12">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Create a New Rally Post </h1>

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

        {{-- One universal post form --}}
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- If a board context is provided (controller passes $board OR ?board=slug), show banner + hidden field --}}
            @php
                // If controller passed $board use that; otherwise allow query param fallback
                $ctxBoard = isset($board) ? $board : \App\Models\Board::where('slug', request('board'))->first();
            @endphp

            @if($ctxBoard)
                <input type="hidden" name="board_id" value="{{ $ctxBoard->id }}">
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm">
                    Posting to board:
                    <a href="{{ route('boards.show', $ctxBoard->slug) }}" class="font-semibold underline">
                        {{ $ctxBoard->name }}
                    </a>
                    <a href="{{ route('posts.create') }}" class="ml-2 text-xs text-gray-500 underline">change</a>
                </div>
            @else
                {{-- Optional board selector (keeps this the only create form you need) --}}
                <div>
                    <label for="board_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Optional: Post to a Board
                    </label>
                    <select
                        id="board_id"
                        name="board_id"
                        class="w-full px-4 py-2 rounded-xl border border-gray-300 bg-white focus:ring focus:ring-blue-200 focus:border-blue-400"
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
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                    required
                >
            </div>

            {{-- Slug Field (prefill if you like) --}}
            <x-form.slug-field :value="old('slug')" />

            {{-- Body --}}
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
                <textarea
                    name="body"
                    id="body"
                    rows="8"
                    required
                    placeholder="Write your full rally story here..."
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                >{{ old('body') }}</textarea>
            </div>

            {{-- Image --}}
            <div>
                <label for="image_path" class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                <input
                    type="file"
                    name="image_path"
                    id="image_path"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                >
            </div>

            {{-- Submit --}}
            <div class="text-center">
                <button
                    type="submit"
                    class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow hover:bg-red-700 transition"
                >
                    Publish Post 
                </button>
            </div>
        </form>
    </div>
</div>
@endsection