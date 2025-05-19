<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Freelancer Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your account's profile information.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('freelancer.profile.store') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @if(auth()->user()->freelancerProfile && auth()->user()->freelancerProfile->id)
                                @method('patch')
                            @endif

                            <div>
                                <x-input-label for="skills" :value="__('Skills (comma-separated)')" />
                                <x-textarea-input id="skills" name="skills" class="mt-1 block w-full">{{ old('skills', auth()->user()->freelancerProfile->skills ?? '') }}</x-textarea-input>
                                <x-input-error class="mt-2" :messages="$errors->get('skills')" />
                            </div>

                            <div>
                                <x-input-label for="portfolio_link" :value="__('Portfolio Link')" />
                                <x-text-input id="portfolio_link" name="portfolio_link" type="url" class="mt-1 block w-full" :value="old('portfolio_link', auth()->user()->freelancerProfile->portfolio_link ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('portfolio_link')" />
                            </div>

                            <div>
                                <x-input-label for="hourly_rate" :value="__('Hourly Rate (USD)')" />
                                <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" class="mt-1 block w-full" :value="old('hourly_rate', auth()->user()->freelancerProfile->hourly_rate ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('hourly_rate')" />
                            </div>

                            <div>
                                <x-input-label for="bio" :value="__('Bio')" />
                                <x-textarea-input id="bio" name="bio" class="mt-1 block w-full">{{ old('bio', auth()->user()->freelancerProfile->bio ?? '') }}</x-textarea-input>
                                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            </div>

                            <div>
                                <x-input-label for="availability" :value="__('Availability')" />
                                <select id="availability" name="availability" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="full-time" {{ old('availability', auth()->user()->freelancerProfile->availability ?? '') == 'full-time' ? 'selected' : '' }}>{{ __('Full-time') }}</option>
                                    <option value="part-time" {{ old('availability', auth()->user()->freelancerProfile->availability ?? '') == 'part-time' ? 'selected' : '' }}>{{ __('Part-time') }}</option>
                                    <option value="contract" {{ old('availability', auth()->user()->freelancerProfile->availability ?? '') == 'contract' ? 'selected' : '' }}>{{ __('Contract/Freelance') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('availability')" />
                            </div>

                            <div>
                                <x-input-label for="experience_level" :value="__('Experience Level')" />
                                <select id="experience_level" name="experience_level" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="entry" {{ old('experience_level', auth()->user()->freelancerProfile->experience_level ?? '') == 'entry' ? 'selected' : '' }}>{{ __('Entry-level') }}</option>
                                    <option value="intermediate" {{ old('experience_level', auth()->user()->freelancerProfile->experience_level ?? '') == 'intermediate' ? 'selected' : '' }}>{{ __('Intermediate') }}</option>
                                    <option value="senior" {{ old('experience_level', auth()->user()->freelancerProfile->experience_level ?? '') == 'senior' ? 'selected' : '' }}>{{ __('Senior') }}</option>
                                    <option value="expert" {{ old('experience_level', auth()->user()->freelancerProfile->experience_level ?? '') == 'expert' ? 'selected' : '' }}>{{ __('Expert') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('experience_level')" />
                            </div>

                            <div>
                                <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                                @if (auth()->user()->freelancerProfile && auth()->user()->freelancerProfile->profile_picture)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ asset('storage/' . auth()->user()->freelancerProfile->profile_picture) }}" alt="Current Profile Picture" class="h-20 w-auto rounded-md object-contain">
                                    </div>
                                @endif
                                <input id="profile_picture" name="profile_picture" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" accept="image/*" />
                                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            </div>

                            <!-- Receive New Job Notifications -->
                            <div>
                                <div class="flex items-center">
                                    <input id="receive_new_job_notifications" name="receive_new_job_notifications" type="checkbox" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" {{ old('receive_new_job_notifications', $freelancerProfile->receive_new_job_notifications ?? true) ? 'checked' : '' }} value="1">
                                    <label for="receive_new_job_notifications" class="ms-2 text-sm font-medium text-gray-900">{{ __('Receive email notifications for new job postings') }}</label>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('receive_new_job_notifications')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
