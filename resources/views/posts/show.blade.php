@extends('layouts.app')

@section('content')
{{-- Navigation --}}
<div class="max-w-6xl mx-auto px-4 mt-6 mb-6 flex justify-between items-center text-sm font-semibold">
    <div>
        @if ($next)
            <a href="{{ route('blog.show', $next->slug) }}" class="text-green-800 hover:text-green-950 hover:underline">
                ← Next Post
            </a>
        @endif
    </div>
    <div>
        <a href="{{ route('blog.index') }}" class="text-blue-600 hover:underline">
            Back to Blog
        </a>
    </div>
    <div>
        @if ($previous)
            <a href="{{ route('blog.show', $previous->slug) }}" class="text-red-800 hover:text-red-950 hover:underline">
                Previous Post →
            </a>
        @endif
    </div>
</div>

{{-- Main Content --}}
<div class="flex flex-col md:flex-row gap-8 mb-12 max-w-6xl mx-auto px-4">

    {{-- Left: Post Image --}}
    <div class="md:w-[40%] max-w-md">
        <img
            src="{{ $post->image_path ? Storage::url($post->image_path) : asset('images/default-post.png') }}"
            alt="{{ $post->title }}"
            class="rounded-lg shadow-md w-full object-cover">
    </div>

    {{-- Right: Title, Author, Actions, Body --}}
    <div class="flex-1 max-w-2xl">

        {{-- Breadcrumb back to the board (only if linked) --}}
        @if($post->board)
            <div class="mb-2">
                <a href="{{ route('boards.show', $post->board->slug) }}"
                   class="text-xs text-red-600 hover:underline">
                    ← {{ $post->board->name }}
                </a>
            </div>
        @endif

        <h1 class="text-3xl font-bold mb-4">{{ $post->title }}</h1>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            {{-- Author Info --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.public', $post->user->id) }}">
                    <x-user-avatar :user="$post->user" size="w-14 h-14" />
                </a>
                <div>
                    <p class="font-semibold text-sm">{{ $post->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                </div>
            </div>

            {{-- Like Button --}}
            @php $user = auth()->user(); @endphp
            <form method="POST" action="{{ route('posts.like', $post) }}">
                @csrf
                <button type="submit" class="text-pink-600 hover:text-pink-800 text-sm" {{ !$user ? 'disabled' : '' }}>
                    ❤️ {{ $post->likes()->count() }}
                    {{ $user && $post->isLikedBy($user) ? 'Unlike' : 'Like' }}
                </button>
            </form>

            {{-- Actions --}}
            @can('update', $post)
                <div class="flex gap-3">
                    <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        ✏️ Edit
                    </a>
                    <form action="{{ route('posts.destroy', $post) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            @endcan
        </div>

        {{-- Body --}}
        <div class="prose max-w-none text-gray-800 mt-2 text-lg leading-relaxed bg-white/45 backdrop-blur-md rounded-xl shadow-xl p-4">
            {!! nl2br(e($post->body)) !!}
        </div>
    </div>
</div>

{{-- Comment Section --}}
<div class="max-w-4xl mb-6 mx-auto px-4">
    @if ($errors->has('body'))
        <div class="mt-2 flex items-start gap-2 animate-fade-in rounded-md border border-red-500 bg-red-100 dark:bg-red-900 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 shadow-md">
            <span class="text-lg">⚠️</span>
            <span>{{ $errors->first('body') }}</span>
        </div>
    @endif

    {{-- Form --}}
    @auth
        <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-6">
            @csrf
            <textarea name="body" rows="3"
                class="w-full p-3 rounded border shadow-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500
                    bg-gray-300 border-gray-300
                    @error('body') border-red-500 ring-red-500 bg-red-50 text-red-900 dark:bg-red-950 dark:text-red-100 @enderror"
                placeholder="Leave a comment...">{{ old('body') }}</textarea>
            <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Comment
            </button>
        </form>
    @endauth

    {{-- 💬 Comment List --}}
    @if ($post->comments->count())
        <div class="space-y-4 mb-6 mt-10">
            <h2 class="text-xl font-bold">Comments</h2>

            @foreach ($post->comments as $comment)
                <div class="bg-gray-300 rounded-lg shadow p-4 border border-gray-200"
                     x-data="{ editing: false, body: '{{ addslashes($comment->body) }}' }">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-semibold text-gray-800">
                            {{ $comment->user->name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $comment->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Body / Edit Field --}}
                    <div class="mb-3">
                        <p x-show="!editing" x-text="body" class="text-gray-900"></p>

                        @can('update', $comment)
                            <form x-show="editing" method="POST" action="{{ route('comments.update', $comment) }}" class="mt-2 flex flex-col gap-2">
                                @csrf
                                <input type="text" name="body" x-model="body" class="border rounded px-2 py-1 text-sm w-full">
                                <div class="flex gap-2">
                                    <button type="submit" class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600">✔️ Update</button>
                                    <button type="button" @click="editing = false" class="text-sm text-gray-500 hover:underline">Cancel</button>
                                </div>
                            </form>
                        @endcan
                    </div>

                    {{-- Edit & Delete Controls --}}
                    <div class="flex gap-3">
                        @can('update', $comment)
                            <button @click="editing = !editing" class="text-sm text-yellow-600 hover:underline">✏️ Edit</button>
                        @endcan

                        @can('delete', $comment)
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">🗑️ Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-6 mb-6 text-gray-500 italic">No comments yet. Be the first to chime in!</p>
    @endif
</div>
@endsection