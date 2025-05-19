<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Job') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('client.jobs.update', $job) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Title -->
                        <div class="mt-4">
                            <x-input-label for="title" :value="__('Job Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $job->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Job Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="5" required>{{ old('description', $job->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Budget -->
                        <div class="mt-4">
                            <x-input-label for="budget" :value="__('Budget (Optional)')" />
                            <x-text-input id="budget" class="block mt-1 w-full" type="number" name="budget" :value="old('budget', $job->budget)" step="0.01" />
                            <x-input-error :messages="$errors->get('budget')" class="mt-2" />
                        </div>

                        <!-- Skills Required -->
                        <div class="mt-4">
                            <x-input-label for="skills_required" :value="__('Skills Required (Comma-separated, Optional)')" />
                            <x-text-input id="skills_required" class="block mt-1 w-full" type="text" name="skills_required" :value="old('skills_required', $job->skills_required)" />
                            <x-input-error :messages="$errors->get('skills_required')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="open" {{ old('status', $job->status) == 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                                <option value="in_progress" {{ old('status', $job->status) == 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                <option value="completed" {{ old('status', $job->status) == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="cancelled" {{ old('status', $job->status) == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-3">
                                {{ __('Update Job') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
