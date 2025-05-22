<x-layouts.freelancer>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Messages') }} - {{ $conversation->job ? $conversation->job->title : 'No job title' }}
            </h2>
            <a href="{{ route('freelancer.messages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to all messages
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
                                        <div class="max-w-3/4 rounded-lg {{ $message->user_id === Auth::id() ? 'bg-cyan-700 text-white' : 'bg-gray-100 text-gray-900' }} px-4 py-2 shadow">
                                            <div class="font-bold text-sm mb-1">
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
                                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 sm:text-sm" placeholder="Type your message here..." required></textarea>
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
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
