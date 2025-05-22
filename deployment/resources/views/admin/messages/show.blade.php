<x-admin-layout>
    <x-slot name="header">
        {{ __('Review Message') }}
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium">{{ __('Review Message') }}</h3>

        <div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-6">
                <p class="text-gray-600 text-sm">{{ __('From:') }} <span class="font-semibold">{{ $message->user->name }} ({{ $message->user->email }})</span></p>
                <p class="text-gray-600 text-sm">{{ __('Conversation/Job:') }} <span class="font-semibold">{{ $message->conversation->job->title ?? 'N/A' }}</span></p>
                <p class="text-gray-600 text-sm">{{ __('Received At:') }} <span class="font-semibold">{{ $message->created_at }}</span></p>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Message Content:') }}</h4>
                <div class="bg-gray-100 p-4 rounded-md text-gray-700">
                    {{ $message->content }}
                </div>
            </div>

            <!-- File Attachments -->
            @if ($message->attachments && $message->attachments->count() > 0)
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ __('File Attachments:') }}</h4>
                    <ul class="mt-2 space-y-2">
                        @foreach ($message->attachments as $attachment)
                            <li class="text-gray-700 flex items-center">
                                @php
                                    $filePath = Storage::url($attachment->file_path);
                                    $isImage = in_array(Storage::mimeType($attachment->file_path), ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                @endphp
                                @if ($isImage)
                                    <img src="{{ $filePath }}" alt="{{ $attachment->original_filename }}" class="max-w-xs h-auto mr-2 inline-block rounded-md shadow-sm">
                                @endif
                                <a href="{{ route('admin.messages.download-attachment', $attachment) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center">
                                     <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $attachment->original_filename ?? 'Download File' }}
                                </a>
                                @if (Storage::exists($attachment->file_path))
                                ({{ \App\Helpers\FileHelper::formatBytes(Storage::size($attachment->file_path)) }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Review Action:') }}</h4>
                <form action="{{ route('admin.messages.update', $message) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-architimex-primary focus:border-architimex-primary sm:text-sm rounded-md">
                            <option value="approved">{{ __('Approve') }}</option>
                            <option value="rejected">{{ __('Reject') }}</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="admin_remarks" class="block text-sm font-medium text-gray-700">{{ __('Admin Remarks (Optional)') }}</label>
                        <textarea id="admin_remarks" name="admin_remarks" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-architimex-primary focus:border-architimex-primary sm:text-sm"></textarea>
                        @error('admin_remarks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button class="bg-architimex-primary hover:bg-architimex-primary-darker">
                            {{ __('Submit Review') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
