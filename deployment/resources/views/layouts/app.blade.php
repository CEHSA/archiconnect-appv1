<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Archi-TimeX') }}</title>

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
                <!-- Show a warning in dev mode if running without Vite dev server -->
                <script>
                    // Check if we can reach the Vite dev server
                    fetch('http://localhost:5173/@vite/client', { method: 'HEAD' })
                        .catch(e => {
                            console.error('Vite dev server not running. Run "npm run dev" to start it.');
                            document.body?.insertAdjacentHTML('afterbegin', 
                                '<div style="background:#f8d7da;color:#842029;padding:1rem;margin:1rem;border-radius:0.25rem;">' +
                                '<strong>Warning:</strong> Vite dev server not running. Run <code>npm run dev</code> to start it.' +
                                '</div>'
                            );
                        });
                </script>
            @endif
        @endproduction
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Toast Notifications -->
            <x-toast-notifications />
        </div>
    </body>
</html>
