<button
  {{ $attributes->merge([
    'type' => 'submit',
    'class' =>
      'inline-flex items-center justify-center rounded-lg px-4 py-2 font-semibold text-white '.
      'bg-red-600 hover:bg-red-500 active:bg-red-700 transition '.
      'focus:outline-none focus:ring-2 focus:ring-rose-400 '.
      'dark:bg-rose-700 dark:hover:bg-rose-600 dark:active:bg-rose-800'
  ]) }}
>
  {{ $slot }}
</button>