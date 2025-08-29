@props(['disabled' => false, 'type' => 'text'])

<input
  {{ $attributes->merge([
    'type' => $type,
    'class' =>
      'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm '.
      'placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 '.
      'dark:bg-stone-800/70 dark:text-stone-100 dark:placeholder-stone-500 dark:border-white/10 '.
      'dark:focus:ring-rose-400 dark:focus:border-rose-400'
  ]) }}
  @disabled($disabled)
/>