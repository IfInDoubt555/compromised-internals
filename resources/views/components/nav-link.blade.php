@props(['active' => false])

<a {{ $attributes->merge([
    'class' => 'inline-flex items-center px-1 pt-1 border-b-2 text-lg font-semibold tracking-wide leading-5 transition duration-150 ease-in-out' . 
        ($active ? ' border-red-500 text-red-600 focus:border-red-700' : ' border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300')
]) }}>
    {{ $slot }}
</a>
