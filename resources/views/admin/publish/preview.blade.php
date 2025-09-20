@extends('layouts.app')

@push('head')
  {{-- prevent indexing of preview --}}
  <meta name="robots" content="noindex,nofollow">
@endpush

@section('content')
@php
  /** @var \App\Models\Post $post */
  $isPublished = $post->isPublished();
  $isScheduled = $post->isScheduled();
@endphp

<div
  class="max-w-6xl mx-auto px-4 mt-6"
  x-data="{ tab: (localStorage.getItem('postPreviewTab') || 'article') }"
  x-init="$watch('tab', t => localStorage.setItem('postPreviewTab', t))"
>
  {{-- Top bar --}}
  <div class="mb-4 flex flex-wrap items-center gap-2">
    <div class="flex gap-2">
      <button class="ci-btn-secondary" :class="{ 'ci-btn-primary': tab==='article' }" @click="tab='article'">Article</button>
      <button class="ci-btn-secondary" :class="{ 'ci-btn-primary': tab==='card' }"    @click="tab='card'">List Card</button>
      <button class="ci-btn-secondary" :class="{ 'ci-btn-primary': tab==='featured' }" @click="tab='featured'">Featured Card</button>
    </div>

    <div class="ml-auto flex flex-wrap items-center gap-2">
      {{-- Status pill --}}
      @if(!$isPublished)
        <span class="px-2 py-1 text-xs font-semibold rounded-md bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
          {{ $isScheduled ? 'Scheduled' : 'Draft' }}
          @if($isScheduled && $post->published_at)
            · {{ $post->published_at->timezone(config('app.timezone'))->format('M j, Y H:i') }}
          @endif
        </span>
      @else
        <span class="px-2 py-1 text-xs font-semibold rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
          Published
        </span>
      @endif>

      {{-- Edit --}}
      <a href="{{ route('admin.posts.edit', $post) }}" class="ci-btn-secondary">Edit</a>

      {{-- View public (only when published) --}}
      @if($isPublished)
        <a href="{{ route('blog.show', $post->slug) }}" class="ci-btn-secondary" target="_blank" rel="noopener">View public</a>
      @endif

      {{-- Publish now (only if authorized + not already published) --}}
      @can('update', $post)
        @unless($isPublished)
          <form method="POST" action="{{ route('admin.publish.now', ['post' => $post]) }}">
            @csrf
            <button class="ci-btn-primary">Publish now</button>
          </form>
        @endunless
      @endcan
    </div>
  </div>

  {{-- Article preview (identical to public; uses body_html + intrinsic hero in the partial) --}}
  <div x-show="tab==='article'" x-cloak>
    @include('posts.partials.article-preview', [
      'post'        => $post,
      'isPreview'   => true,
      'showActions' => false,
    ])
  </div>

  {{-- List-card preview --}}
  <div x-show="tab==='card'" x-cloak class="max-w-5xl mx-auto">
    @include('partials.blog-post-card', ['post' => $post, 'variant' => 'compact'])
  </div>

  {{-- Featured-card preview --}}
  <div x-show="tab==='featured'" x-cloak class="max-w-5xl mx-auto">
    @include('partials.blog-post-card', ['post' => $post, 'variant' => 'featured'])
  </div>
</div>
@endsection