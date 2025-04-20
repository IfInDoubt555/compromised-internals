<?php
// resources/views/components/cart-badge.blade.php
?>

@php
    $cartCount = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0;
@endphp

@if ($cartCount > 0)
    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1 py-0.5 text-xs font-bold text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
        {{ $cartCount }}
    </span>
@endif
