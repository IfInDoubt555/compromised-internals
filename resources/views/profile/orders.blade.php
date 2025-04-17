@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-8">
    <h1 class="text-2xl font-bold mb-6 text-center">My Orders</h1>

    @if ($orders->count())
        @foreach ($orders as $order)
            <div x-data="{ open: false }" class="mb-4 border rounded-lg shadow bg-white">
                <!-- Order Summary -->
                <button 
                    @click="open = !open" 
                    class="w-full flex justify-between items-center p-4 text-left focus:outline-none">
                    <div>
                        <p class="font-semibold">Order #{{ $order->id }}</p>
                        <p class="text-gray-500 text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div :class="{'transform rotate-180': open}" class="transition-transform duration-300">
                        <!-- Dropdown arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <!-- Order Details -->
                <div x-show="open" x-collapse class="p-4 border-t">
                    <p><span class="font-semibold">Status:</span> {{ ucfirst($order->status) }}</p>
                    <p><span class="font-semibold">Total Amount:</span> ${{ number_format($order->total_amount, 2) }}</p>

                    @if ($order->tracking_number)
                        <p><span class="font-semibold">Tracking Number:</span> {{ $order->tracking_number }}</p>
                    @endif

                    <div class="mt-4">
                        <p class="font-semibold mb-2">Items:</p>
                        <ul class="space-y-2">
                            @foreach ($order->items as $item)
                                <li class="flex justify-between">
                                    <span>{{ $item->product_name }} (x{{ $item->quantity }})</span>
                                    <span>${{ number_format($item->price, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-center text-gray-500">You have no orders yet.</p>
    @endif
</div>
@endsection
