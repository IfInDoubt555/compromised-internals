@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Blog – Latest Rally News & Articles | Compromised Internals',
        'description' => 'Dive into the Compromised Internals Rally Blog for the latest news, in-depth articles, event coverage, and expert analysis on drivers, cars, and competitions.',
        'url'         => url()->current(),
        'image'       => asset('images/default-post.png'),
    ];
@endphp

@push('head')
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ $seo['url'] }}">
    <meta property="og:title"       content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image"       content="{{ $seo['image'] }}">

    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:url"         content="{{ $seo['url'] }}">
    <meta name="twitter:title"       content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image"       content="{{ $seo['image'] }}">
@endpush

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

    <form method="GET" action="{{ route('blog.index') }}" class="mb-6 text-center">
        <input
            type="text"
            name="tag"
            placeholder="Search posts by tag (e.g. subaru)"
            value="{{ request('tag') }}"
            class="px-4 py-2 w-80 border border-gray-300 rounded-l-md shadow focus:outline-none focus:ring">
        <button
            type="submit"
            class="px-4 py-2 bg-red-600 text-white rounded-r-md hover:bg-red-700 font-semibold">
            Search
        </button>
    </form>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <article class="bg-white-700 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col">
            <!-- Image -->
            <div class="h-64 w-full flex items-center justify-center overflow-hidden bg-black/5 hover:bg-black/10 transition-colors duration-300">
                @if ($post->image_path && Storage::disk('public')->exists($post->image_path))
                <img src="{{ Storage::url($post->image_path) }}"
                    alt="{{ $post->title }}"
                    class="w-full h-full object-cover" />
                @else
                <img src="{{ asset('images/default-post.png') }}"
                    alt="Default Blog Post Image"
                    class="w-full h-full object-cover" />
                @endif
            </div>

            <!-- Post Info -->
            <div class="p-4 flex flex-col flex-grow space-y-3 bg-gray-300">
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
                    <a href="{{ route('posts.edit', $post) }}" class="text-green-800 hover:underline">✏️ Edit</a>

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