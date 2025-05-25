<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Architex Axis') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @production
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Check if build directory exists, otherwise use Vite dev server -->
            @if(file_exists(public_path('build/manifest.json')))
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @else
                <script type="module" src="http://localhost:5173/@vite/client"></script>
                <link rel="stylesheet" href="http://localhost:5173/resources/css/app.css" />
                <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
            @endif
        @endproduction
        <style>
            body {
                background-color: #F4F7F9; /* Light gray background */
                background-image:
                linear-gradient(rgba(220, 226, 231, 0.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(220, 226, 231, 0.5) 1px, transparent 1px);
                background-size: 20px 20px;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 w-full">
            {{-- The slot will be filled by the login.blade.php content --}}
            {{ $slot }}
        </div>
    </body>
</html>
