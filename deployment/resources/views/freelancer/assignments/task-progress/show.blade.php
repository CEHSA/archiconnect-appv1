<x-freelancer-layout>
    <x-slot name="header">
        {{ __('Task Progress Details for Assignment: ') . $taskProgress->jobAssignment->job->title }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Progress Details</h3>
                        <p><strong>Assignment:</strong> {{ $taskProgress->jobAssignment->job->title }}</p>
                        <p><strong>Submitted At:</strong> {{ $taskProgress->created_at->format('Y-m-d H:i') }}</p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                        <p>{{ $taskProgress->description }}</p>
                    </div>

                    <!-- File Attachment -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">File Attachment</h3>
                        @if ($taskProgress->file_path)
                            @php
                                $filePath = Storage::url($taskProgress->file_path); // Assuming file_path is stored and accessible via Storage
                                $isImage = in_array($taskProgress->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']); // Use mime_type and add webp support for preview images.
                            @endphp
                            <div class="mt-2">
                                @if ($isImage)
                                    <img src="{{ $filePath }}" alt="{{ $taskProgress->original_filename }}" class="max-w-xs h-auto mr-2 inline-block rounded-md shadow-sm">
                                @endif
                                <a href="{{ route('freelancer.task-progress.download', $taskProgress) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center mt-2">
                                     <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $taskProgress->original_filename ?? 'Download Attached File' }}
                                </a>
                                @if ($taskProgress->size)
                                ({{ \App\Helpers\FileHelper::formatBytes($taskProgress->size) }}) {{-- Assuming size is stored and FileHelper exists --}}
                                @endif
                            </div>
                        @else
                            <p class="mt-2 text-gray-600">No file was attached to this progress update.</p>
                        @endif
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('freelancer.assignments.show', $taskProgress->jobAssignment) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to Assignment') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
