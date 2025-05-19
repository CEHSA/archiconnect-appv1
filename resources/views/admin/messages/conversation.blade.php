<x-admin-layout>
    <x-slot name="header">
        {{ __('Conversation Details') }}
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">
                {{ __('Conversation') }}
                @if($conversation->job)
                    {{ __('for Job:') }} {{ $conversation->job->title }}
                @endif
            </h3>
            
            <div>
                <a href="{{ route('admin.messages.create', ['conversation' => $conversation->id]) }}" class="bg-architimex-primary hover:bg-architimex-primary-darker text-white font-bold py-2 px-4 rounded">
                    {{ __('Reply') }}
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md mt-6 p-6">
            <div class="mb-6">
                <p class="text-gray-600 text-sm">{{ __('Participants:') }}</p>
                <div class="flex items-center mt-2">
                    <div class="text-sm">
                        <span class="font-semibold">{{ $conversation->participant1->name }}</span>
                        ({{ $conversation->participant1->email }})
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $conversation->participant1->role === 'client' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($conversation->participant1->role) }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center mt-2">
                    <div class="text-sm">
                        <span class="font-semibold">{{ $conversation->participant2->name }}</span>
                        ({{ $conversation->participant2->email }})
                        <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $conversation->participant2->role === 'client' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($conversation->participant2->role) }}
                        </span>
                    </div>
                </div>
                @if($conversation->job)
                <div class="mt-4">
                    <p class="text-gray-600 text-sm">{{ __('Related Job:') }}</p>
                    <a href="{{ route('admin.jobs.show', $conversation->job) }}" class="text-architimex-primary hover:underline">
                        {{ $conversation->job->title }}
                    </a>
                </div>
                @endif
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Messages:') }}</h4>

                <div class="space-y-6">
                    @forelse($conversation->messages->sortBy('created_at') as $message)
                        <div class="p-4 rounded-lg {{ $message->user_id === $conversation->participant1_id ? 'bg-blue-50 ml-0 mr-12' : ($message->user_id === $conversation->participant2_id ? 'bg-green-50 ml-12 mr-0' : 'bg-gray-50') }}">
                            <div class="flex justify-between items-start">
                                <span class="font-medium text-gray-900">{{ $message->user->name }}</span>
                                <div>
                                    <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full {{ 
                                        $message->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                        ($message->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') 
                                    }}">
                                        {{ ucfirst($message->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 text-gray-700">
                                {{ $message->content }}
                            </div>
                            
                            @if($message->status === 'pending')
                                <div class="mt-4 flex justify-end">
                                    <form action="{{ route('admin.messages.update', $message) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs py-1 px-2 rounded mr-2">
                                            {{ __('Approve') }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.messages.show', $message) }}" class="bg-gray-500 hover:bg-gray-600 text-white text-xs py-1 px-2 rounded">
                                        {{ __('Review') }}
                                    </a>
                                </div>
                            @endif
                            
                            @if($message->attachments->count() > 0)
                                <div class="mt-3 border-t border-gray-200 pt-3">
                                    <p class="text-xs text-gray-500 mb-1">{{ __('Attachments:') }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="flex items-center text-xs text-architimex-primary hover:underline">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $attachment->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-500">
                            {{ __('No messages in this conversation yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('admin.messages.index') }}" class="text-gray-600 hover:text-gray-900">
                &larr; {{ __('Back to Messages') }}
            </a>
        </div>
    </div>
</x-admin-layout>
