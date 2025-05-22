{{-- resources/views/components/freelancer-nav-link.blade.php --}}
{{-- This can be very similar to client/admin-nav-link, adjust styles if freelancer sidebar is different --}}
@props(['active'])
@php
$classes = ($active ?? false)
            ? 'block px-4 py-2.5 text-sm text-white bg-architimex-primary font-semibold rounded-md transition duration-150 ease-in-out'
            : 'block px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary-darker hover:text-white rounded-md transition duration-150 ease-in-out';
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
