{{-- resources/views/partials/blog-post-card.blade.php --}}
@props([
  'post',
  // 'default'  = index card (220px thumb)
  // 'compact'  = small list (160px thumb)
  // 'featured' = hero-sized card (fills container)
  // 'list'     = homepage list-row (no inner card)
  'variant' => 'default',
])

@php
  // Safe defaults so includes never explode
  $variant     = $variant ?? 'default';
  $isFeatured  = ($variant === 'featured');

  // Wider banner for featured, boxier for list/default
  $thumbAspect = match ($variant) {
      'featured' => 'aspect-[2/1] xl:aspect-[21/9]', // wider
      'compact'  => 'aspect-[16/10]',                // small list
      default    => 'aspect-[16/10]',
  };

  // Shared image classes
  $imgClass = 'w-full h-full object-cover';
@endphp

<article {{ $attributes->class([
  'rounded-2xl overflow-hidden ring-1 ring-black/5 shadow dark:ring-white/10 bg-white/90 dark:bg-stone-900/70'
]) }}>
  {{-- Thumb --}}
  <div class="{{ $thumbAspect }}">
    <img
      src="{{ $post->thumbnail_url ?? asset('images/default-post.png') }}"
      alt="{{ $post->title }}"
      class="{{ $imgClass }}"
      loading="lazy"
    >
  </div>

  {{-- Text --}}
  <div class="p-4 sm:p-6">
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
          <path d="M3 12h15v1.5H3z"/>
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
</article>