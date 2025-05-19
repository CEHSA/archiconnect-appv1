<x-client-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Schedule Briefing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Request an Initial Briefing') }}</h3>

                    <p class="mb-6">{{ __('Please fill out the form below to request an initial briefing session with our admin team. This will help us understand your project needs.') }}</p>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('client.briefing.store') }}">
                        @csrf

                        {{-- Preferred Date/Time --}}
                        <div class="mb-4">
                            <x-input-label for="preferred_datetime" :value="__('Preferred Date and Time')" />
                            <x-text-input id="preferred_datetime" class="block mt-1 w-full" type="datetime-local" name="preferred_datetime" :value="old('preferred_datetime')" required />
                            <x-input-error :messages="$errors->get('preferred_datetime')" class="mt-2" />
                        </div>

                        {{-- Project Type --}}
                        <div class="mb-4">
                            <x-input-label for="project_type" :value="__('Project Type')" />
                            <x-text-input id="project_type" class="block mt-1 w-full" type="text" name="project_type" :value="old('project_type')" required />
                            <x-input-error :messages="$errors->get('project_type')" class="mt-2" />
                        </div>

                        {{-- Brief Description --}}
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Brief Description of Project')" />
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Submit Briefing Request') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-client-layout>
