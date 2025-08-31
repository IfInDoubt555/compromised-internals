@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-10">
  <h1 class="text-2xl font-bold">Threads</h1>

  <section>
    <h2 class="font-semibold mb-2">Scheduled</h2>
    <div class="space-y-2">
      @forelse($scheduled as $t)
        <div class="ci-card p-3 flex items-center justify-between">
          <div>
            <div class="font-semibold">{{ $t->title }}</div>
            <div class="text-xs ci-muted">Board: {{ $t->board->name ?? '—' }} • Publishes {{ optional($t->scheduled_for)->setTimezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
          </div>
          <a class="btn-secondary" href="{{ route('admin.threads.edit', $t) }}">Edit</a>
        </div>
      @empty
        <p class="ci-muted text-sm">Nothing scheduled.</p>
      @endforelse
    </div>
  </section>

  <section>
    <h2 class="font-semibold mb-2">Drafts</h2>
    <div class="space-y-2">
      @foreach($drafts as $t)
        <div class="ci-card p-3 flex items-center justify-between">
          <div>
            <div class="font-semibold">{{ $t->title }}</div>
            <div class="text-xs ci-muted">Board: {{ $t->board->name ?? '—' }}</div>
          </div>
          <a class="btn-secondary" href="{{ route('admin.threads.edit', $t) }}">Edit</a>
        </div>
      @endforeach
    </div>
  </section>

  <section>
    <h2 class="font-semibold mb-2">Recently Published</h2>
    <div class="space-y-2">
      @foreach($published as $t)
        <div class="ci-card p-3 flex items-center justify-between">
          <div>
            <div class="font-semibold">{{ $t->title }}</div>
            <div class="text-xs ci-muted">Published {{ optional($t->published_at)->setTimezone(config('app.timezone'))->diffForHumans() }}</div>
          </div>
          <a class="btn-secondary" href="{{ route('admin.threads.edit', $t) }}">Edit</a>
        </div>
      @endforeach
    </div>
  </section>
</div>
@endsection