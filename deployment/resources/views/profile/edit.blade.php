@php
    $layoutComponent = 'app-layout'; // Default layout, assuming standard Breeze <x-app-layout>
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'freelancer') {
            $layoutComponent = 'layouts.freelancer';
        } elseif ($role === 'client') {
            $layoutComponent = 'layouts.client';
        } elseif ($role === 'admin') {
            // Admin profile is handled by AdminProfileController and its own layout
            // This page is for 'user' (client/freelancer) profiles
            // If an admin somehow lands here, use app layout or redirect.
            // For now, assuming admin profile edit is separate.
        }
    }
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if (Auth::check() && Auth::user()->role !== 'freelancer')
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
