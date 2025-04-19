@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-12 p-8 bg-white shadow-xl rounded-2xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Edit Post ✏️</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
            <ul class="list-disc pl-6 space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $post->title) }}"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 focus:outline-none transition bg-white"
                    required
                >
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input 
                    type="text" 
                    name="slug" 
                    id="slug" 
                    value="{{ old('slug', $post->slug) }}"
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 focus:outline-none transition bg-white"
                    required
                >
                <small class="text-gray-500">Use lowercase letters and dashes (e.g. rally-winter-blast)</small>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                <textarea 
                    name="excerpt" 
                    id="excerpt" 
                    rows="2" 
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 focus:outline-none transition bg-white"
                >{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <!-- Body -->
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
                <textarea 
                    name="body" 
                    id="body" 
                    rows="8" 
                    required 
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 focus:outline-none transition bg-white"
                >{{ old('body', $post->body) }}</textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                <input 
                    type="file" 
                    name="image_path" 
                    id="image_path" 
                    class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring focus:ring-blue-200 focus:outline-none transition bg-white"
                >
            </div>

            <!-- Update Button -->
            <div class="text-center">
                <button 
                    type="submit" 
                    class="inline-block px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow hover:bg-red-700 transition"
                >
                    Update Post 🏁
                </button>
            </div>
        </form>
    </div>
</div>
@endsection