<x-client-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Conversation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            {{ $conversation->job ? $conversation->job->title : 'General Conversation' }}
                        </h3>
                        <a href="{{ route('client.messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            &larr; {{ __('Back to Messages') }}
                        </a>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="space-y-4 mb-6 max-h-96 overflow-y-auto p-2" id="messages-container">
                            @foreach($conversation->messages as $message)
                                <div class="flex {{ $message->user_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[75%] {{ $message->user_id == Auth::id() ? 'bg-green-500 text-white rounded-br-none' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-bl-none' }} rounded-lg p-3 shadow relative">
                                        {{-- Optional: Add a tail to the message bubble --}}
                                        <div class="absolute bottom-0 {{ $message->user_id == Auth::id() ? 'right-0 mr-[-7px]' : 'left-0 ml-[-7px]' }} w-3 h-3 {{ $message->user_id == Auth::id() ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-700' }} transform rotate-45 origin-bottom"></div>
                                        
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-medium text-sm {{ $message->user_id == Auth::id() ? 'text-white opacity-90' : 'text-gray-700 dark:text-gray-300' }}">{{ $message->user->name }}</span>
                                            <span class="text-xs opacity-75 {{ $message->user_id == Auth::id() ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}">{{ $message->created_at->format('M d, H:i') }}</span>
                                        </div>
                                        <p class="text-sm break-words">{{ $message->body }}</p> {{-- Use message->body --}}
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

                        <form method="POST" action="{{ route('client.messages.store') }}" class="mt-4" enctype="multipart/form-data"> {{-- Add enctype for file uploads --}}
                            @csrf
                            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                            
                            <div class="mb-4">
                                <x-textarea-input 
                                    id="content" 
                                    name="content" 
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                    rows="3" 
                                    placeholder="{{ __('Type your message here...') }}" 
                                    required
                                ></x-textarea-input>
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            </div>
                            
                            <div class="flex justify-end items-center gap-2"> {{-- Added items-center and gap-2 --}}
                                {{-- Emoji button placeholder --}}
                                <button type="button" class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    😊 {{-- Simple emoji icon --}}
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Send Message') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Scroll to the bottom of the messages container when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messages-container');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    </script>
</x-client-layout>
