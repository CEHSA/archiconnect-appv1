<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Work for Assignment: ') }} {{ $assignment->job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Assignment Details</h3>
                        <p><strong>Job Title:</strong> {{ $assignment->job->title }}</p>
                        <p><strong>Client:</strong> {{ $assignment->job->client->name }}</p>
                        <p><strong>Assignment Status:</strong> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->status === 'accepted' ? 'bg-green-100 text-green-800' : ($assignment->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($assignment->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                        </span></p>
                    </div>

                    <hr class="my-6 border-gray-200">

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
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mt-4">
                            <x-input-label for="submission_file" :value="__('Submission File')" />
                            <input id="submission_file" name="submission_file" type="file" class="block mt-1 w-full text-sm text-gray-900 border-gray-300 rounded-md shadow-sm focus:border-cyan-500 focus:ring-cyan-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-gray-100 file:text-gray-700
                                hover:file:bg-gray-200" required />
                            <x-input-error :messages="$errors->get('submission_file')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-600">Max file size: 20MB. Allowed types: PDF, DOC, DOCX, ZIP, JPG, PNG.</p>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('freelancer.assignments.show', $assignment->id) }}" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="bg-cyan-700 hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:ring-cyan-500">
                                {{ __('Submit for Review') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
