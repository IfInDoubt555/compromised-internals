@props(['path' => null, 'alt' => '', 'size' => 80, 'class' => ''])

@php
$user = $user ?? Auth::user();
@endphp

@if ($path)
  <x-img :path="$path"
         :alt="$alt"
         :widths="[80,160,320]"
         sizes="{{ $size }}px"
         width="{{ $size }}"
         height="{{ $size }}"
         class="rounded-full {{ $class }}" />
@else
  <img src="{{ asset('images/default-avatar.png') }}"
       alt="{{ $alt }}"
       width="{{ $size }}" height="{{ $size }}"
       class="rounded-full {{ $class }}">
@endif