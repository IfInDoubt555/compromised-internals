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
@endphp

@push('head')
    {{-- Canonical + robots --}}
    <link rel="canonical" href="{{ $seo['url'] }}">
    <meta name="robots" content="index,follow">

    {{-- Basic Meta --}}
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Compromised Internals">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="{{ $seo['url'] }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ $seo['image'] }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['url'] }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">

    {{-- Structured Data --}}
    <script type="application/ld+json">
        @json($ld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
    </script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

  {{-- Hero --}}
  <header class="mb-8 text-center">
    <h1 class="ci-title-xl">Rally Blog</h1>
    <p class="mt-2 text-sm ci-muted">News, features, and notes from the stages.</p>
  </header>

  @auth
  {{-- Floating New Post button --}}
  <a href="{{ route('posts.create') }}"
     class="fixed bottom-6 right-6 inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-600 text-white shadow-lg ring-1 ring-stone-900/10 dark:ring-white/10 transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
     title="New Post" aria-label="Create new post">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 5v14m7-7H5" />
      </svg>
  </a>
  @endauth

  {{-- Layout: sidebar + main --}}
  <div class="grid grid-cols-1 lg:grid-cols-[minmax(280px,340px)_1fr] gap-8">

    {{-- Sidebar --}}
    <aside class="lg:sticky lg:top-24">
      @include('partials.blog-sidebar')
    </aside>

    {{-- Main --}}
    <main>
      @if($posts->count())
        <ul class="space-y-5">
          @foreach($posts as $post)
            <li>
              <article class="group ci-card p-4 sm:p-5 transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="grid sm:grid-cols-[220px_1fr] gap-5 items-start">
                  {{-- Thumbnail --}}
                  <a href="{{ route('posts.show', $post->slug) }}"
                     class="block overflow-hidden rounded-xl ring-1 ring-stone-900/5 dark:ring-white/10">
                    <div class="aspect-[16/10]">
                      <img
                        src="{{ $post->thumbnail_url }}"
                        alt="{{ $post->title }}"
                        loading="lazy"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
                    </div>
                  </a>

                  {{-- Text --}}
                  <div>
                    @php($author = $post->user)

                    <div class="flex items-center gap-3 text-xs ci-muted">
                      <a href="{{ $author ? route('profile.public', $author->id) : '#' }}" class="shrink-0">
                        {{-- pass the stored path and a pixel size --}}
                        <x-user-avatar :path="$author?->profile_picture" :alt="$author?->name ?? 'User'" :size="32" class="w-8 h-8"/>
                      </a>
                      <span class="font-medium ci-body">{{ $author?->name ?? 'Deleted user' }}</span>
                      <span aria-hidden="true">•</span>
                      <time datetime="{{ $post->created_at->toDateString() }}">
                        {{ $post->created_at->format('M j, Y') }}
                      </time>
                    </div>

                    <h2 class="mt-2 ci-title-lg leading-snug">
                      <a href="{{ route('posts.show', $post->slug) }}" class="underline-offset-4 hover:underline">
                        {{ $post->title }}
                      </a>
                    </h2>

                    <p class="mt-2 ci-body line-clamp-3">
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
                          <a href="{{ route('posts.edit', $post) }}" class="font-semibold text-emerald-700 dark:text-emerald-300 hover:underline">✏️ Edit</a>
                          <form action="{{ route('posts.destroy', $post) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this post?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="font-semibold text-red-600 dark:text-red-400 hover:underline">🗑️ Delete</button>
                          </form>
                        </div>
                      @endcan
                    </div>
                  </div>
                </div>
              </article>
            </li>
          @endforeach
        </ul>
      @else
        <p class="ci-body">No posts yet.</p>
      @endif

      {{-- Pagination --}}
      <div class="mt-10 flex justify-center">
        <div class="ci-card px-3 py-2">
          {{ $posts->links() }}
        </div>
      </div>
    </main>
  </div>
</div>
@endsection