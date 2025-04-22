@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-center items-center gap-2 mb-6">
      <h1 class="text-3xl font-bold">Rally Blog</h1>
      <span class="text-3xl inline-block animate-floatWave origin-bottom-left">🏁</span>
    </div>

    @auth
    {{-- Floating button, bottom right --}}
    <a href="{{ route('posts.create') }}" 
            class="fixed bottom-6 right-6 bg-red-600 hover:bg-red-700 text-white rounded-full p-4 shadow-lg z-50 transition"
            title="New Post">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" 
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14m7-7H5"/>
            </svg>
        </a>
    @endauth

    <div class="grid md:grid-cols-2 gap-6">
        @foreach($posts as $post)
            <article class="bg-white rounded-xl shadow p-4 transition hover:shadow-md hover:-translate-y-1">
                @if ($post->image_path)
                    <img
                        src="{{ Storage::url($post->image_path) }}" 
                        alt="{{ $post->title }}" 
                        class="w-full h-64 object-cover rounded-lg shadow-md"
                        >
                @else
                    <img 
                        src="{{ asset('images/default-post.png') }}" 
                        alt="Default Post Image" 
                        class="w-full h-64 object-cover rounded-lg shadow-md"
                    >
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

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection