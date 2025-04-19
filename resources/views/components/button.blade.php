@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'inline-block px-6 py-3 bg-red-600 text-white font-semibold rounded-xl shadow hover:bg-red-700 transition'
    ]) }}
>
    {{ $slot }}
</button>
