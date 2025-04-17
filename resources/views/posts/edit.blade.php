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
        @method('PUT')

        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" id="title" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" value="{{ old('title', $post->title) }}" required>
        </div>

        <!-- Slug -->
        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <input type="text" name="slug" id="slug" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" value="{{ old('slug', $post->slug) }}" required>
        </div>

        <!-- Excerpt -->
        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
            <textarea name="excerpt" id="excerpt" class="w-full rounded-xl border-gray-300 focus:ring focus:ring-blue-200" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>