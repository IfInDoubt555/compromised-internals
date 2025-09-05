{{-- props: user (App\Models\User), size (int), alt? class? --}}
@props([
  'user',
  'size' => 80,
  'alt' => '',
  'class' => '',
])

@php
  /** @var \App\Models\User $user */
  $path = $user->profile_picture ?? null;
  $fallback = asset('images/default-avatar.png');
@endphp

@if (empty($path))
  <img
    src="{{ $fallback }}"
    alt="{{ $alt }}"
    width="{{ (int) $size }}"
    height="{{ (int) $size }}"
    class="rounded-full {{ $class }}">
@else
  {{-- If you have an <x-img> responsive helper, use it. Otherwise use Storage::url --}}
  @if (class_exists(\Illuminate\Support\Facades\Storage::class))
    <img
      src="{{ \Illuminate\Support\Facades\Storage::url($path) }}"
      alt="{{ $alt }}"
      width="{{ (int) $size }}"
      height="{{ (int) $size }}"
      class="rounded-full {{ $class }}">
  @else
    <img
      src="{{ $fallback }}"
      alt="{{ $alt }}"
      width="{{ (int) $size }}"
      height="{{ (int) $size }}"
      class="rounded-full {{ $class }}">
  @endif
@endif