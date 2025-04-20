@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Back to Blog (Top) --}}
    <a href="{{ route('blog.index') }}" class="text-blue-600 mb-3 inline-block hover:underline">
        ← Back to Blog
    </a>

    {{-- Title + Author --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-3xl font-bold">{{ $post->title }}</h1>
        <div class="flex items-center gap-3">
            <x-user-avatar :user="$post->user" size="w-10 h-10" />
            <div>
                <p class="font-semibold text-sm">{{ $post->user->name }}</p>
                <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Image --}}
    @if ($post->image_path)
        <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="rounded-lg shadow-md mb-6">
    @else
        <img src="{{ asset('images/default-post.png') }}" alt="Default Image" class="rounded-lg shadow-md mb-6">
    @endif

    {{-- Body --}}
    <div class="prose max-w-none mb-6">
        {!! nl2br(e($post->body)) !!}
    </div>

    {{-- Author Actions --}}
    @can('update', $post)
    <div class="flex flex-wrap gap-4 items-center mb-4">
        <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            ✏️ Edit
        </a>

        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                🗑️ Delete
            </button>
        </form>
    </div>
    @endcan

    {{-- Back to Blog (Bottom) --}}
    <a href="{{ route('blog.index') }}" class="text-blue-600 mt-2 inline-block hover:underline">
        ← Back to Blog
    </a>

</div>
@endsection