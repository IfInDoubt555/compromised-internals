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
  /** @var \App\Models\Post $post */
  $variant    = $variant ?? 'default';
  $isFeatured = ($variant === 'featured');

  // Wider banner for featured, boxier for others
  $thumbAspect = match ($variant) {
      'featured' => 'aspect-[2/1] xl:aspect-[21/9]',
      'compact'  => 'aspect-[16/10]',
      default    => 'aspect-[16/10]',
  };

  $imgClass = 'w-full h-full object-cover';
@endphp

<article {{ $attributes->class([
  'rounded-2xl overflow-hidden ring-1 ring-black/5 shadow dark:ring-white/10 bg-white/90 dark:bg-stone-900/70'
]) }}>
  {{-- Thumb --}}
  <div class="{{ $thumbAspect }}">
    @php
      $raw  = $post->thumbnail_url ?? asset('images/default-post.png');
      $host = parse_url($raw, PHP_URL_HOST);
      $sameHost = !$host || $host === request()->getHost();
      // Cloudflare Image Resizing (only for same-host URLs)
      $cf = function (int $w) use ($raw, $sameHost) {
        return $sameHost
          ? "/cdn-cgi/image/width={$w},quality=75,format=auto,fit=cover{$raw}"
          : $raw; // fallback: leave as-is for external hosts
      };
      // Intrinsic size (helps CLS)
      $intrinsicW = 1280; $intrinsicH = 800;
    @endphp

    <picture>
      <source
        srcset="{{ $cf(1600) }} 1600w, {{ $cf(1280) }} 1280w, {{ $cf(1024) }} 1024w, {{ $cf(768) }} 768w, {{ $cf(480) }} 480w"
        sizes="(min-width:1024px) 768px, (min-width:640px) 600px, 100vw">
      <img
        src="{{ $cf(1024) }}"
        alt="{{ $post->title }}"
        width="{{ $intrinsicW }}" height="{{ $intrinsicH }}"
        class="{{ $imgClass }}"
        loading="lazy" decoding="async" fetchpriority="low">
    </picture>
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

    {{-- (3) Clamp title in featured to keep carousel/card height consistent --}}
    <h2 class="mt-2 {{ $isFeatured ? 'ci-title-xl' : 'ci-title-lg' }} leading-snug {{ $variant === 'featured' ? 'line-clamp-2' : '' }}">
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
</article>