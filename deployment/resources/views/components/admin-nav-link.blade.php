{{-- Create a Blade component for admin sidebar navigation links.
     It should accept 'href' and 'active' (boolean) props.
     Active state should have a different background or text color (e.g., 'bg-gray-700' or 'text-white font-semibold').
     Default style: 'block px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded-md transition duration-150 ease-in-out'
--}}
@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block px-4 py-2.5 text-sm text-white bg-architimex-primary font-semibold rounded-md transition duration-150 ease-in-out'
            : 'block px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary-darker hover:text-white rounded-md transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
