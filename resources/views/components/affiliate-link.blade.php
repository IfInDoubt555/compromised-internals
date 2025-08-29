@props([
  'href',                 // destination URL (raw brand link)
  'brand' => null,        // 'booking'|'trip'|'agoda'|'expedia'|'viator'
  'subid' => null,        // e.g., 'CER-2025-hotels'
  'target' => '_blank',
  'class' => 'text-blue-600 hover:underline',
])

@php
  // Build redirect URL with encoded destination
  $params = ['u' => $href];
  if ($brand) $params['brand'] = $brand;
  if ($subid) $params['subid'] = $subid;

  $out = route('out', $params);
@endphp

<a href="{{ $out }}"
   target="{{ $target }}"
   rel="sponsored nofollow noopener"
   class="{{ $class }}">
   {{ $slot }}
</a>