@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-12">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Create a New Rally Post 🏁</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                <ul class="list-disc pl-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" required>
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" required>
                <small class="text-gray-500">Use lowercase letters and dashes (e.g. rally-winter-blast)</small>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                <textarea name="excerpt" id="excerpt" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" rows="2" placeholder="Short summary..."></textarea>
            </div>

            <!-- Body -->
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
                <textarea name="body" id="body" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" rows="8" required placeholder="Write your full rally story here..."></textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                <input type="file" name="image" id="image" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200 bg-white">
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="inline-block px-6 py-3 bg-blue-600 text-blue font-semibold rounded-xl shadow hover:bg-blue-700 transition">
                    🚀 Publish Post
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
