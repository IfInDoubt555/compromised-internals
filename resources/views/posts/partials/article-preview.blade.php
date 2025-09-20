{{-- Shared renderer for a post article (public + admin preview) --}}
@php
  /** @var \App\Models\Post $post */
  $isPreview   = $isPreview   ?? false;    // admin preview page?
  $showActions = $showActions ?? ! $isPreview;

  $author = $post->user;

  // Safe defaults so the view never explodes
  $liked = $liked
    ?? (auth()->check() && $post->likes()->where('user_id', auth()->id())->exists());

  $btn = $btn
    ?? 'inline-flex items-center gap-2 rounded-lg px-3 py-2 ring-1 ring-black/5 dark:ring-white/10
        bg-white/80 dark:bg-stone-800/60 text-sm font-semibold text-stone-800 dark:text-stone-100
        hover:bg-white hover:shadow';

  // --- Responsive hero image variants (local storage only) ---
  $hero = $post->image_url;
  $isLocalPath = \Illuminate\Support\Str::startsWith(
      parse_url($hero, PHP_URL_PATH) ?? $hero,
      ['/storage', 'storage/']
  );

  $pathOnly = parse_url($hero, PHP_URL_PATH) ?? $hero;
  $ext      = strtolower(pathinfo($pathOnly, PATHINFO_EXTENSION));
  $base     = $ext ? substr($hero, 0, - (strlen($ext) + 1)) : $hero;

  // Wider set for hero banners
  $heroWidths = [640, 960, 1280, 1600, 1920];
  // Container is max-w-5xl (≈1024px). Use 100vw on small screens.
  $heroSizes  = '(min-width:1280px) 1024px, (min-width:1024px) 1024px, 100vw';

  $srcsetOrig = $isLocalPath
      ? implode(', ', array_map(fn($w) => "{$base}-{$w}.{$ext} {$w}w", $heroWidths))
      : '';
  $srcsetWebp = $isLocalPath
      ? implode(', ', array_map(fn($w) => "{$base}-{$w}.webp {$w}w", $heroWidths))
      : '';
  $srcsetAvif = $isLocalPath
      ? implode(', ', array_map(fn($w) => "{$base}-{$w}.avif {$w}w", $heroWidths))
      : '';
@endphp

{{-- Feature image + title (orientation-aware) --}}
<div class="max-w-5xl mx-auto px-4">
  <figure
    class="rounded-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-xl"
    x-data="{ portrait: false }"
    x-init="
      (() => {
        const i = $refs.hero;
        const set = () => portrait = i.naturalHeight > i.naturalWidth;
        if (i.complete) set();
        i.addEventListener('load', set, { once: true });
      })()
    "
  >
    <picture class="block">
      @if($isLocalPath)
        <source type="image/avif" srcset="{{ $srcsetAvif }}" sizes="{{ $heroSizes }}">
        <source type="image/webp" srcset="{{ $srcsetWebp }}" sizes="{{ $heroSizes }}">
      @endif
      <img
        x-ref="hero"
        src="{{ $hero }}"
        @if($isLocalPath) srcset="{{ $srcsetOrig }}" sizes="{{ $heroSizes }}" @endif
        alt="{{ $post->title }}"
        loading="eager" fetchpriority="high" decoding="async"
        :class="portrait
          ? 'block w-full h-auto object-contain max-h-[80vh]'
          : 'block w-full h-auto object-cover aspect-[16/9] md:aspect-[2/1] xl:aspect-[21/9]'"
      />
    </picture>
  </figure>

  <h1 class="mt-6 text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-stone-100">
    {{ $post->title }}
    @if($isPreview && ($post->status ?? 'draft') !== 'published')
      <span class="ml-2 align-middle text-xs font-semibold px-2 py-1 rounded-md
                   bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
        {{ \Illuminate\Support\Str::title($post->status ?? 'draft') }} preview
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
           prose-img:rounded-xl prose-img:shadow-xl prose-img:mx-auto prose-img:max-h-[90vh] prose-img:cursor-zoom-in
           dark:prose-invert dark:text-stone-200 dark:bg-stone-900/70 dark:ring-white/10
           js-lightbox-scope">
    {!! $post->body_html !!}
  </article>
</div>