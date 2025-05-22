<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Budget Appeal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <a href="{{ route('freelancer.assignments.show', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to Assignment') }}
                        </a>
                    </div>

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Budget Appeal for Job:') }} {{ $assignment->job->title }}</h3>

                    <form method="POST" action="{{ route('freelancer.assignments.budget-appeals.store', $assignment) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="current_budget" :value="__('Current Not-to-Exceed Budget')" />
                            <x-text-input id="current_budget" class="block mt-1 w-full" type="text" name="current_budget" :value="old('current_budget', $assignment->job->not_to_exceed_budget)" required readonly />
                            <x-input-error :messages="$errors->get('current_budget')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="requested_budget" :value="__('Requested New Budget')" />
                            <x-text-input id="requested_budget" class="block mt-1 w-full" type="number" step="0.01" name="requested_budget" :value="old('requested_budget')" required autofocus />
                            <x-input-error :messages="$errors->get('requested_budget')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="reason" :value="__('Reason for Appeal')" />
                            <textarea id="reason" name="reason" rows="6" class="block mt-1 w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" required>{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="evidence" :value="__('Evidence (Optional)')" />
                            <input id="evidence" class="block mt-1 w-full text-gray-900 border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-100 file:text-gray-700
                                hover:file:bg-gray-200"
                                type="file" name="evidence" />
                            <x-input-error :messages="$errors->get('evidence')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4 bg-cyan-700 hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:ring-cyan-500">
                                {{ __('Submit Appeal') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
