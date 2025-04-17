<!-- resources/views/home.blade.php -->

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white text-gray-900 font-sans">

  <!-- Hero Section -->

  <header class="relative bg-gray-900 text-white">
    <img src="{{ asset('images/vintagerally.webp') }}" 
      alt="Rally Hero" 
      class="w-full h-[30em] object-cover object-center opacity-60">
    <div class="absolute inset-0 flex items-center justify-center flex-col text-center">
        <h2 class="text-4xl font-bold">Welcome to Compromised Internals</h2>
        <p class="mt-2 text-lg max-w-xl">Your one-stop hub for everything rally – news, history, events, and more.</p>
        <a href="{{ route('blog.index') }}" class="bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700 transition">
        Explore the Blog
        </a>
    </div>
</header>

<!--
  <header class="bg-gray-100 p-8 text-center">
    <h2 class="text-4xl font-extrabold mb-2">Latest Rally News & Features</h2>
    <p class="text-lg text-gray-600">Follow the FIA, local events, and the world of rally from one place.</p>
  </header>
-->

  <!-- Blog Cards Section -->
  <section class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
@foreach($posts as $post)
<div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
  @if ($post->image_path)
    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="h-64 w-full object-cover rounded-t-lg">
  @else
    <img src="{{ asset('images/default-post.webp') }}" alt="Default Image" class="h-64 w-full object-cover rounded-t-lg">
  @endif
  <div class="p-4 flex flex-col flex-grow">
        <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $post->title }}</h2>
        <p class="text-gray-600 flex-grow">{{ $post->summary }}</p>
    </div>
    <div class="p-4">
        <a href="{{ route('blog.show', $post->slug) }}" class="inline-block px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700">
            Read More
        </a>
    </div>
  </div>
@endforeach

  </section>

  <!-- Footer -->
  <footer class="bg-gray-100 p-4 text-center text-sm text-gray-500 mt-12">
    &copy; 2025 Compromised Internals. All rights reserved.
  </footer>
</div>
@endsection