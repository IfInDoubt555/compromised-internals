@props([
  'path',                  // storage-relative: e.g. 'posts/post_abc.png'
  'alt' => '',
  'sizes' => '(max-width: 768px) 100vw, 720px',
  'widths' => [160,320,640,960,1280],
  'class' => '',
  'loading' => 'lazy',     // 'eager' for LCP
  'fetchpriority' => null, // 'high' for LCP
  'width' => null,         // optional, to reduce CLS
  'height' => null,
])

@php
    $extless = preg_replace('/\.(png|jpe?g|webp|avif)$/i', '', $path);
    $srcset = fn($fmt) => collect($widths)->map(
        fn($w) => asset("storage/{$extless}-{$w}.{$fmt}")." {$w}w"
    )->join(', ');
    $placeholder = asset('storage/'.preg_replace('/\.(png|jpe?g)$/i','-320.webp',$path));
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