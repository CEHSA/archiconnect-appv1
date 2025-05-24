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
                background-size: 20px 20px;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex bg-architimex-lightbg">
            <!-- Sidebar -->
            <aside class="w-64 bg-architimex-sidebar text-white flex-shrink-0">
                <div class="p-4 flex items-center space-x-2 border-b border-gray-700">
                    <img src="{{ asset('images/bird_logo.png') }}" alt="Logo" class="h-10 w-auto invert brightness-0">
                    <h1 class="text-xl font-semibold">{{ config('app.name', 'Architex Axis') }}</h1>
                </div>

                <nav class="mt-4 px-2 space-y-1"
                     x-data="{
                         activeAccordion: $persist('{{ request()->segment(2) }}').as('adminMenuState'),
                         isActiveSection(section) {
                             return window.location.pathname.startsWith('/admin/' + section);
                         }
                     }">
                    <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-admin-nav-link>

                    <!-- Jobs Section -->
                    <div class="space-y-1">
                        <button @click.stop="activeAccordion = isActiveSection('jobs') ? 'jobs' : (activeAccordion === 'jobs' ? '' : 'jobs')"
                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary hover:text-white rounded-md">
                            <span>{{ __('Jobs') }}</span>
                            <svg class="w-4 h-4 transform transition-transform"
                                :class="{'rotate-180': activeAccordion === 'jobs'}"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeAccordion === 'jobs'"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="pl-4 pr-1 space-y-1">
                            <x-admin-nav-link :href="route('admin.jobs.index')" :active="request()->routeIs('admin.jobs.index')">
                                {{ __('All Jobs') }}
                            </x-admin-nav-link>
                            <x-admin-nav-link :href="route('admin.jobs.current')" :active="request()->routeIs('admin.jobs.current')">
                                {{ __('Current Jobs') }}
                            </x-admin-nav-link>
                            <x-admin-nav-link :href="route('admin.jobs.create')" :active="request()->routeIs('admin.jobs.create')">
                                {{ __('Create Job') }}
                            </x-admin-nav-link>
                        </div>
                    </div>

                    <!-- Users Section -->
                    <div class="space-y-1">
                        <button @click.stop="activeAccordion = isActiveSection('users') ? 'users' : (activeAccordion === 'users' ? '' : 'users')"
                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary hover:text-white rounded-md">
                            <span>{{ __('Users') }}</span>
                            <svg class="w-4 h-4 transform transition-transform"
                                :class="{'rotate-180': activeAccordion === 'users'}"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeAccordion === 'users'"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="pl-4 pr-1 space-y-1">
                            <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                                {{ __('All Users') }}
                            </x-admin-nav-link>
                            <x-admin-nav-link :href="route('admin.users.activity.index')" :active="request()->routeIs('admin.users.activity.index')">
                                {{ __('User Activity') }}
                            </x-admin-nav-link>
                        </div>
                    </div>

                    <!-- Messages Section -->
                    <div class="space-y-1">
                        <button @click.stop="activeAccordion = isActiveSection('messages') ? 'messages' : (activeAccordion === 'messages' ? '' : 'messages')"
                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary hover:text-white rounded-md">
                            <span>{{ __('Messages') }}</span>
                            <svg class="w-4 h-4 transform transition-transform"
                                :class="{'rotate-180': activeAccordion === 'messages'}"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeAccordion === 'messages'"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="pl-4 pr-1 space-y-1">
                            <x-admin-nav-link :href="route('admin.messages.index')" :active="request()->routeIs('admin.messages.index')">
                                {{ __('Inbox') }}
                            </x-admin-nav-link>
                            <x-admin-nav-link :href="route('admin.messages.history')" :active="request()->routeIs('admin.messages.history')">
                                {{ __('History') }}
                            </x-admin-nav-link>
                        </div>
                    </div>

                    <!-- Reports Section -->
                    <div class="space-y-1">
                        <button @click.stop="activeAccordion = isActiveSection('reports') ? 'reports' : (activeAccordion === 'reports' ? '' : 'reports')"
                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-gray-300 hover:bg-architimex-primary hover:text-white rounded-md">
                            <span>{{ __('Reports') }}</span>
                            <svg class="w-4 h-4 transform transition-transform"
                                :class="{'rotate-180': activeAccordion === 'reports'}"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="activeAccordion === 'reports'"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             class="pl-4 pr-1 space-y-1">
                            <x-admin-nav-link :href="route('admin.reports.client-project-status')" :active="request()->routeIs('admin.reports.client-project-status')">
                                {{ __('Project Status') }}
                            </x-admin-nav-link>
                            <x-admin-nav-link :href="route('admin.reports.financials')" :active="request()->routeIs('admin.reports.financials')">
                                {{ __('Financials') }}
                            </x-admin-nav-link>
                        </div>
                    </div>

                    <!-- Settings Link -->
                    <x-admin-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                        {{ __('Settings') }}
                    </x-admin-nav-link>
                </nav>

                <div class="p-4 mt-auto border-t border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                            <p class="text-xs text-gray-400">Administrator</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="bg-white shadow">
                    <div class="max-w-full mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        @if (isset($header))
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $header }}
                            </h2>
                        @endif

                        <div class="flex items-center space-x-4">
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
                                        <x-dropdown-link :href="route('admin.profile.edit')">
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

                <!-- Main Content Area -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 subtle-grid">
                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
