<button
  {{ $attributes->merge([
    'type' => 'button',
    'class' =>
      'inline-flex items-center justify-center px-4 py-2 rounded-md font-semibold text-xs '.
      'bg-white text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 '.
      'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 '.
      'transition ease-in-out duration-150 '.
      'dark:bg-stone-800/80 dark:text-stone-100 dark:border-white/10 dark:hover:bg-stone-700 '.
      'dark:focus:ring-rose-400 dark:focus:ring-offset-stone-900'
  ]) }}>
  {{ $slot }}
</button>