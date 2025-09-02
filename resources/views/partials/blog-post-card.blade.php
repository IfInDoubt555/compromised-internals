{{-- resources/views/partials/blog-post-card.blade.php --}}
@props([
  'post',
  // 'default' = index card (220px thumb)
  // 'compact' = small list (160px thumb)
  // 'featured' = hero-sized card (fills container)
  // 'list' = homepage list-row (no inner card)
  'variant' => 'default',
])

@php
  $isFeatured = $variant === 'featured';
  $isCompact  = $variant === 'compact';
  $isList     = $variant === 'list';

  $thumbCol   = ($isCompact || $isList) ? 'sm:grid-cols-[160px_1fr]' : 'sm:grid-cols-[220px_1fr]';
  $imgClass   = 'h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]';
  $articleCls = $isList
      ? 'group px-4 sm:px-5 py-4 hover:bg-white/60 dark:hover:bg-stone-800/50 transition'
      : 'group ci-card p-4 sm:p-5 transition '.($isFeatured ? 'hover:shadow-xl' : 'hover:shadow-lg');
 @endphp

<article class="{{ $articleCls }}">
  <div class="grid {{ $thumbCol }} gap-5 items-start">
    {{-- Thumbnail --}}
    <a href="{{ route('posts.show', $post->slug) }}"
       class="block overflow-hidden rounded-xl {{ $isList ? '' : 'ring-1 ring-stone-900/5 dark:ring-white/10' }}">      <div class="aspect-[16/10]">
        <img
          src="{{ $post->thumbnail_url }}"
          alt="{{ $post->title }}"
          loading="lazy"
          class="{{ $imgClass }}" />
      </div>
    </a>

    {{-- Text --}}
    <div>
      @php($author = $post->user)

      <div class="flex items-center gap-3 text-xs ci-muted">
        <a href="{{ $author ? route('profile.public', $author->id) : '#' }}" class="shrink-0">
          <x-user-avatar :path="$author?->profile_picture" :alt="$author?->name ?? 'User'" :size="32" class="w-8 h-8"/>
        </a>
        <span class="font-medium ci-body">{{ $author?->name ?? 'Deleted user' }}</span>
        <span aria-hidden="true">•</span>
            <time datetime="{{ optional($post->published_at ?? $post->created_at)?->toDateString() }}">
          {{ optional($post->published_at ?? $post->created_at)?->format('M j, Y') }}
          </time>
      </div>

      <h2 class="mt-2 {{ $isFeatured ? 'ci-title-xl' : 'ci-title-lg' }} leading-snug">
        <a href="{{ route('posts.show', $post->slug) }}" class="underline-offset-4 hover:underline">
          {{ $post->title }}
        </a>
      </h2>

      <p class="mt-2 ci-body {{ $variant === 'compact' ? 'line-clamp-2' : 'line-clamp-3' }}">
        {{ $post->excerpt_for_display }}
      </p>

      <div class="mt-4 flex items-center justify-between">
        <a href="{{ route('posts.show', $post->slug) }}"
           class="ci-cta inline-flex items-center gap-2 text-sm font-semibold">
          Read article
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M13.5 4.5 21 12l-7.5 7.5-1.06-1.06L18.88 12l-6.44-6.44 1.06-1.06Z"/>
            <path d="M3 12h15v1.5H3z" />
          </svg>
        </a>

        @can('update', $post)
          <div class="flex items-center gap-4 text-xs sm:text-sm">
            <a href="{{ route('posts.edit', $post) }}" class="font-semibold text-emerald-700 dark:text-emerald-300 hover:underline">Edit</a>
            <form action="{{ route('posts.destroy', $post) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this post?');">
              @csrf @method('DELETE')
              <button type="submit" class="font-semibold text-red-600 dark:text-red-400 hover:underline">Delete</button>
            </form>
          </div>
        @endcan
      </div>
    </div>
  </div>
</article>