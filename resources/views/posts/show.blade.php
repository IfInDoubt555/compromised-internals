@extends('layouts.app')

@section('content')
@php
  // Context: theme + auth
  $user  = auth()->user();
  $liked = $user ? $post->isLikedBy($user) : false;

  // Reusable themed button classes from Board model helper
  // Fallback to a sky accent if the post has no board
  $btn = $post->board?->accentButtonClasses()
      ?? 'border border-sky-400 text-sky-700 bg-sky-100 hover:bg-sky-200 ring-1 ring-sky-500/20 dark:border-sky-600 dark:text-sky-300 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 dark:ring-sky-400/20';
@endphp

{{-- Top nav --}}
<div class="max-w-5xl mx-auto px-4 mt-6 mb-6 grid grid-cols-3 text-sm font-semibold">
  <div>
    @if ($next)
      <a href="{{ route('blog.show', $next->slug) }}"
         class="text-emerald-800 hover:text-emerald-900 hover:underline dark:text-emerald-300 dark:hover:text-emerald-200">
        ← Next Post
      </a>
    @endif
  </div>
  <div class="text-center">
    <a href="{{ route('blog.index') }}"
       class="text-blue-600 hover:underline dark:text-sky-300 dark:hover:text-sky-200">
      Back to Blog
    </a>
  </div>
  <div class="text-right">
    @if ($previous)
      <a href="{{ route('blog.show', $previous->slug) }}"
         class="text-rose-800 hover:text-rose-900 hover:underline dark:text-rose-300 dark:hover:text-rose-200">
        Previous Post →
      </a>
    @endif
  </div>
</div>

{{-- Feature image + title --}}
<div class="max-w-5xl mx-auto px-4">
  <figure class="rounded-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-xl">
    <img
      src="{{ $post->image_path ? Storage::url($post->image_path) : asset('images/default-post.png') }}"
      alt="{{ $post->title }}"
      class="w-full h-auto object-cover">
  </figure>

  <h1 class="mt-6 text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-stone-100">
    {{ $post->title }}
  </h1>
</div>

{{-- Meta bar (author chip | actions | board pill) --}}
<div class="max-w-5xl mx-auto px-4 mt-3">
  <div class="flex flex-wrap items-center gap-3">
    {{-- Author chip --}}
    <a href="{{ route('profile.public', $post->user->id) }}"
       class="group inline-flex items-center gap-3 rounded-xl border px-3 py-2
              bg-white/80 text-gray-900 border-gray-200 shadow-sm
              hover:bg-white hover:shadow
              dark:bg-stone-900/70 dark:text-stone-100 dark:border-white/10">
      <x-user-avatar :user="$post->user" size="w-8 h-8" />
      <div class="leading-tight">
        <div class="text-sm font-semibold group-hover:underline">{{ $post->user->name }}</div>
        <div class="text-xs text-gray-500 dark:text-stone-400">{{ $post->created_at->format('M j, Y') }}</div>
      </div>
    </a>

    {{-- Actions --}}
    <div class="order-3 w-full sm:order-none sm:w-auto">
      <div class="flex flex-wrap items-center gap-2">
        {{-- Like --}}
        <form method="POST" action="{{ route('posts.like', $post) }}">
          @csrf
          <button type="submit"
                  {{ !$user ? 'disabled' : '' }}
                  aria-pressed="{{ $liked ? 'true' : 'false' }}"
                  title="{{ $liked ? 'Unlike' : 'Like' }}"
                  class="inline-flex items-center gap-2 rounded-lg px-3 py-2 {{ $btn }} disabled:opacity-50">
            {{ $post->likes()->count() }}
            <span>{{ $liked ? 'Unlike' : 'Like' }}</span>
          </button>
        </form>

        @can('update', $post)
          {{-- Edit --}}
          <a href="{{ route('posts.edit', $post) }}"
             class="inline-flex items-center rounded-lg px-3 py-2 {{ $btn }}">
            Edit
          </a>

          {{-- Delete (themed to board color; change to fixed red if desired) --}}
          <form action="{{ route('posts.destroy', $post) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this post?');">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center rounded-lg px-3 py-2 {{ $btn }}">
              Delete
            </button>
          </form>
        @endcan
      </div>
    </div>

    {{-- Board pill (push to right on desktop) --}}
    <div class="sm:ml-auto">
      @if($post->board)
        <a href="{{ route('boards.show', $post->board->slug) }}"
           class="inline-block rounded-md border px-2 py-1 text-xs
                  bg-white text-gray-700 border-gray-300 hover:underline
                  dark:bg-stone-800/60 dark:text-stone-200 dark:border-white/10">
          {{ $post->board->name }}
        </a>
      @endif
    </div>
  </div>
</div>

{{-- Article --}}
<div class="max-w-5xl mx-auto px-4 mt-8 mb-12">
  <article
    class="prose max-w-4xl mx-auto text-gray-800 text-[1rem] leading-relaxed
           bg-white/50 backdrop-blur-md rounded-xl shadow-xl p-8 ring-1 ring-black/5
           dark:prose-invert dark:text-stone-200 dark:bg-stone-900/70 dark:ring-white/10">
    {!! nl2br(e($post->body)) !!}
  </article>
