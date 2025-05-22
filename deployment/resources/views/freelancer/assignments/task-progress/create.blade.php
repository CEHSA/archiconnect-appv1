<x-freelancer-layout>
    <x-slot name="header">
        {{ __('Submit Task Progress for Assignment: ') . $assignment->job->title }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Submit Progress Update</h3>

                    <form method="POST" action="{{ route('freelancer.assignments.task-progress.store', $assignment) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Task Description / Progress Update')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-architimex-primary focus:ring-architimex-primary rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mt-4">
                            <x-input-label for="progress_file" :value="__('Upload File (Optional)')" />
                            <input id="progress_file" name="progress_file" type="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-architimex-primary file:text-white hover:file:bg-architimex-primary-darker"/>
                            <x-input-error :messages="$errors->get('progress_file')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4 bg-architimex-primary hover:bg-architimex-primary-darker">
                                {{ __('Submit Progress') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
