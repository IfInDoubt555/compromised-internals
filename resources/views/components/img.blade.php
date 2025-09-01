@props([
  'path',
  'base' => 'storage',     // 'storage' (default) or 'public'
  'alt' => '',
  'sizes' => '(max-width: 768px) 100vw, 720px',
  'widths' => [160,320,640,960,1280],
  'class' => '',
  'loading' => 'lazy',
  'fetchpriority' => null,
  'width' => null,
  'height' => null,
])

@php
    $extless = preg_replace('/\.(png|jpe?g|webp|avif)$/i', '', $path);
    $prefix  = $base === 'public' ? '' : 'storage/';
    $srcset = fn($fmt) => collect($widths)->map(
        fn($w) => asset($prefix."{$extless}-{$w}.{$fmt}")." {$w}w"
    )->join(', ');
    $placeholder = asset($prefix . preg_replace('/\.(png|jpe?g|webp|avif)$/i', '-320.webp', $path));
@endphp

<picture>
  <source type="image/avif" srcset="{{ $srcset('avif') }}" sizes="{{ $sizes }}">
  <source type="image/webp" srcset="{{ $srcset('webp') }}" sizes="{{ $sizes }}">
  <img
    src="{{ $placeholder }}"
    alt="{{ $alt }}"
    class="{{ $class }}"
    loading="{{ $loading }}"
    decoding="async"
    @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
  >
</picture>