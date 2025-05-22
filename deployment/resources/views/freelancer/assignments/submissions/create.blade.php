<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit Work for Assignment: ') }} {{ $assignment->job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Assignment Details</h3>
                        <p><strong>Job Title:</strong> {{ $assignment->job->title }}</p>
                        <p><strong>Client:</strong> {{ $assignment->job->client->name }}</p>
                        <p><strong>Assignment Status:</strong> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->status === 'accepted' ? 'bg-green-100 text-green-800' : ($assignment->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($assignment->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                        </span></p>
                    </div>

                    <hr class="my-6 dark:border-gray-700">

                    <form method="POST" action="{{ route('freelancer.assignments.submissions.store', $assignment->id) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mt-4">
                            <x-input-label for="title" :value="__('Submission Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description / Notes (Optional)')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mt-4">
                            <x-input-label for="submission_file" :value="__('Submission File')" />
                            <input id="submission_file" name="submission_file" type="file" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800" required />
                            <x-input-error :messages="$errors->get('submission_file')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Max file size: 20MB. Allowed types: PDF, DOC, DOCX, ZIP, JPG, PNG.</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('freelancer.assignments.show', $assignment->id) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Submit for Review') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
