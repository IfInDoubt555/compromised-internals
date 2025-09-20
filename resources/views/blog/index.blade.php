@extends('layouts.app')

@php
    $seo = [
        'title'       => 'Rally Blog | News, Articles & Event Coverage – Compromised Internals',
        'description' => 'Explore the Compromised Internals Rally Blog: news, site updates, and community posts on travel, WRC live threads, sim racing, photography, and more – all in one hub.',
        'url'         => url()->current(),
        'image'       => asset('images/default-post.png'),
    ];

    $ld = [
        '@context' => 'https://schema.org',
        '@type'    => 'CollectionPage',
        'url'      => $seo['url'],
        'name'     => 'Rally Blog',
        'description' => $seo['description'],
        'isPartOf' => [
            '@type' => 'WebSite',
            'name'  => 'Compromised Internals',
            'url'   => url('/'),
        ],
    ];

    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $posts */
    $items    = $posts instanceof \Illuminate\Contracts\Pagination\Paginator ? $posts->getCollection() : collect($posts);
    $featured = $items->first();
    $rest     = $items->slice(1);

    // ---- Preload the featured image (local images only) ----
    $preloadAvif = $preloadWebp = $preloadOrig = '';
    $preloadSizes = '(min-width:1280px) 960px, (min-width:1024px) 896px, 100vw';

    if ($featured) {
        $hero = $featured->thumbnail_url ?? asset('images/default-post.png');
        $pathOnly = parse_url($hero, PHP_URL_PATH) ?? $hero;
        $isLocalPath = \Illuminate\Support\Str::startsWith($pathOnly, ['/storage', 'storage/']);

        if ($isLocalPath) {
            $ext  = strtolower(pathinfo($pathOnly, PATHINFO_EXTENSION));
            $base = $ext ? substr($hero, 0, - (strlen($ext) + 1)) : $hero;

            $widths = [720, 960, 1280];
            $srcsetOrig = implode(', ', array_map(fn($w) => "{$base}-{$w}.{$ext} {$w}w", $widths));
            $srcsetWebp = implode(', ', array_map(fn($w) => "{$base}-{$w}.webp {$w}w", $widths));
            $srcsetAvif = implode(', ', array_map(fn($w) => "{$base}-{$w}.avif {$w}w", $widths));

            $preloadAvif = $srcsetAvif;
            $preloadWebp = $srcsetWebp;
            $preloadOrig = $srcsetOrig;
        } else {
            $preloadOrig = $hero;
        }
    }
@endphp

@push('head')
    <link rel="canonical" href="{{ $seo['url'] }}">
    <meta name="robots" content="index,follow">
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Compromised Internals">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['url'] }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">
    <script type="application/ld+json" nonce="@cspNonce">
        @json($ld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
    </script>

    @if($featured)
        @if($preloadAvif)
            <link rel="preload" as="image" type="image/avif" imagesrcset="{{ $preloadAvif }}" imagesizes="{{ $preloadSizes }}">
            <link rel="preload" as="image" type="image/webp" imagesrcset="{{ $preloadWebp }}" imagesizes="{{ $preloadSizes }}">
            <link rel="preload" as="image" imagesrcset="{{ $preloadOrig }}" imagesizes="{{ $preloadSizes }}">
        @else
            <link rel="preload" as="image" href="{{ $preloadOrig }}">
        @endif
    @endif
@endpush

@section('content')
<div class="mx-auto px-4 sm:px-6 lg:px-8 pt-[calc(var(--nav-h)+10px)] pb-8 lg:pt-10 max-w-[88rem]">

  <header class="mb-8 text-center">
    <h1 class="ci-title-xl">Rally Blog</h1>
    <p class="mt-2 text-sm ci-muted">News, features, and notes from the stages.</p>
  </header>

  @auth
    <a href="{{ route('posts.create') }}"
       class="fixed bottom-6 right-6 inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-600 text-white shadow-lg ring-1 ring-stone-900/10 dark:ring-white/10 transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
       title="New Post" aria-label="Create new post">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 5v14m7-7H5" />
      </svg>
    </a>
  @endauth

  {{-- MOBILE tools --}}
  <div class="lg:hidden sticky top-[calc(var(--nav-h)+8px)] z-30">
    <div class="ci-card px-4 py-3 ring-1 ring-black/5 dark:ring-white/10 shadow-sm
                backdrop-blur supports-[backdrop-filter]:bg-white/70 dark:supports-[backdrop-filter]:bg-stone-900/60">
      @include('partials.blog-sidebar')
    </div>
  </div>
  <div class="lg:hidden mt-4">
    @include('partials.blog-hot-right-now', ['items' => $hotPosts ?? [], 'limit' => 3])
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[minmax(300px,360px)_1fr] gap-10 items-start mt-6">
    {{-- Sidebar (desktop) --}}
    <aside class="hidden lg:block sticky top-[calc(var(--nav-h)+24px)] self-start">
      <div class="max-h-[calc(100vh-(var(--nav-h)+24px))] overflow-y-auto pr-2">
        @include('partials.blog-sidebar')
        <div class="mt-4">
          @include('partials.blog-hot-right-now', ['items' => $hotPosts ?? [], 'limit' => 3])
        </div>
      </div>
    </aside>

    {{-- Main --}}
    <main class="min-w-0">
      @if($items->count())

        {{-- Featured first post --}}
        @if($featured)
          <section aria-label="Featured post" class="mb-8">
            @include('partials.blog-post-card', ['post' => $featured, 'variant' => 'featured'])
          </section>
        @endif

        {{-- Rest: stacked on mobile, masonry (columns) on desktop --}}
        @if($rest->count())
          {{-- Mobile/Tablet stacked list --}}
          <ul class="space-y-5 lg:hidden">
            @foreach($rest as $post)
              <li>@include('partials.blog-post-card', ['post' => $post])</li>
            @endforeach
          </ul>

          {{-- Desktop masonry using CSS columns (no row gaps/holes) --}}
          <ul class="hidden lg:block columns-2 2xl:columns-2 gap-8 [column-fill:_balance]">
            @foreach($rest as $post)
              <li class="mb-8 break-inside-avoid">
                @include('partials.blog-post-card', ['post' => $post])
              </li>
            @endforeach
          </ul>
        @endif

      @else
        <p class="ci-body">No posts yet.</p>
      @endif

      <div class="mt-10 flex justify-center">
        <div class="ci-card px-3 py-2">
          {{ $posts->links() }}
        </div>
      </div>
    </main>
  </div>
</div>
@endsection