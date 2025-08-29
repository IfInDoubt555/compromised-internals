@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }}
  {!! $attributes->merge([
    'class' => 'block w-full rounded-xl px-4 py-2 shadow-sm
                border border-gray-300 bg-white placeholder-gray-500
                focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500
                dark:bg-stone-800/60 dark:text-stone-100 dark:border-white/10 dark:placeholder-stone-500
                dark:focus:ring-emerald-400 dark:focus:border-emerald-400'
  ]) !!}
>