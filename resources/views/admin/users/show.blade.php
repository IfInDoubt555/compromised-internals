@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Posts by {{ $user->name }}</h1>

  @if ($posts->count())
    <div class="grid gap-6">
      @foreach ($posts as $post)
        <article class="ci-admin-card hover:shadow-md transition">
          <h2 class="text-xl font-semibold">
            <a href="{{ route('blog.show', $post->slug) }}" class="ci-link">
              {{ $post->title }}
            </a>
          </h2>
          <p class="text-sm ci-muted">
            Posted on {{ $post->created_at->format('F j, Y') }}
          </p>
          <p class="mt-2">
            {{ Str::limit(strip_tags($post->excerpt ?? $post->body), 160) }}
          </p>
        </article>
      @endforeach
    </div>

    @if ($orders->count())
      <h2 class="text-xl font-bold mt-10 mb-4">🛒 Orders</h2>
      <ul class="space-y-4">
        @foreach ($orders as $order)
          <li class="ci-admin-card">
            <p><strong>Order ID:</strong> {{ $order->id }}</p>
            <p><strong>Total:</strong> ${{ $order->total_amount }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p class="text-sm ci-muted">
              <strong>Placed:</strong> {{ $order->created_at->format('F j, Y') }}
            </p>
          </li>
        @endforeach
      </ul>
    @else
      <p class="ci-muted mt-10">No orders found for this user.</p>
    @endif

    <div class="mt-6">
      {{ $posts->links() }}
    </div>
  @else
    <p class="ci-muted">This user hasn't posted anything yet.</p>
  @endif

  <div class="mt-6">
    <a href="{{ route('admin.users.index') }}" class="ci-link">&larr; Back to User List</a>
  </div>
</div>
@endsection