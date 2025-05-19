<x-layouts.client>
    <x-slot name="header">
        {{ __('Schedule a Briefing') }}
    </x-slot>

    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form method="POST" action="{{ route('client.briefing-requests.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Briefing Details') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('Please provide your preferred date and time for the briefing session.') }}</p>
                <!-- Preferred Date -->
                <div class="mt-4">
                    <x-input-label for="preferred_date" :value="__('Preferred Date')" />
                    <x-text-input id="preferred_date" class="block mt-1 w-full" type="date" name="preferred_date" :value="old('preferred_date')" required autofocus />
                    <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
                </div>

                <!-- Preferred Time -->
                <div class="mt-4">
                    <x-input-label for="preferred_time" :value="__('Preferred Time')" />
                    <x-text-input id="preferred_time" class="block mt-1 w-full" type="time" name="preferred_time" :value="old('preferred_time')" required />
                    <x-input-error :messages="$errors->get('preferred_time')" class="mt-2" />
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Project Information') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('Please provide a brief overview of your project to help us prepare for the briefing.') }}</p>
                <!-- Project Overview -->
                <div class="mt-4">
                    <x-input-label for="project_overview" :value="__('Project Overview')" />
                    <x-textarea-input id="project_overview" class="block mt-1 w-full" name="project_overview" rows="5" required>{{ old('project_overview') }}</x-textarea-input>
                    <x-input-error :messages="$errors->get('project_overview')" class="mt-2" />
                </div>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('File Attachments') }}</h3>
                <p class="mt-1 text-sm text-gray-600">{{ __('You can attach any relevant documents, images, or files related to your project (e.g., site plans, inspiration images).') }}</p>
                <!-- File Attachments -->
                <div class="mt-4">
                    <x-input-label for="attachments" :value="__('Select Files (Optional)')" />
                    <input id="attachments" class="block mt-1 w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-architimex-primary file:text-white
                        hover:file:bg-architimex-primary-darker" type="file" name="attachments[]" multiple />
                    <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Submit Briefing Request') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.client>
