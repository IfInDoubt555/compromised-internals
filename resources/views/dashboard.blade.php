@extends('layouts.app')

@section('content')
<div class="py-12">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

    {{-- Welcome card --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-4 max-w-xl mx-auto ring-1 ring-black/5
                dark:bg-stone-900/70 dark:ring-white/10">
      <div class="flex items-center gap-4 justify-center">
        <x-user-avatar :path="$user->profile_picture" alt="{{ $user->name }}" :size="8" />
        <h2 class="text-xl font-semibold text-gray-900 dark:text-stone-100">
          Welcome back, {{ Auth::user()->name }}!
        </h2>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      {{-- Quick Actions --}}
      <div class="bg-white shadow-sm sm:rounded-lg p-6 ring-1 ring-black/5
                  dark:bg-stone-900/70 dark:ring-white/10">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-stone-100 mb-2">Quick Actions</h3>
        <div class="flex flex-col space-y-2">
          <a href="{{ route('posts.create') }}" class="text-blue-500 dark:text-sky-300 hover:underline">New Post</a>

          {{-- Thread creation needs a board slug; send user to boards to pick one --}}
          <a href="{{ route('boards.index') }}" class="text-blue-500 dark:text-sky-300 hover:underline">New Thread</a>

          <a href="{{ route('profile.edit') }}" class="text-blue-500 dark:text-sky-300 hover:underline">Edit Profile</a>
          <a href="{{ route('shop.index') }}" class="text-blue-500 dark:text-sky-300 hover:underline">Visit Shop</a>
        </div>
      </div>

      {{-- Orders --}}
      @if ($orders->count())
        <div x-data="{ openOrders: false }"
             class="bg-white p-6 rounded-lg shadow mb-6 ring-1 ring-black/5
                    dark:bg-stone-900/70 dark:ring-white/10">
          <button
            @click="openOrders = !openOrders"
            class="w-full flex justify-between items-center text-lg font-semibold text-left
                   text-gray-900 dark:text-stone-100">
            Orders
            <svg :class="{ 'rotate-180': openOrders }"
                 class="h-6 w-6 transform transition-transform duration-300"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div x-show="openOrders" x-collapse class="mt-4">
            @foreach ($orders as $order)
              <div class="border rounded p-4 mb-2 border-gray-200 dark:border-white/10
                          bg-white/80 dark:bg-stone-900/50">
                <div class="flex justify-between">
                  <span class="text-gray-900 dark:text-stone-100">Order #{{ $order->id }}</span>
                  <span class="text-gray-500 dark:text-stone-400 text-sm">
                    {{ $order->created_at->format('M d, Y') }}
                  </span>
                </div>

                <div class="mt-2 text-sm text-gray-800 dark:text-stone-300">
                  <p><span class="font-semibold text-gray-900 dark:text-stone-100">Status:</span> {{ ucfirst($order->status) }}</p>

                  @if ($order->tracking_number)
                    <div class="mt-2">
                      <strong class="text-gray-900 dark:text-stone-100">Tracking Number:</strong>
                      {{ $order->tracking_number }}
                    </div>
                  @endif

                  @if ($order->shipping_address)
                    <div class="mt-2">
                      <strong class="text-gray-900 dark:text-stone-100">Shipping Address:</strong><br>
                      {{ $order->shipping_name }}<br>
                      {{ $order->shipping_address }}<br>
                      {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                      {{ $order->shipping_country }}
                    </div>
                  @endif

                  <p class="mt-2">
                    <span class="font-semibold text-gray-900 dark:text-stone-100">Total:</span>
                    ${{ number_format($order->total_amount, 2) }}
                  </p>

                  <div class="mt-2">
                    <p class="font-semibold text-gray-900 dark:text-stone-100">Items:</p>
                    <ul class="list-disc ml-6">
                      @foreach ($order->items as $item)
                        <li>
                          {{ $item->product_name }} (x{{ $item->quantity }})
                          @if ($item->size || $item->color)
                            <br>
                            <span class="text-sm text-gray-600 dark:text-stone-400">
                              @if ($item->size) Size: {{ $item->size }} @endif
                              @if ($item->color) | Color: {{ ucfirst($item->color) }} @endif
                            </span>
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="bg-white p-6 rounded-lg shadow mb-6 ring-1 ring-black/5
                    dark:bg-stone-900/70 dark:ring-white/10">
          <p class="text-center text-gray-500 dark:text-stone-400">You have no orders yet.</p>
        </div>
      @endif

    </div>

    {{-- Your Latest Posts --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 max-h-64 overflow-y-auto ring-1 ring-black/5
                dark:bg-stone-900/70 dark:ring-white/10">
      <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-stone-100">Your Latest Posts</h3>

      @if ($posts->count())
        <ul class="list-disc list-inside space-y-1">
          @foreach ($posts as $post)
            <li class="flex justify-between items-center">
              <div>
                <a href="{{ route('posts.show', $post) }}"
                   class="text-blue-600 dark:text-sky-300 hover:underline">
                  {{ $post->title }}
                </a>
                <a href="{{ route('posts.edit', $post) }}"
                   class="text-sm text-gray-500 dark:text-stone-400 hover:text-blue-500 dark:hover:text-sky-300 ml-2">
                  Edit
                </a>
              </div>
              <form action="{{ route('posts.destroy', $post) }}" method="POST"
                    onsubmit="return confirm('Delete this post?')" class="ml-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm text-red-500 hover:text-red-700 dark:text-rose-400 dark:hover:text-rose-300">
                  Delete
                </button>
              </form>
            </li>
          @endforeach
        </ul>
      @else
        <p class="text-gray-600 dark:text-stone-400">You have no posts yet.</p>
      @endif
    </div>

    {{-- Your Latest Threads --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6 max-h-64 overflow-y-auto ring-1 ring-black/5
                dark:bg-stone-900/70 dark:ring-white/10">
      <h3 class="text-xl font-semibold mb-4 text-gray-900 dark:text-stone-100">Your Latest Threads</h3>

      @if (isset($threads) && $threads->count())
        <ul class="list-disc list-inside space-y-1">
          @foreach ($threads as $thread)
            <li class="flex justify-between items-center">
              <div>
                <a href="{{ route('threads.show', $thread) }}"
                   class="text-blue-600 dark:text-sky-300 hover:underline">
                  {{ $thread->title }}
                </a>
                <a href="{{ route('threads.edit', $thread) }}"
                   class="text-sm text-gray-500 dark:text-stone-400 hover:text-blue-500 dark:hover:text-sky-300 ml-2">
                  Edit
                </a>
              </div>
              <form action="{{ route('threads.destroy', $thread) }}" method="POST"
                    onsubmit="return confirm('Delete this thread?')" class="ml-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="text-sm text-red-500 hover:text-red-700 dark:text-rose-400 dark:hover:text-rose-300">
                  Delete
                </button>
              </form>
            </li>
          @endforeach
        </ul>
      @else
        <p class="text-gray-600 dark:text-stone-400">You have no threads yet.</p>
      @endif
    </div>

  </div>
</div>
@endsection