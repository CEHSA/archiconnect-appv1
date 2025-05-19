<x-freelancer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Messages') }}
            </h2>
            <a href="{{ route('freelancer.messages.createAdmin') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('New Message to Admin') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($conversations->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                You don't have any message conversations yet.
                            </p>
                        </div>
                    @else
                        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                            <ul role="list" class="divide-y divide-gray-200">
                                @foreach($conversations as $conversation)
                                    <li>
                                        <a href="{{ route('freelancer.messages.show', $conversation) }}" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <p class="truncate text-sm font-medium text-indigo-600">
                                                            {{ $conversation->job ? $conversation->job->title : 'No associated job' }}
                                                        </p>
                                                    </div>
                                                    <div class="ml-2 flex flex-shrink-0">
                                                        <span class="inline-flex rounded-full {{ $conversation->hasUnreadMessages() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} px-2 text-xs font-semibold leading-5">
                                                            {{ $conversation->hasUnreadMessages() ? 'New messages' : 'Read' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <span>
                                                                {{ $conversation->messages->isEmpty() ? 'No messages' : Str::limit($conversation->messages->first()->content, 50) }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <p>
                                                            <time datetime="{{ $conversation->last_message_at ?? $conversation->created_at }}">
                                                                {{ ($conversation->last_message_at ?? $conversation->created_at)->diffForHumans() }}
                                                            </time>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
