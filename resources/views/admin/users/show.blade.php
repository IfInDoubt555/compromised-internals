@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        Posts by {{ $user->name }}
    </h1>

    @if ($posts->count())
    <div class="grid gap-6">
        @foreach ($posts as $post)
        <div class="border rounded-lg p-4 shadow hover:shadow-md transition">
            <h2 class="text-xl font-semibold">
                <a href="{{ route('blog.show', $post->slug) }}" class="hover:underline text-blue-600">
                    {{ $post->title }}
                </a>
            </h2>
            <p class="text-sm text-gray-500">
                Posted on {{ $post->created_at->format('F j, Y') }}
            </p>
            <p class="mt-2 text-gray-700">{{ Str::limit(strip_tags($post->excerpt ?? $post->body), 120) }}</p>
        </div>
        @endforeach
    </div>
    @if ($orders->count())
    <h2 class="text-xl font-bold mt-10 mb-4">🛒 Orders</h2>
    <ul class="space-y-4">
        @foreach ($orders as $order)
        <li class="border p-4 rounded shadow-sm">
            <p><strong>Order ID:</strong> {{ $order->id }}</p>
            <p><strong>Total:</strong> ${{ $order->total_amount }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p class="text-sm text-gray-500"><strong>Placed:</strong> {{ $order->created_at->format('F j, Y') }}</p>
        </li>
        @endforeach
    </ul>
    @else
    <p class="text-gray-600 mt-10">No orders found for this user.</p>
    @endif

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
    @else
    <p class="text-gray-600">This user hasn't posted anything yet.</p>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">&larr; Back to User List</a>
    </div>
</div>
@endsection