@props([
    'action',                 // route to submit to
    'title' => 'Create Post', // heading
    'submitLabel' => 'Publish',
    'board' => null,          // optional \App\Models\Board
    'model' => null,          // optional editing model (Post/Thread)
])

@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-12 mb-12">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">{{ $title }}</h1>

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

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- If we’re inside a board, carry that context along --}}
            @if($board)
                <input type="hidden" name="board_id" value="{{ $board->id }}">
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm">
                    Posting to board: <span class="font-semibold">{{ $board->name }}</span>
                </div>
            @endif

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title', optional($model)->title) }}"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                    required
                >
            </div>

            {{-- Slug (reuse your component) --}}
            <x-form.slug-field :value="old('slug', optional($model)->slug)" />

            {{-- Body --}}
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
                <textarea
                    name="body" id="body" rows="8" required
                    placeholder="Write your post..."
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                >{{ old('body', optional($model)->body) }}</textarea>
            </div>

            {{-- Image (optional) --}}
            <div>
                <label for="image_path" class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                <input
                    type="file"
                    name="image_path" id="image_path"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-400 bg-white"
                >
            </div>

            <div class="text-center">
                <button type="submit"
                    class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow hover:bg-red-700 transition">
                    {{ $submitLabel }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection