<button
  {{ $attributes->merge([
    'type' => 'submit',
    'class' =>
      'inline-flex items-center justify-center rounded-lg px-4 py-2 font-semibold text-white '.
      'bg-red-600 hover:bg-red-700 shadow transition disabled:opacity-50 disabled:cursor-not-allowed '.
      'focus:outline-none focus:ring-2 focus:ring-rose-400 '.
      'dark:bg-rose-600 dark:hover:bg-rose-500'
  ]) }}
>
  {{ $slot }}
</button>