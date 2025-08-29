@props([
  'name',
  'label' => null,
])

<div>
  @if ($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-stone-300 mb-1">
      {{ $label }}
    </label>
  @endif

  <textarea
    name="{{ $name }}"
    id="{{ $name }}"
    rows="6"
    {!! $attributes->merge([
      'class' =>
        'w-full px-4 py-2 rounded-xl border bg-white text-gray-900 '.
        'placeholder-gray-500 border-gray-300 focus:outline-none '.
        'focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition '.
        'dark:bg-stone-800/70 dark:text-stone-100 dark:placeholder-stone-500 '.
        'dark:border-white/10 dark:focus:ring-rose-400 dark:focus:border-rose-400'
    ]) !!}
  >{!! trim($slot) !!}</textarea>

  @error($name)
    <p class="text-sm text-red-600 dark:text-rose-300 mt-1">{{ $message }}</p>
  @enderror
</div>