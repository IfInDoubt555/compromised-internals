@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Create New Post</h1>

    <form method="POST" action="{{ route('blog.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block font-bold">Title</label>
            <input name="title" class="w-full border rounded px-3 py-2" value="{{ old('title') }}" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Slug</label>
            <input name="slug" class="w-full border rounded px-3 py-2" value="{{ old('slug') }}" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Excerpt</label>
            <textarea name="excerpt" class="w-full border rounded px-3 py-2" rows="2">{{ old('excerpt') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Body</label>
            <textarea name="body" class="w-full border rounded px-3 py-2" rows="5">{{ old('body') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Image</label>
            <input type="file" name="image_path">
        </div>

        <button class="bg-red-600 text-white px-4 py-2 rounded">Publish</button>
    </form>
</div>
@endsection
