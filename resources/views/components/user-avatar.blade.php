@props(['path' => null, 'alt' => '', 'size' => 32, 'class' => ''])

@if ($path)
  <x-img
      :path="$path"
      :alt="$alt"
      :widths="[160,320]"         {{-- ← stop asking for 80px which doesn’t exist --}}
      sizes="{{ (int) $size }}px"
      width="{{ (int) $size }}"
      height="{{ (int) $size }}"
      class="rounded-full {{ $class }}" />
@else
  <img src="{{ asset('images/default-avatar.png') }}"
       alt="{{ $alt }}"
       width="{{ (int) $size }}" height="{{ (int) $size }}"
       class="rounded-full {{ $class }}">
@endif