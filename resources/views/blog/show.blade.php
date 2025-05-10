@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4 text-center">{{ $post->title }}</h1>

    @if ($post->image_path)
        <img
            src="{{ Storage::url($post->image_path) }}"
            alt="{{ $post->title }}"
            class="rounded-lg shadow mb-6 mx-auto">
    @else
        <img
            src="{{ asset('images/default-post.png') }}" 
            alt="Default Image" 
            class="rounded-lg shadow mb-6 mx-auto">
    @endif

    <div class="flex items-center justify-center gap-3 mb-6 text-sm text-gray-500">
        <a href="{{ route('profile.public', $post->user_id) }}" class="flex items-center gap-2">
            <x-user-avatar :user="$post->user" size="w-20 h-20" />
            <span>{{ $post->user->name }}</span>
        </a>
        <span>&bull;</span>
        <span>{{ $post->created_at->format('M j, Y') }}</span>
    </div>

    <div class="prose max-w-none text-gray-800">
        {!! nl2br(e($post->body)) !!}
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline">&larr; Back to Blog</a>
    </div>
</div>
@endsection
