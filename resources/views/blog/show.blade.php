@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
<h1 class="text-3xl font-bold mb-6 text-center">{{ $post['title'] }}</h1>
    <img src="{{ asset($post['image_path']) }}" alt="{{ $post['title'] }}" class="rounded-lg shadow mb-6">
    <p class="text-gray-700 leading-relaxed">{{ $post['body'] }}</p>

    <a href="{{ route('blog.index') }}" class="text-blue-600 mt-6 inline-block hover:underline">&larr; Back to Blog</a>
</div>
@endsection
