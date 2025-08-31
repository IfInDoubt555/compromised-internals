@extends('layouts.app')

@section('content')
{{-- Top nav --}}
<div class="max-w-6xl mx-auto px-4 mt-6 mb-6 grid grid-cols-3 text-sm font-semibold">
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
<div class="max-w-4xl mx-auto px-4">
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

{{-- Meta row (author card | actions | board pill) --}}
<div class="max-w-4xl mx-auto px-4 mt-4">
  <div class="grid gap-4 sm:grid-cols-[1fr_auto_auto] items-start">
    {{-- Author card (left) --}}
    <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow
                dark:bg-stone-900/70 dark:border-white/10">
      <h3 class="text-sm font-semibold mb-3 text-gray-900 dark:text-stone-100">About the author</h3>
      <div class="flex items-center gap-3">
        <a href="{{ route('profile.public', $post->user->id) }}">
          <x-user-avatar :user="$post->user" size="w-10 h-10" />
        </a>
        <div class="leading-tight">
          <p class="text-sm font-medium text-gray-900 dark:text-stone-100">
            {{ $post->user->name }}
          </p>
          <p class="text-xs text-gray-500 dark:text-stone-400">
            {{ $post->created_at->format('M j, Y') }}
          </p>
          <a href="{{ route('profile.public', $post->user->id) }}"
             class="text-xs text-blue-600 hover:underline dark:text-sky-300 dark:hover:text-sky-200">
            View profile
          </a>
        </div>
      </div>
    </div>

    {{-- Actions (middle) --}}
    <div class="flex flex-col gap-2 sm:w-44">
      @php $user = auth()->user(); @endphp
      <form method="POST" action="{{ route('posts.like', $post) }}">
        @csrf
        <button type="submit"
          class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg
                 bg-rose-600 text-white hover:bg-rose-500 disabled:opacity-50">
          ❤️ {{ $post->likes()->count() }}
          <span>{{ $user && $post->isLikedBy($user) ? 'Unlike' : 'Like' }}</span>
        </button>
      </form>

      @can('update', $post)
        <a href="{{ route('posts.edit', $post) }}"
           class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg
                  bg-amber-500 text-white hover:bg-amber-600">✏️ Edit</a>

        <form action="{{ route('posts.destroy', $post) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to delete this post?');">
          @csrf @method('DELETE')
          <button type="submit"
            class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg
                   bg-red-600 text-white hover:bg-red-700">🗑️ Delete</button>
        </form>
      @endcan
    </div>

    {{-- Board pill (right) --}}
    <div class="sm:justify-self-end">
      @if($post->board)
        <a href="{{ route('boards.show', $post->board->slug) }}"
           class="inline-block px-2 py-1 rounded-md border border-gray-300 bg-white text-gray-700
                  hover:underline text-xs
                  dark:bg-stone-800/60 dark:text-stone-200 dark:border-white/10">
          {{ $post->board->name }}
        </a>
      @endif
    </div>
  </div>
</div>

{{-- Article + Sidebar --}}
<div class="max-w-6xl mx-auto px-4 mt-8 mb-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
  {{-- Article --}}
  <article
    class="lg:col-span-2 prose max-w-none text-gray-800 text-[0.975rem] leading-relaxed
           bg-white/45 backdrop-blur-md rounded-xl shadow-xl p-5 ring-1 ring-black/5
           dark:prose-invert dark:text-stone-200 dark:bg-stone-900/70 dark:ring-white/10">
    {!! nl2br(e($post->body)) !!}
  </article>

  {{-- Sidebar --}}
  <aside class="lg:col-span-1 space-y-6">

    {{-- Tags --}}
    @if(method_exists($post,'tags') && $post->tags->count())
      <div class="rounded-xl border border-gray-200 bg-white/80 p-4 shadow
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
    @endif
  </aside>
</div>

{{-- Comments --}}
<div class="max-w-4xl mx-auto px-4 mb-12">
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
@endsection