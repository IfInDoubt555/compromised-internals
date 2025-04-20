@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Rally Blog 🏁</h1>
    @auth
        <div class="mb-6">
            <a href="{{ route('posts.create') }}" class="inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 mb-6">
                + New Post
            </a>
        </div>
    @endauth

    <div class="grid md:grid-cols-2 gap-6">
        @foreach($posts as $post)
            <article class="bg-white rounded-xl shadow p-4 transition hover:shadow-md hover:-translate-y-1">
                @if ($post->image_path)
                    <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="rounded-lg shadow-md">
                @else
                    <img src="{{ asset('images/default-post.png') }}" alt="Default Post Image" class="rounded-lg shadow-md">
                @endif

                <div class="flex items-center gap-3 mt-4 mb-2">
                    <a href="{{ route('profile.public', $post->user->id) }}">
                        <x-user-avatar :user="$post->user" size="w-10 h-10" />
                    </a>
                    <div>
                        <p class="font-semibold text-sm">{{ $post->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                    </div>
                </div>

                <h2 class="text-xl font-semibold mb-2">
                    <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-red-600 transition">
                        {{ $post->title }}
                    </a>
                </h2>

                <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>

                <a href="{{ route('posts.show', $post->slug) }}" class="inline-block px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700">
                    Read More
                </a>

                @can('update', $post)
                    <div class="mt-4 flex gap-4">
                        <a href="{{ route('posts.edit', $post) }}" class="text-yellow-500 hover:underline">✏️ Edit</a>

                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">🗑️ Delete</button>
                        </form>
                    </div>
                @endcan
            </article>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection