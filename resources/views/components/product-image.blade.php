{{-- Expects: $image (string|null), $colors (array<string,string>) --}}
<div class="flex justify-center">
  <img
    id="productImage"
    src="{{ $image ? asset($image) : (isset($colors['white']) ? asset($colors['white']) : asset('images/placeholder.png')) }}"
    alt="{{ $attributes->get('alt', 'Product Image') }}"
    width="{{ $attributes->get('width', 640) }}"
    height="{{ $attributes->get('height', 640) }}"
    class="rounded-lg w-full max-w-md h-auto {{ $attributes->get('class') }}"
    @foreach ($colors as $color => $path)
      data-{{ $color }}="{{ asset($path) }}"
    @endforeach
  />
</div>

@once
<script>
  function changeShirtColor(color) {
    const img = document.getElementById('productImage');
    if (!img) return;
    const key = 'data-' + String(color);
    const newSrc = img.getAttribute(key);
    if (newSrc) { img.src = newSrc; }
  }
</script>
@endonce