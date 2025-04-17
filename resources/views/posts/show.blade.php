@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ $post->title }}</h1>

<!-- debug section for figuing out if auth is working 
    <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg mb-6">
        <p>Post User ID: {{ $post->user_id }}</p>
        <p>Logged In User ID: {{ auth()->id() }}</p>
    </div>
-->
    @if ($post->image_path)
        <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="rounded-lg shadow-md">
    @else
        <img src="{{ asset('images/default-post.webp') }}" alt="Default Image" class="rounded-lg shadow-md">
    @endif

    <div class="prose max-w-none">
        {!! nl2br(e($post->body)) !!}
    </div>

    @can('update', $post)
        <div class="mt-6 flex gap-4">
            <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">✏️ Edit</a>

            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">🗑️ Delete</button>
            </form>
        </div>
    @endcan
</div>
@endsection
