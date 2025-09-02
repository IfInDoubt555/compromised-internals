{{-- Shared renderer for a post article (public + admin preview) --}}
@php
  /** @var \App\Models\Post $post */
  $isPreview    = $isPreview   ?? false;    // admin preview page?
  $showActions  = $showActions ?? ! $isPreview;

  $author = $post->user;
  // Safe defaults so the view never explodes
  $liked = $liked
    ?? (auth()->check() && $post->likes()->where('user_id', auth()->id())->exists());
  $btn = $btn
    ?? 'inline-flex items-center gap-2 rounded-lg px-3 py-2 ring-1 ring-black/5 dark:ring-white/10
        bg-white/80 dark:bg-stone-800/60 text-sm font-semibold text-stone-800 dark:text-stone-100
        hover:bg-white hover:shadow';
@endphp

{{-- Feature image + title --}}
<div class="max-w-5xl mx-auto px-4">
  <figure class="rounded-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-xl">
    <img
      src="{{ $post->image_url }}"
      alt="{{ $post->title }}"
      class="w-full h-auto object-cover aspect-[16/9] sm:aspect-[2/1]"
      loading="lazy"
    />
  </figure>

  <h1 class="mt-6 text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-stone-100">
    {{ $post->title }}
    @if($isPreview && $post->status !== 'published')
      <span class="ml-2 align-middle text-xs font-semibold px-2 py-1 rounded-md
                   bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
        {{ Str::title($post->status ?? 'draft') }} preview
      </span>
    @endif
  </h1>
</div>

{{-- Meta bar --}}
<div class="max-w-5xl mx-auto px-4 mt-3">
  <div class="flex flex-wrap items-center gap-3">

    {{-- Author chip --}}
    <a href="{{ $author ? route('profile.public', $author->id) : '#' }}"
       class="group inline-flex items-center gap-3 rounded-xl border px-3 py-2
              bg-white/80 text-gray-900 border-gray-200 shadow-sm
              hover:bg-white hover:shadow
              dark:bg-stone-900/70 dark:text-stone-100 dark:border-white/10">
      <x-user-avatar :path="$author?->profile_picture" :alt="$author?->name ?? 'User'" :size="32" class="w-8 h-8" />
      <div class="leading-tight">
        <div class="text-sm font-semibold group-hover:underline">
          {{ $author?->name ?? 'Deleted user' }}
        </div>
        <div class="text-xs text-gray-500 dark:text-stone-400">
          {{ optional($post->published_at ?? $post->created_at)?->format('M j, Y') }}
        </div>
      </div>
    </a>

    {{-- Actions (hidden on preview) --}}
    @if($showActions)
      <div class="order-3 w-full sm:order-none sm:w-auto">
        <div class="flex flex-wrap items-center gap-2">
          @auth
            <form method="POST" action="{{ route('posts.like', $post) }}">
              @csrf
              <button type="submit"
                      aria-pressed="{{ $liked ? 'true' : 'false' }}"
                      title="{{ $liked ? 'Unlike' : 'Like' }}"
                      class="{{ $btn }}">
                {{ $post->likes()->count() }}
                <span>{{ $liked ? 'Unlike' : 'Like' }}</span>
              </button>
            </form>
          @else
            <a href="{{ route('login') }}" class="{{ $btn }}">
              {{ $post->likes()->count() }}
              <span>Log in to like</span>
            </a>
          @endauth

          @can('update', $post)
            <a href="{{ route('posts.edit', $post) }}" class="{{ $btn }}">Edit</a>
            <form action="{{ route('posts.destroy', $post) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this post?');">
              @csrf @method('DELETE')
              <button type="submit" class="{{ $btn }}">Delete</button>
            </form>
          @endcan
        </div>
      </div>
    @endif

    {{-- Board pill --}}
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