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
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
  <div class="flex justify-center items-center gap-2 mb-6">
      <h1 class="text-3xl font-bold">Rally Blog</h1>
      <span class="text-3xl inline-block animate-floatWave origin-bottom-left">🏁</span>
  </div>

  @auth
  {{-- Floating New Post button --}}
  <a href="{{ route('posts.create') }}"
     class="fixed bottom-6 right-6 bg-red-600 hover:bg-red-700 text-white rounded-full p-4 shadow-lg z-50 transition"
     title="New Post">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 5v14m7-7H5" />
      </svg>
  </a>
  @endauth

  {{-- Layout: sidebar + main --}}
  <div class="grid grid-cols-1 lg:grid-cols-[minmax(260px,320px)_1fr] gap-8">
    {{-- Sidebar --}}
    <aside class="lg:sticky lg:top-24">
      @include('partials.blog-sidebar')
    </aside>

    {{-- Main --}}
    <main>
      {{-- BLOG: Media List Rows --}}
      <div class="mt-2">
        @if($posts->count())
          <ul class="divide-y divide-gray-300/60">
            @foreach($posts as $post)
              <li class="py-6">
                <div class="grid sm:grid-cols-[200px_1fr] gap-6 items-start">
                  {{-- Thumbnail --}}
                  <a href="{{ route('posts.show', $post->slug) }}"
                     class="block aspect-[16/10] overflow-hidden rounded-xl group">
                    <img
                      src="{{ $post->image_path && Storage::disk('public')->exists($post->image_path)
                              ? Storage::url($post->image_path)
                              : asset('images/default-post.png') }}"
                      alt="{{ $post->title }}"
                      class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
                  </a>

                  {{-- Text --}}
                  <div>
                    <div class="flex items-center gap-3 text-xs text-gray-600">
                      <a href="{{ route('profile.public', $post->user->id) }}">
                        <x-user-avatar :user="$post->user" size="w-8 h-8" />
                      </a>
                      <span class="font-medium text-gray-800">{{ $post->user->name }}</span>
                      <span>•</span>
                      <time datetime="{{ $post->created_at->toDateString() }}">
                        {{ $post->created_at->format('M j, Y') }}
                      </time>

                      <a href="{{ route('posts.show', $post->slug) }}"
                         class="ml-auto inline-flex items-center gap-1 text-sm font-semibold text-red-700 hover:underline">
                        Read article →
                      </a>
                    </div>

                    <h2 class="mt-2 font-orbitron text-2xl font-bold text-gray-900">
                      <a href="{{ route('posts.show', $post->slug) }}" class="hover:underline">
                        {{ $post->title }}
                      </a>
                    </h2>

                    <p class="mt-2 text-gray-700">
                      {{ $post->excerpt }}
                    </p>

                    @can('update', $post)
                      <div class="mt-3 flex items-center gap-4 text-sm">
                        <a href="{{ route('posts.edit', $post) }}" class="text-green-800 hover:underline">✏️ Edit</a>
                        <form action="{{ route('posts.destroy', $post) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this post?');">
                          @csrf @method('DELETE')
                          <button type="submit" class="text-red-600 hover:underline">🗑️ Delete</button>
                        </form>
                      </div>
                    @endcan
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-gray-700">No posts yet.</p>
        @endif

        {{-- Pagination --}}
        <div class="flex justify-center mt-8">
          {{ $posts->links() }}
        </div>
      </div>
    </main>
  </div>
</div>
@endsection