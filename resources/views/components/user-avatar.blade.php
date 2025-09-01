{{-- resources/views/components/user-avatar.blade.php --}}
@props(['path' => null, 'alt' => '', 'size' => 80, 'class' => '', 'raw' => false])

@if ($raw || !$path)
  <img src="{{ $path ? Storage::url($path) : asset('images/default-avatar.png') }}"
       alt="{{ $alt }}" width="{{ $size }}" height="{{ $size }}"
       class="rounded-full {{ $class }}">
@else
  <x-img :path="$path" :alt="$alt" :widths="[80,160,320]"
         sizes="{{ $size }}px" width="{{ $size }}" height="{{ $size }}"
         class="rounded-full {{ $class }}" />
@endif