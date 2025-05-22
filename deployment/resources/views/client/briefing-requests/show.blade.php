<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Briefing Request Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Request Details</h3>
                        <p><strong>Preferred Date:</strong> {{ $briefingRequest->preferred_date }}</p>
                        <p><strong>Preferred Time:</strong> {{ $briefingRequest->preferred_time }}</p>
                        <p><strong>Status:</strong> <x-status-badge :status="$briefingRequest->status" /></p>
                        <p><strong>Submitted At:</strong> {{ $briefingRequest->created_at->format('Y-m-d H:i') }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Project Overview</h3>
                        <p>{{ $briefingRequest->project_overview }}</p>
                    </div>

                    <!-- File Attachments -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">File Attachments</h3>
                        @if ($briefingRequest->attachments && $briefingRequest->attachments->count() > 0) {{-- Assuming a relationship named 'attachments' --}}
                            <ul class="mt-2 space-y-2">
                                @foreach ($briefingRequest->attachments as $attachment)
                                    <li class="text-gray-700 dark:text-gray-300 flex items-center">
                                        @php
                                            $filePath = Storage::url($attachment->file_path); // Assuming file_path is stored and accessible via Storage
                                            $isImage = in_array($attachment->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']); // Assuming file_type is stored, added webp support for preview images.
                                        @endphp
                                        @if ($isImage)
                                            <img src="{{ $filePath }}" alt="{{ $attachment->original_filename }}" class="max-w-xs h-auto mr-2 inline-block rounded-md shadow-sm">
                                        @endif
                                        <a href="{{ route('client.briefing-requests.download-attachment', $attachment) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $attachment->original_filename ?? 'Download File' }}
                                        </a>
                                        @if ($attachment->size)
                                        ({{ \App\Helpers\FileHelper::formatBytes($attachment->size) }}) {{-- Assuming size is stored and FileHelper exists --}}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-gray-600 dark:text-gray-400">No files were attached to this briefing request.</p>
                        @endif
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('client.briefing-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Briefing Requests') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
