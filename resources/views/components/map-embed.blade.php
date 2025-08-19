@props([
  'src' => null,        // full Google My Maps embed URL
  'title' => 'Event Map',
])

@if ($src)
  <section {{ $attributes->class('max-w-6xl mx-auto px-4 mt-8') }}>
    <div class="rounded-2xl bg-white shadow-lg ring-1 ring-black/5 p-3">
      <div class="w-full aspect-[16/9]">
        <iframe
          src="{{ $src }}"
          title="{{ $title }}"
          class="w-full h-full rounded-xl border ring-1 ring-black/5"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen
        ></iframe>
      </div>
    </div>
  </section>
@endif