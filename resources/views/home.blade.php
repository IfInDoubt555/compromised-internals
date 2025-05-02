<!-- resources/views/home.blade.php -->

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white text-gray-900 font-sans">

  <!-- Hero Section -->

  <header class="relative bg-gray-900 text-white">
    <img src="{{ asset('images/subarubg.webp') }}" 
       alt="Rally Hero" 
       class="w-[1000px] h-[45em] object-cover object-center mx-auto opacity-60">
       <div class="absolute inset-0 flex items-center justify-center">
        <div class="bg-black/30 backdrop-blur-sm rounded-md p-6 text-center text-white">
          <h1 class="text-2xl font-bold">Welcome to Compromised Internals</h1>
          <p class="mt-2">Your one-stop hub for everything rally – news, history, events, and more.</p>
          <a href="/blog" class="inline-block mt-4 px-4 py-2 bg-red-600 hover:bg-red-700 rounded text-white font-semibold">
            Explore the Blog
          </a>
        </div>
</div>

</header>

  <!-- Blog Cards Section -->
    <section class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
        @foreach($posts as $post)
            <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
                <div class="h-64 w-full flex items-center justify-center overflow-hidden rounded-t-lg bg-black/5 hover:bg-black/10  transition-colors    duration-300">
                    @if ($post->image_path)
                        <img src="{{ asset('storage/' . $post->image_path) }}"
                             alt="{{ $post->title }}"
                             class="max-h-full max-w-full object-contain" />
                    @else
                        <img src="{{ asset('images/default-post.png') }}"
                             alt="Default Image"
                             class="max-h-full max-w-full object-contain" />
                    @endif
                </div>

                <div class="p-4 flex flex-col flex-grow space-y-2">
                    <!-- Author & Button Row -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('profile.public', $post->user->id) }}">
                                <x-user-avatar :user="$post->user" size="w-10 h-10" />
                            </a>
                            <div>
                                <p class="font-semibold text-sm">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>

                        <a href="{{ route('posts.show', $post->slug) }}" class="px-4 py-2 bg-red-600 text-white font-semibold text-sm rounded       hover:bg-red-700">
                            Read More
                        </a>
                    </div>

                    <!-- Post Title & Summary -->
                    <h2 class="text-lg font-bold text-gray-900">{{ $post->title }}</h2>
                    <p class="text-gray-600 flex-grow">{{ $post->excerpt }}</p>
                </div>
            </div>
        @endforeach
    </section>
</div>
@endsection