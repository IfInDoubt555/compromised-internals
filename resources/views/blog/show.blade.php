@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4 text-center">{{ $post->title }}</h1>

    @if ($post->image_path && file_exists(public_path('storage/' . $post->image_path)))
    <img src="{{ asset('storage/' . $post->image_path) }}"
        alt="{{ $post->title }}"
        class="max-h-full max-w-full object-contain" />
    @else
    <img src="{{ asset('images/default-post.png') }}"
        alt="Default Post Image"
        title="{{ $post->title ?? 'Default Blog Post Image' }}"
        class="max-h-full max-w-full object-contain" />
    @endif

    <div class="flex items-center justify-center gap-3 mb-6 text-sm text-gray-500">
        <a href="{{ route('profile.public', $post->user_id) }}" class="flex items-center gap-2">
            <x-user-avatar :user="$post->user" size="w-14 h-14" />
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