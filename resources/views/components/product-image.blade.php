<div class="flex justify-center">
    <img
        id="productImage"
        src="{{ asset('storage/' . $image) }}"
        alt="Product Image"
        class="rounded-lg w-full max-w-md h-auto"
        @foreach($colors as $color=> $path)
    data-{{ $color }}="{{ asset('storage/' . $path) }}"
    @endforeach
    />
</div>

<script>
    function changeShirtColor(color) {
        const img = document.getElementById('productImage');
        if (!img) return;
        const newSrc = img.dataset[color];
        if (newSrc) {
            img.src = newSrc;
        }
    }
</script>