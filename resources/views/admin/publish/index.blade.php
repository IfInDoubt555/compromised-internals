@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ tab: $persist('posts') }">
  <div class="flex items-center justify-between mb-4">
    <h1 class="ci-title-lg">Content Queue</h1>
    <a class="ci-btn-sky" href="{{ route('admin.publish.create') }}">Create Content</a>
  </div>

  {{-- Tabs --}}
  <div class="mb-6 flex gap-2">
    <button class="pill pill-hover" :class="tab==='posts' ? 'pill-primary' : ''" @click="tab='posts'">Posts</button>
    <button class="pill pill-hover" :class="tab==='threads' ? 'pill-primary' : ''" @click="tab='threads'">Board Threads</button>
  </div>

  {{-- POSTS TAB --}}
  <section x-show="tab==='posts'" x-cloak>
    {{-- Drafts --}}
    <h2 class="ci-title-md mb-2">Drafts</h2>
    <div class="ci-table-wrap mb-8">
      <table class="ci-table">
        <thead class="ci-thead">
          <tr>
            <th class="ci-th">Title</th>
            <th class="ci-th">Updated</th>
            <th class="ci-th w-40">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($postDrafts as $p)
            <tr class="ci-tr">
              <td class="ci-td">
                <a class="ci-link" href="{{ route('blog.show', $p->slug) }}">{{ $p->title }}</a>
              </td>
              <td class="ci-td">{{ $p->updated_at?->diffForHumans() }}</td>
              <td class="ci-td">
                <a class="ci-cta" href="{{ route('admin.posts.edit', $p) }}">Edit</a>
              </td>
            </tr>
          @empty
            <tr><td class="ci-td" colspan="3">No drafts</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="ci-pagination">{{ $postDrafts->withQueryString()->links() }}</div>

    {{-- Scheduled --}}
    <h2 class="ci-title-md mt-8 mb-2">Scheduled</h2>
    <div class="ci-table-wrap mb-8">
      <table class="ci-table">
        <thead class="ci-thead">
          <tr>
            <th class="ci-th">Title</th>
            <th class="ci-th">Publish At</th>
            <th class="ci-th w-40">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($postScheduled as $p)
            <tr class="ci-tr">
              <td class="ci-td">{{ $p->title }}</td>
              <td class="ci-td">{{ optional($p->scheduled_for)->format('M d, Y H:i') }}</td>
              <td class="ci-td">
                <a class="ci-cta" href="{{ route('admin.posts.edit', $p) }}">Edit</a>
              </td>
            </tr>
          @empty
            <tr><td class="ci-td" colspan="3">No scheduled posts</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="ci-pagination">{{ $postScheduled->withQueryString()->links() }}</div>

    {{-- Recently Published --}}
    <h2 class="ci-title-md mt-8 mb-2">Recently Published</h2>
    <ul class="space-y-2">
      @foreach($postPublished as $p)
        <li>
          <a class="ci-link" href="{{ route('blog.show', $p->slug) }}">{{ $p->title }}</a>
          <span class="ci-muted">— {{ $p->published_at?->diffForHumans() }}</span>
        </li>
      @endforeach
    </ul>
  </section>

  {{-- THREADS TAB --}}
  <section x-show="tab==='threads'" x-cloak>
    {{-- Drafts --}}
    <h2 class="ci-title-md mb-2">Drafts</h2>
    <div class="ci-table-wrap mb-8">
      <table class="ci-table">
        <thead class="ci-thead">
          <tr>
            <th class="ci-th">Title</th>
            <th class="ci-th">Board</th>
            <th class="ci-th">Updated</th>
            <th class="ci-th w-40">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($threadDrafts as $t)
            <tr class="ci-tr">
              <td class="ci-td">{{ $t->title }}</td>
              <td class="ci-td">{{ $t->board->name ?? '—' }}</td>
              <td class="ci-td">{{ $t->updated_at?->diffForHumans() }}</td>
              <td class="ci-td">
                <a class="ci-cta" href="{{ route('admin.threads.edit', $t) }}">Edit</a>
              </td>
            </tr>
          @empty
            <tr><td class="ci-td" colspan="4">No drafts</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="ci-pagination">{{ $threadDrafts->withQueryString()->links() }}</div>

    {{-- Scheduled --}}
    <h2 class="ci-title-md mt-8 mb-2">Scheduled</h2>
    <div class="ci-table-wrap mb-8">
      <table class="ci-table">
        <thead class="ci-thead">
          <tr>
            <th class="ci-th">Title</th>
            <th class="ci-th">Board</th>
            <th class="ci-th">Publish At</th>
            <th class="ci-th w-40">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($threadScheduled as $t)
            <tr class="ci-tr">
              <td class="ci-td">{{ $t->title }}</td>
              <td class="ci-td">{{ $t->board->name ?? '—' }}</td>
              <td class="ci-td">{{ optional($t->scheduled_for)->format('M d, Y H:i') }}</td>
              <td class="ci-td">
                <a class="ci-cta" href="{{ route('admin.threads.edit', $t) }}">Edit</a>
              </td>
            </tr>
          @empty
            <tr><td class="ci-td" colspan="4">No scheduled threads</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="ci-pagination">{{ $threadScheduled->withQueryString()->links() }}</div>

    {{-- Recently Published --}}
    <h2 class="ci-title-md mt-8 mb-2">Recently Published</h2>
    <ul class="space-y-2">
      @foreach($threadPublished as $t)
        <li>
          <span>{{ $t->title }}</span>
          <span class="ci-muted">— {{ $t->published_at?->diffForHumans() }}</span>
        </li>
      @endforeach
    </ul>
  </section>
</div>
@endsection