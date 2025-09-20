@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <h1 class="ci-title-lg">Scheduled Content</h1>
    <a class="ci-btn-sky" href="{{ route('admin.publish.index') }}">Back to Queue</a>
  </div>

  {{-- POSTS --}}
  <h2 class="ci-title-md mb-2">Posts</h2>
  <div class="ci-table-wrap mb-10">
    <table class="ci-table">
      <thead class="ci-thead">
        <tr>
          <th class="ci-th">Title</th>
          <th class="ci-th">Board</th>
          <th class="ci-th">Publish At</th>
          <th class="ci-th w-48">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($posts as $p)
          <tr class="ci-tr">
            <td class="ci-td">
              <a class="ci-link" href="{{ route('admin.publish.preview', $p) }}">{{ $p->title }}</a>
            </td>
            <td class="ci-td">{{ $p->board->name ?? '—' }}</td>
            <td class="ci-td">
              {{ optional($p->published_at)->timezone(config('app.timezone'))->format('M d, Y H:i') }}
            </td>
            <td class="ci-td">
              <a class="ci-cta" href="{{ route('admin.posts.edit', $p) }}">Edit</a>
            </td>
          </tr>
        @empty
          <tr><td class="ci-td" colspan="4">No scheduled posts</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- THREADS --}}
  <h2 class="ci-title-md mb-2">Threads</h2>
  <div class="ci-table-wrap">
    <table class="ci-table">
      <thead class="ci-thead">
        <tr>
          <th class="ci-th">Title</th>
          <th class="ci-th">Board</th>
          <th class="ci-th">Publish At</th>
          <th class="ci-th w-48">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($threads as $t)
          <tr class="ci-tr">
            <td class="ci-td">{{ $t->title }}</td>
            <td class="ci-td">{{ $t->board->name ?? '—' }}</td>
            <td class="ci-td">
              {{ optional($t->published_at)->timezone(config('app.timezone'))->format('M d, Y H:i') }}
            </td>
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
</div>
@endsection