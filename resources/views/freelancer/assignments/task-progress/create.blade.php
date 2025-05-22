<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Task Progress for Assignment: ') . $assignment->job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Submit Progress Update</h3>

                    <form method="POST" action="{{ route('freelancer.assignments.task-progress.store', $assignment) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Task Description / Progress Update')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mt-4">
                            <x-input-label for="progress_file" :value="__('Upload File (Optional)')" />
                            <input id="progress_file" name="progress_file" type="file" class="block mt-1 w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-100 file:text-gray-700
                                hover:file:bg-gray-200"/>
                            <x-input-error :messages="$errors->get('progress_file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4 bg-cyan-700 hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:ring-cyan-500">
                                {{ __('Submit Progress') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