</div>

{{-- Tags (centered to match article width) --}}
@if(method_exists($post,'tags') && $post->tags->count())
  <div class="max-w-5xl mx-auto px-4 mb-10">
    <div class="max-w-4xl mx-auto rounded-xl border border-gray-200 bg-white/80 p-4 shadow
                dark:bg-stone-900/70 dark:border-white/10">
      <h3 class="text-sm font-semibold mb-3 text-gray-900 dark:text-stone-100">Tags</h3>
      <div class="flex flex-wrap gap-2">
        @foreach($post->tags as $tag)
          <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
             class="text-xs px-2 py-1 rounded-lg border bg-gray-100 border-gray-300 text-gray-800
                    hover:underline
                    dark:bg-stone-700 dark:border-stone-600 dark:text-stone-100">
            {{ $tag->name ?? $tag->slug }}
          </a>
        @endforeach
      </div>
    </div>
  </div>
@endif

{{-- Comments (aligned with article width) --}}
<div class="max-w-5xl mx-auto px-4 mb-12">
  <div class="max-w-4xl mx-auto">
    @if ($errors->has('body'))
      <div
        class="mt-2 flex items-start gap-2 animate-fade-in rounded-md border border-red-500 bg-red-100 dark:bg-red-900 px-4 py-3 text-sm font-medium text-red-800 dark:text-red-200 shadow-md">
        <span class="text-lg">⚠️</span>
        <span>{{ $errors->first('body') }}</span>
      </div>
    @endif

    @auth
      @php $invalid = $errors->has('body'); @endphp
      <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-6">
        @csrf
        <textarea
          name="body" rows="3" aria-invalid="{{ $invalid ? 'true' : 'false' }}"
          @class([
            'w-full p-3 rounded shadow-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500',
            'border bg-gray-300 border-gray-300 dark:bg-stone-800/60 dark:border-white/10 dark:text-stone-100 dark:placeholder-stone-500' => ! $invalid,
            'border bg-red-50 border-red-500 text-red-900 ring-red-500 dark:bg-red-950 dark:text-red-100 dark:border-red-500' => $invalid,
          ])
          placeholder="Leave a comment...">{{ old('body') }}</textarea>
        @error('body')
          <p class="mt-2 text-sm text-red-600 dark:text-red-300">{{ $message }}</p>
        @enderror
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Comment
        </button>
      </form>
    @endauth

    @if ($post->comments->count())
      <div class="space-y-4 mb-6 mt-10">
        <h2 class="text-xl font-bold text-slate-900 dark:text-stone-100">Comments</h2>

        @foreach ($post->comments as $comment)
          <div class="rounded-lg shadow p-4 border border-gray-200 bg-gray-300
                      dark:bg-stone-900/60 dark:border-white/10 dark:text-stone-200"
               x-data="{ editing: false, body: '{{ addslashes($comment->body) }}' }">
            <div class="flex items-center justify-between mb-2">
              <div class="text-sm font-semibold text-gray-800 dark:text-stone-100">
                {{ $comment->user->name }}
              </div>
              <div class="text-xs text-gray-500 dark:text-stone-400">
                {{ $comment->created_at->diffForHumans() }}
              </div>
            </div>

            <div class="mb-3">
              <p x-show="!editing" x-text="body" class="text-gray-900 dark:text-stone-100"></p>

              @can('update', $comment)
                <form x-show="editing" method="POST" action="{{ route('comments.update', $comment) }}"
                      class="mt-2 flex flex-col gap-2">
                  @csrf
                  <input type="text" name="body" x-model="body"
                         class="border rounded px-2 py-1 text-sm w-full
                                border-gray-300 dark:border-white/10
                                bg-white dark:bg-stone-800/60
                                text-slate-900 dark:text-stone-100">
                  <div class="flex gap-2">
                    <button type="submit"
                            class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600">
                      Update
                    </button>
                    <button type="button" @click="editing = false"
                            class="text-sm text-gray-500 dark:text-stone-400 hover:underline">
                      Cancel
                    </button>
                  </div>
                </form>
              @endcan
            </div>

            <div class="flex gap-3">
              @can('update', $comment)
                <button @click="editing = !editing"
                        class="text-sm text-amber-600 hover:underline dark:text-amber-300 dark:hover:text-amber-200">
                  Edit
                </button>
              @endcan
              @can('delete', $comment)
                <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                      onsubmit="return confirm('Delete this comment?')">
                  @csrf @method('DELETE')
                  <button type="submit"
                          class="text-sm text-red-600 hover:underline dark:text-rose-300 dark:hover:text-rose-200">
                    Delete
                  </button>
                </form>
              @endcan
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="mt-6 mb-6 text-gray-500 italic dark:text-stone-400">No comments yet. Be the first to chime in!</p>
    @endif
  </div>
</div>
@endsection