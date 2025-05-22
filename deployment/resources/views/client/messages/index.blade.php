<x-client-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Your Conversations') }}</h3>

        @if($conversations->count())
            <div class="space-y-4">
                @foreach($conversations as $conversation)
                    <div class="border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <a href="{{ route('client.messages.show', $conversation) }}" class="block p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">
                                        {{ $conversation->job ? $conversation->job->title : 'General Conversation' }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        @if($conversation->participant1_id == Auth::id())
                                            {{ __('With: ') }} {{ $conversation->participant2->name ?? 'Unknown' }}
                                        @else
                                            {{ __('With: ') }} {{ $conversation->participant1->name ?? 'Unknown' }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'No messages yet' }}
                                    </p>
                                    @if($conversation->unreadCount(Auth::user()) > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-architimex-primary rounded-full">
                                            {{ $conversation->unreadCount(Auth::user()) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($conversation->messages->isNotEmpty())
                                <div class="mt-2 text-sm text-gray-600 truncate">
                                    <span class="font-medium">{{ $conversation->messages->first()->user->name }}:</span>
                                    {{ $conversation->messages->first()->content }}
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">{{ __("You don't have any messages yet.") }}</p>
            </div>
        @endif
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
