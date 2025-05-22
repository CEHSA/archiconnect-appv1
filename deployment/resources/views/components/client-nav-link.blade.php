{{-- resources/views/components/client-nav-link.blade.php --}}
{{-- This can be very similar to admin-nav-link, adjust colors/styles if client sidebar is different --}}
@props(['active'])
@php
$classes = ($active ?? false)
            ? 'block px-4 py-2.5 text-sm text-white bg-architimex-primary font-semibold rounded-md transition duration-150 ease-in-out' // Active style
            : 'block px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary-darker hover:text-white rounded-md transition duration-150 ease-in-out'; // Default style
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
