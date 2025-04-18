@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
    <div class="bg-white shadow-sm sm:rounded-lg p-4 max-w-xl mx-auto">
        <div class="flex items-center gap-4 justify-center">
            <x-user-avatar size="w-14 h-14" />
            <h2 class="text-xl font-semibold">Welcome back, {{ Auth::user()->name }}!</h2>
        </div>
    </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Quick Stats -->


            <!-- Quick Actions -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-xl font-semibold mb-2">Quick Actions</h3>
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('posts.create') }}" class="text-blue-500 hover:underline">New Post</a>
                    <a href="{{ route('profile.edit') }}" class="text-blue-500 hover:underline">Edit Profile</a>
                    <a href="{{ route('shop.index') }}" class="text-blue-500 hover:underline">Visit Shop</a>
                </div>
            </div>
            @if ($orders->count())
    <div x-data="{ openOrders: false }" class="bg-white p-6 rounded-lg shadow mb-6">
        <button 
            @click="openOrders = !openOrders" 
            class="w-full flex justify-between items-center text-lg font-semibold text-left">
            Orders
            <svg :class="{ 'rotate-180': openOrders }" class="h-6 w-6 transform transition-transform duration-300" 
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="openOrders" x-collapse class="mt-4">
            @foreach ($orders as $order)
                <div class="border rounded p-4 mb-2">
                    <div class="flex justify-between">
                        <span>Order #{{ $order->id }}</span>
                        <span class="text-gray-500 text-sm">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="mt-2 text-sm">
                        <p><span class="font-semibold">Status:</span> {{ ucfirst($order->status) }}</p>
                        {{-- Show Tracking Number if Available --}}
                        @if ($order->tracking_number)
                            <div class="mt-2 text-sm">
                                <strong>Tracking Number:</strong> {{ $order->tracking_number }}
                            </div>
                        @endif

                        {{-- Show Shipping Info if Available --}}
                        @if ($order->shipping_address)
                            <div class="mt-2 text-sm">
                                <strong>Shipping Address:</strong><br>
                                {{ $order->shipping_name }}<br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                {{ $order->shipping_country }}
                            </div>
                        @endif
                        <p><span class="font-semibold">Total:</span> ${{ number_format($order->total_amount, 2) }}</p>
                        @if ($order->tracking_number)
                            <p><span class="font-semibold">Tracking:</span> {{ $order->tracking_number }}</p>
                        @endif
                        <div class="mt-2">
                            <p class="font-semibold">Items:</p>
                            <ul class="list-disc ml-6">
                                @foreach ($order->items as $item)
                                    <li>
                                        {{ $item->product_name }} (x{{ $item->quantity }})
                                        @if ($item->size || $item->color)
                                            <br>
                                            <span class="text-sm text-gray-600">
                                                @if ($item->size)
                                                    Size: {{ $item->size }}
                                                @endif
                                                @if ($item->color)
                                                    | Color: {{ ucfirst($item->color) }}
                                                @endif
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
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <p class="text-center text-gray-500">You have no orders yet.</p>
    </div>
@endif

        </div>

        <!-- Recent Posts -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Your Latest Posts</h3>
                @if($posts->count())
                    <ul class="list-disc list-inside">
                    @foreach ($user->posts as $post)
                    <li>
                        <a href="{{ route('posts.show', $post->slug) }}" class="text-blue-600 hover:underline">
                            {{ $post->title }}
                        </a>
                        <a href="{{ route('posts.edit', $post) }}" class="text-sm text-gray-500 hover:text-blue-500     ml-2">Edit</        a>
                    </li>
                @endforeach
                </ul>
            @else
                <p class="text-gray-600">You have no posts yet.</p>
            @endif
        </div>

    </div>
</div>
@endsection
