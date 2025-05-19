<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Update Work Submission') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <a href="{{ route('admin.submissions.show', $submission->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Submission Details') }}
                        </a>
                    </div>

                    <h3 class="text-2xl font-semibold mb-1">{{ $submission->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Submitted by: {{ $submission->freelancer->name }} on {{ $submission->submitted_at->format('F j, Y H:i') }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">For Job: <a href="{{ route('admin.jobs.show', $submission->jobAssignment->job->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $submission->jobAssignment->job->title }}</a></p>
                    
                    @if($submission->file_path)
                        <div class="mb-4">
                            <span class="font-semibold">{{ __('Submitted File:') }}</span>
                            <a href="{{ route('admin.submissions.download', $submission->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline ml-2">
                                {{ $submission->original_filename }} ({{ \App\Helpers\FileHelper::formatBytes($submission->size) }})
                            </a>
                        </div>
                    @endif

                    <hr class="my-6 dark:border-gray-700">

                    <form method="POST" action="{{ route('admin.submissions.update', $submission->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Submission Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ old('status', $submission->status) == $statusOption ? 'selected' : '' }}>
                                        {{ Str::title(str_replace('_', ' ', $statusOption)) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Admin Remarks -->
                        <div class="mt-4">
                            <x-input-label for="admin_remarks" :value="__('Admin Remarks (Internal or for Freelancer)')" />
                            <textarea id="admin_remarks" name="admin_remarks" rows="5" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('admin_remarks', $submission->admin_remarks) }}</textarea>
                            <x-input-error :messages="$errors->get('admin_remarks')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Update Submission') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
