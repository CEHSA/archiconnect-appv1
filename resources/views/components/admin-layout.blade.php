<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Architex Axis') }} Admin</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .subtle-grid {
                background-image: linear-gradient(to right, rgba(200, 200, 200, 0.1) 1px, transparent 1px),
                                linear-gradient(to bottom, rgba(200, 200, 200, 0.1) 1px, transparent 1px);
                background-size: 20px 20px; /* Adjust grid size */
            }
        </style>
        @stack('styles') {{-- Add this line --}}
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex bg-architimex-lightbg">
            <!-- Sidebar -->
            <aside class="w-64 bg-architimex-sidebar text-white flex-shrink-0">
                <div class="p-4 flex items-center space-x-2 border-b border-gray-700">
                    <img src="{{ asset('images/bird_logo.png') }}" alt="Logo" class="h-10 w-auto invert brightness-0"> {{-- Assuming logo is dark, invert for light on dark bg --}}
                    <h1 class="text-xl font-semibold">{{ config('app.name', 'Architex Axis') }}</h1>
                </div>
                <nav class="mt-4 px-2">
                    {{-- Sidebar Navigation Slot or Hardcoded Links --}}
                    <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.jobs.index')" :active="request()->routeIs('admin.jobs.*')">
                        {{ __('Jobs') }}
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.messages.index')" :active="request()->routeIs('admin.messages.*')">
                        <div class="flex items-center justify-between">
                            <span>{{ __('Messages') }}</span>
                            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                                    {{ $unreadMessagesCount }}
                                </span>
                            @endif
                        </div>
                    </x-admin-nav-link>
                    {{-- Add more links as per your image: Users, Reports, Settings --}}
                    <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        {{ __('Users') }}
                    </x-admin-nav-link>
                    {{-- Reports Section --}}
                    <x-admin-nav-link :href="route('admin.reports.job-progress')" :active="request()->routeIs('admin.reports.*')">
                        {{ __('Reports') }}
                    </x-admin-nav-link>
                    {{-- Sub-links for Reports (Optional, could be a dropdown or separate links) --}}
                    {{-- For now, just adding them as flat links under Reports --}}
                    <x-admin-nav-link :href="route('admin.reports.client-project-status')" :active="request()->routeIs('admin.reports.client-project-status')" class="ml-4">
                        {{ __('Client Project Status') }}
                    </x-admin-nav-link>
                    <x-admin-nav-link :href="route('admin.reports.financials')" :active="request()->routeIs('admin.reports.financials')" class="ml-4">
                        {{ __('Financials') }}
                    </x-admin-nav-link>

                     <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.settings.*')"> {{-- Placeholder route --}}
                        {{ __('Settings') }}
                    </x-admin-nav-link>
                </nav>
                <div class="p-4 mt-auto border-t border-gray-700">
                    <div class="flex items-center space-x-3">
                        {{-- User avatar placeholder --}}
                        <div class="w-10 h-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst(Auth::guard('admin')->user()->role) }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top header bar -->
                <header class="bg-white shadow-sm">
                    <div class="max-w-full mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <!-- Page Specific Header -->
                        @if (isset($header))
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $header }}
                            </h2>
                        @endif

                        <!-- Right side of header: Notifications, User dropdown -->
                        <div class="flex items-center space-x-4">
                           <!-- Notifications Bell Icon -->
                           <a href="{{ route('notifications.index') }}" class="relative text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if(Auth::guard('admin')->user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                        {{ Auth::guard('admin')->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                           </a>
                            <!-- Settings Dropdown -->
                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ Auth::guard('admin')->user()->name }}</div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')"> {{-- Make admin profile edit if needed --}}
                                            {{ __('Profile') }}
                                        </x-dropdown-link>
                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('admin.logout')"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                {{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main page content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto subtle-grid">
                    <div class="container mx-auto px-6 py-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts') {{-- Add this line --}}
    </body>
</html>
