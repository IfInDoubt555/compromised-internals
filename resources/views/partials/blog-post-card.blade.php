{{-- resources/views/partials/blog-post-card.blade.php --}}
@props([
  'post',
  // 'default'  = index card
  // 'compact'  = small list
  // 'featured' = hero-sized card
  'variant' => 'default',
])

@php
  /** @var \App\Models\Post $post */
  $variant    = $variant ?? 'default';
  $isFeatured = ($variant === 'featured');

  // Fixed container heights keep the grid tidy; image switches between cover/contain.
  $imageBox = match ($variant) {
    'featured' => 'h-[360px] md:h-[420px] xl:h-[480px]',
    'compact'  => 'h-[180px] md:h-[220px]',
    default    => 'h-[260px] md:h-[320px]',
  };
@endphp

<article {{ $attributes->class([
  'rounded-2xl overflow-hidden ring-1 ring-black/5 shadow dark:ring-white/10 bg-white/90 dark:bg-stone-900/70'
]) }}>
  {{-- Thumb --}}
  <div
    class="relative w-full {{ $imageBox }} overflow-hidden rounded-t-2xl ring-1 ring-black/5 dark:ring-white/10"
    x-data="{ portrait: false }"
    x-init="
      (() => {
        const i = $refs.cardImg;
        const set = () => portrait = i.naturalHeight > i.naturalWidth;
        if (i.complete) set();
        i.addEventListener('load', set, { once: true });
      })()
    "
  >
    <img
      x-ref="cardImg"
      src="{{ $post->thumbnail_url ?? asset('images/default-post.png') }}"
      alt="{{ $post->title }}"
      class="absolute inset-0 w-full h-full transition-transform duration-300"
      :class="portrait ? 'object-contain p-2' : 'object-cover'"
      loading="lazy" decoding="async"
    />
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