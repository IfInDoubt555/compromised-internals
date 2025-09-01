@props(['path' => null, 'alt' => '', 'size' => 80, 'class' => ''])
@php
  // Support numeric pixel sizes only. If someone passes a Tailwind class by mistake,
  // fall back to 80px.
  $px = is_numeric($size) ? (int) $size : 80;
  // Pick sensible width set: small avatars get small variants, larger chips get bigger.
  $widths = $px <= 64 ? [80,160,320] : [160,320,640];
@endphp

@if ($path)
  <x-img
    :path="$path"
    :alt="$alt"
    :widths="$widths"
    sizes="{{ $px }}px"
    width="{{ $px }}"
    height="{{ $px }}"
    class="rounded-full {{ $class }}"
  />
@else
  <img
    src="{{ asset('images/default-avatar.png') }}"
    alt="{{ $alt }}"
    width="{{ $px }}" height="{{ $px }}"
    class="rounded-full {{ $class }}"
  >
@endif