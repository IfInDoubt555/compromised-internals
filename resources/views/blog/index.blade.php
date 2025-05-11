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
            <path d="M12 5v14m7-7H5" />
        </svg>
    </a>
    @endauth

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <article class="bg-white-700 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <!-- Image -->
            <div class="h-64 w-full flex items-center justify-center overflow-hidden bg-black/5 hover:bg-black/10 transition-colors duration-300">
                @if ($post->image_path && Storage::exists($post->image_path))
                <img src="{{ Storage::url($post->image_path) }}"
                    alt="{{ $post->title }}"
                    class="max-h-full max-w-full object-contain" />
                @else
                <img src="{{ asset('images/default-post.png') }}"
                    alt="Default Post Image"
                    title="{{ $post->title ?? 'Default Blog Post Image' }}"
                    class="max-h-full max-w-full object-contain" />
                @endif
            </div>

            <!-- Post Info -->
            <div class="p-4 flex flex-col flex-grow space-y-3 bg-gray-400">
                <!-- Author Row -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.public', $post->user->id) }}">
                            <x-user-avatar :user="$post->user" size="w-14 h-14" />
                        </a>
                        <div>
                            <p class="font-semibold text-sm">{{ $post->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                        </div>
                    </div>

                    <a href="{{ route('posts.show', $post->slug) }}" class="px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded hover:bg-red-700">
                        Read More
                    </a>
                </div>

                <!-- Title -->
                <h2 class="text-lg font-bold text-gray-900">
                    <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-red-600 transition">
                        {{ $post->title }}
                    </a>
                </h2>

                <!-- Excerpt -->
                <p class="text-gray-600 text-center flex-grow">{{ $post->excerpt }}</p>

                <!-- Admin Options -->
                @can('update', $post)
                <div class="mt-2 flex gap-4 text-sm">
                    <a href="{{ route('posts.edit', $post) }}" class="text-yellow-500 hover:underline">✏️ Edit</a>

                    <form action="{{ route('posts.destroy', $post) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">🗑️ Delete</button>
                    </form>
                </div>
                @endcan
            </div>
        </article>
        @endforeach
    </div>

    <div class="flex justify-center mt-6">
        {{ $posts->links() }}
    </div>
</div>
@endsection