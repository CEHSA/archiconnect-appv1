<x-client-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white shadow sm:rounded-lg border border-green-300">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your account's profile information and company details.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('client.profile.store') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @if(auth()->user()->clientProfile && auth()->user()->clientProfile->id)
                                @method('patch')
                            @endif                            
                            <div>
                                <x-input-label for="company_name" :value="__('Company Name')" />
                                <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $user->clientProfile->company_name)" autofocus autocomplete="organization" />
                                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                            </div>

                            <div>
                                <x-input-label for="project_preferences" :value="__('Project Preferences')" />
                                <textarea id="project_preferences" name="project_preferences" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">{{ old('project_preferences', $user->clientProfile->project_preferences) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('project_preferences')" />
                            </div>

                            <div>
                                <x-input-label for="contact_details" :value="__('Contact Details')" />
                                <textarea id="contact_details" name="contact_details" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">{{ old('contact_details', $user->clientProfile->contact_details) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('contact_details')" />
                            </div>                            
                            <div>
                                <x-input-label for="company_website" :value="__('Company Website')" />
                                <x-text-input id="company_website" name="company_website" type="url" class="mt-1 block w-full" :value="old('company_website', $user->clientProfile->company_website ?? '')" autocomplete="url" />
                                <x-input-error class="mt-2" :messages="$errors->get('company_website')" />
                            </div>

                            <div>
                                <x-input-label for="industry" :value="__('Industry')" />
                                <x-text-input id="industry" name="industry" type="text" class="mt-1 block w-full" :value="old('industry', $user->clientProfile->industry ?? '')" autocomplete="organization-title" />
                                <x-input-error class="mt-2" :messages="$errors->get('industry')" />
                            </div>

                            <!-- Receive Application Notifications -->
                            <div class="mt-4">
                                <div class="flex items-center">
                                    <input id="receive_application_notifications" name="receive_application_notifications" type="checkbox" class="w-4 h-4 text-cyan-600 bg-gray-100 border-gray-300 rounded focus:ring-cyan-500 focus:ring-2" value="1" {{ old('receive_application_notifications', $user->clientProfile->receive_application_notifications ?? true) ? 'checked' : '' }}>
                                    <label for="receive_application_notifications" class="ms-2 text-sm font-medium text-gray-900">{{ __('Receive email notifications for new job applications on your posted jobs') }}</label>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('receive_application_notifications')" />
                            </div>

                            <div>
                                <x-input-label for="profile_picture" :value="__('Profile Picture (Logo)')" />
                                @if ($user->clientProfile && $user->clientProfile->profile_picture)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ asset('storage/' . $user->clientProfile->profile_picture) }}" alt="Current Profile Picture" class="h-20 w-auto rounded-md object-contain bg-gray-100 p-1">
                                    </div>
                                @endif
                                <input id="profile_picture" name="profile_picture" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" accept="image/*" />
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>                            
                            <div class="flex items-center gap-4 mt-6">
                                <x-primary-button>{{ __('Save Profile') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600"
                                    >{{ __('Profile updated successfully.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
