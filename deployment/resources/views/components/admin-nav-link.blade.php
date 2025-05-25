@props(["active", "href"])

@php
$classes = ($active ?? false)
    ? "block px-4 py-2 text-sm text-white bg-architimex-primary rounded-md transition-colors duration-150 ease-in-out"
    : "block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-architimex-primary-darker rounded-md transition-colors duration-150 ease-in-out";
@endphp

<a {{ $attributes->merge(["class" => $classes, "href" => $href]) }}>
    {{ $slot }}
</a>
