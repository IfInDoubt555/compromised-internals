@props([
  'slug' => null,              // edit pages
  'value' => null,             // create pages
  'defaultMode' => 'auto',     // 'auto' | 'manual'
  'name' => 'slug',
  'label' => 'Slug',
  'source' => 'title',         // id of the input to mirror in auto mode
])

@php
  $initial = old($name, $slug ?? $value ?? '');
  $auto = $defaultMode === 'auto' && $initial === '';
@endphp

<div x-data="{
        auto: {{ $auto ? 'true' : 'false' }},
        slug: @js($initial),
        toSlug(s) {
          return (s || '').toString()
            .normalize('NFD').replace(/[\u0300-\u036f]/g,'')        // strip accents
            .toLowerCase().trim()
            .replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');     // kebab
        },
      }"
     x-init="
        const src = document.getElementById(@js($source));
        if (auto && src) { slug = toSlug(src.value) }
        if (src) {
          src.addEventListener('input', () => { if (auto) slug = toSlug(src.value) });
        }
     "
     class="space-y-2">

  <div class="flex items-center justify-between">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-stone-300">
      {{ $label }}
    </label>

    <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-stone-400 select-none">
      <input type="checkbox" x-model="auto"
             class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500
                    dark:bg-stone-800 dark:border-white/10">
      Auto from “{{ $source }}”
    </label>
  </div>

  <input
    id="{{ $name }}"
    name="{{ $name }}"
    type="text"
    x-model="slug"
    :readonly="auto"
    {{ $attributes->merge([
      'class' =>
        'w-full px-4 py-2 rounded-xl border bg-white border-gray-300
         placeholder-gray-500 focus:ring focus:ring-blue-200 focus:border-blue-400
         dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500'
    ]) }}
  />

  @error($name)
    <p class="text-sm text-red-600 dark:text-rose-300 mt-1">{{ $message }}</p>
  @enderror
</div>