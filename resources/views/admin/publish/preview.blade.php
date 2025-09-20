@extends('layouts.app')

@push('head')
  {{-- prevent indexing of preview --}}
  <meta name="robots" content="noindex,nofollow">
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 mt-6" x-data="{ tab: 'article' }">
  <div class="mb-4 flex items-center gap-2">
    <button class="ci-btn-secondary" :class="{ 'ci-btn-primary': tab==='article' }" @click="tab='article'">Article</button>
    <button class="ci-btn-secondary" :class="{ 'ci-btn-primary': tab==='card' }"    @click="tab='card'">List Card</button>
    <button class="ci-btn-secondary" :class="{ 'ci-btn-primary': tab==='featured' }" @click="tab='featured'">Featured Card</button>

    <div class="ml-auto flex items-center gap-2">
      <a href="{{ route('admin.posts.edit', $post) }}" class="ci-btn-secondary">Edit</a>

      @can('update', $post)
        <form method="POST" action="{{ route('admin.publish.now', ['post' => $post]) }}">
          @csrf
          <button class="ci-btn-primary">Publish now</button>
        </form>
      @endcan
    </div>
  </div>

  {{-- Article preview (identical to public) --}}
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