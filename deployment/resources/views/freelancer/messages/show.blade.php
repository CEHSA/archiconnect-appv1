<x-layouts.freelancer>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Messages') }} - {{ $conversation->job ? $conversation->job->title : 'No job title' }}
            </h2>
            <a href="{{ route('freelancer.messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                &larr; {{ __('Back to Messages') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6">
                    <div class="flex flex-col">
                        <div class="flex-1 overflow-y-auto" style="max-height: 60vh;">
                            <div class="space-y-4 p-4">
                                @foreach($conversation->messages as $message)
                                    <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[75%] {{ $message->user_id == Auth::id() ? 'bg-green-500 text-white rounded-br-none' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-bl-none' }} rounded-lg p-3 shadow relative">
                                            {{-- Optional: Add a tail to the message bubble --}}
                                            <div class="absolute bottom-0 {{ $message->user_id == Auth::id() ? 'right-0 mr-[-7px]' : 'left-0 ml-[-7px]' }} w-3 h-3 {{ $message->user_id == Auth::id() ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }} transform rotate-45 origin-bottom"></div>

                                            <div class="flex justify-between items-center mb-1">
                                                <span class="font-medium text-sm {{ $message->user_id == Auth::id() ? 'text-white opacity-90' : 'text-gray-700 dark:text-gray-300' }}">{{ $message->user->name }}</span>
                                                <span class="text-xs opacity-75 {{ $message->user_id == Auth::id() ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}">{{ $message->created_at->format('M d, H:i') }}</span>
                                            </div>
                                            <p class="text-sm break-words">{{ $message->content }}</p> {{-- Use message->body --}}
                                            @if($message->attachments && $message->attachments->count() > 0)
                                                <div class="mt-2 border-t border-opacity-25 {{ $message->user_id == Auth::id() ? 'border-white' : 'border-gray-800 dark:border-gray-200' }} pt-2">
                                                    <div class="text-xs font-semibold {{ $message->user_id == Auth::id() ? 'text-white opacity-90' : 'text-gray-700 dark:text-gray-300' }}">Attachments:</div>
                                                    <div class="flex flex-wrap mt-1 gap-2">
                                                        @foreach($message->attachments as $attachment)
                                                            @php
                                                                $filePath = Storage::url($attachment->file_path);
                                                                $isImage = in_array(Storage::mimeType($attachment->file_path), ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                                            @endphp
                                                            <div class="flex flex-col items-center">
                                                                @if ($isImage)
                                                                    <img src="{{ $filePath }}" alt="{{ $attachment->original_name }}" class="max-w-[150px] h-auto rounded-md shadow-sm mb-1 cursor-pointer" onclick="window.open('{{ $filePath }}', '_blank')"> {{-- Use original_name and add click to open --}}
                                                                @endif
                                                                <a href="{{ $filePath }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs {{ $message->user_id == Auth::id() ? 'text-white bg-green-600 hover:bg-green-700' : 'text-gray-900 dark:text-gray-100 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500' }} rounded transition">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                                    </svg>
                                                                    {{ Str::limit($attachment->original_name ?? 'Download File', 15) }} {{-- Use original_name --}}
                                                                </a>
                                                                @if (Storage::exists($attachment->file_path))
                                                                <span class="text-xs {{ $message->user_id == Auth::id() ? 'text-white opacity-75' : 'text-gray-500 dark:text-gray-400' }}">({{ \App\Helpers\FileHelper::formatBytes(Storage::size($attachment->file_path)) }})</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t mt-4 pt-4">
                            <form action="{{ route('freelancer.messages.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">

                                <div class="mb-4">
                                    <label for="content" class="block text-sm font-medium text-gray-700">Message</label>
                                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Type your message here..." required></textarea>
                                    @error('content')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments (optional)</label>
                                    <input id="attachments" name="attachments[]" type="file" multiple class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor:pointer bg-gray-50 focus:outline-none">
                                    <p class="mt-1 text-xs text-gray-500">Max 5 files, each up to 5MB</p>
                                    @error('attachments.*')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Send Message
                                    </button>
                                    <p class="mt-2 text-xs text-gray-500">
                                        Note: Your message will be reviewed by an admin before being delivered.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
                                                {{ $message->user->name }}
                                                @if($message->status === 'pending')
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pending approval
                                                    </span>
                                                @elseif($message->status === 'rejected')
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm">{{ $message->content }}</p>
                                            @if($message->attachments && $message->attachments->count() > 0)
                                                <div class="mt-2">
                                                    <div class="text-xs font-semibold">Attachments:</div>
                                                    <div class="flex flex-wrap mt-1 gap-2">
                                                        @foreach($message->attachments as $attachment)
                                                            @php
                                                                $filePath = Storage::url($attachment->file_path);
                                                                $isImage = in_array(Storage::mimeType($attachment->file_path), ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                                                            @endphp
                                                            <div class="flex flex-col items-center">
                                                                @if ($isImage)
                                                                    <img src="{{ $filePath }}" alt="{{ $attachment->original_filename }}" class="max-w-xs h-auto rounded-md shadow-sm mb-1">
                                                                @endif
                                                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs text-gray-900 bg-gray-200 rounded hover:bg-gray-300 transition">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                                    </svg>
                                                                    {{ Str::limit($attachment->original_filename ?? 'Download File', 15) }}
                                                                </a>
                                                                @if (Storage::exists($attachment->file_path))
                                                                <span class="text-xs text-gray-500">({{ \App\Helpers\FileHelper::formatBytes(Storage::size($attachment->file_path)) }})</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="text-xs mt-1 {{ $message->user_id === Auth::id() ? 'text-cyan-200' : 'text-gray-500' }}">
                                                {{ $message->created_at->format('M j, Y g:i A') }}
                                                @if($message->read_at && $message->user_id === Auth::id())
                                                    <span class="ml-2">Read</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t mt-4 pt-4">
                            <form action="{{ route('freelancer.messages.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">

                                <div class="mb-4">
                                    <label for="content" class="block text-sm font-medium text-gray-700">Message</label>
                                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Type your message here..." required></textarea>
                                    @error('content')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments (optional)</label>
                                    <input id="attachments" name="attachments[]" type="file" multiple class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor:pointer bg-gray-50 focus:outline-none">
                                    <p class="mt-1 text-xs text-gray-500">Max 5 files, each up to 5MB</p>
                                    @error('attachments.*')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Send Message
                                    </button>
                                    <p class="mt-2 text-xs text-gray-500">
                                        Note: Your message will be reviewed by an admin before being delivered.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
