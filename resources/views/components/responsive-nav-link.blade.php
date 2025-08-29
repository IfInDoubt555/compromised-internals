@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium 
               text-indigo-700 dark:text-indigo-300 
               bg-indigo-50 dark:bg-zinc-800 
               focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 
               focus:bg-indigo-100 dark:focus:bg-zinc-700 
               focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium 
               text-gray-600 dark:text-gray-300 
               hover:text-gray-800 dark:hover:text-white 
               hover:bg-gray-50 dark:hover:bg-zinc-800 
               hover:border-gray-300 dark:hover:border-gray-600 
               focus:outline-none focus:text-gray-800 dark:focus:text-white 
               focus:bg-gray-50 dark:focus:bg-zinc-700 
               focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>