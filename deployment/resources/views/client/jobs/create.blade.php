<x-layouts.client>
    <x-slot name="header">
        {{ __('Post New Job') }}
    </x-slot>

    <div class="bg-white p-6 rounded-xl shadow-lg">
        <form method="POST" action="{{ route('client.jobs.store') }}">
            @csrf

            <!-- Title -->
            <div class="mb-4">
                <x-input-label for="title" :value="__('Job Title')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="mb-4">
                <x-input-label for="description" :value="__('Job Description')" />
                <x-textarea-input id="description" class="block mt-1 w-full" name="description" rows="5" required>{{ old('description') }}</x-textarea-input>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Budget -->
            <div class="mb-4">
                <x-input-label for="budget" :value="__('Budget (Optional)')" />
                <x-text-input id="budget" class="block mt-1 w-full" type="number" name="budget" :value="old('budget')" step="0.01" />
                <x-input-error :messages="$errors->get('budget')" class="mt-2" />
            </div>

            <!-- Skills Required -->
            <div class="mb-4">
                <x-input-label for="skills_required" :value="__('Skills Required (Comma-separated, Optional)')" />
                <x-text-input id="skills_required" class="block mt-1 w-full" type="text" name="skills_required" :value="old('skills_required')" />
                <x-input-error :messages="$errors->get('skills_required')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Post Job') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.client>
