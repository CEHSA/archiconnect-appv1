<x-freelancer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-gray-700 text-3xl font-medium">
                {{ __('Messages') }}
            </h2>
            <a href="{{ route('freelancer.messages.createAdmin') }}" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('New Message to Admin') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    @if($conversations->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No messages') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __("You don't have any message conversations yet.") }}
                            </p>
                        </div>
                    @else
                        <h4 class="text-xl font-medium text-gray-700 mb-4">{{ __('All Conversations') }}</h4>
                        <div class="flex flex-col">
                            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                    {{-- Status Icon --}}
                                                </th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Participant') }}
                                                </th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Related Job') }}
                                                </th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Last Message') }}
                                                </th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                    {{ __('Last Activity') }}
                                                </th>
                                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            @foreach($conversations as $conversation)
                                                @php
                                                    $currentUser = auth()->user();
                                                    $otherParticipant = null;
                                                    if ($conversation->participant1_id === $currentUser->id) {
                                                        $otherParticipant = $conversation->participant2;
                                                    } else if ($conversation->participant2_id === $currentUser->id) {
                                                        $otherParticipant = $conversation->participant1;
                                                    } else {
                                                        // Fallback or find based on participants relationship if direct properties are not set
                                                        // This case might indicate an issue or a different structure for participants
                                                        // For now, we assume participant1 or participant2 is the current user.
                                                        // If $conversation->participants is available:
                                                        // $otherParticipant = $conversation->participants->where('id', '!=', $currentUser->id)->first();
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                                                        @if ($conversation->hasUnreadMessagesForUser($currentUser->id))
                                                            <svg class="w-5 h-5 text-blue-500 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                                                            <span class="sr-only">Unread</span>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-400 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M12 12l6.75 4.5M12 12l-6.75 4.5"></path></svg>
                                                            <span class="sr-only">Read</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                        @if($otherParticipant)
                                                        <div class="text-sm leading-5 font-medium text-gray-900">
                                                            {{ $otherParticipant->name }}
                                                            <span class="ml-1 px-2 py-1 text-xs rounded-full
                                                                @if(optional($otherParticipant)->role === 'client') bg-blue-100 text-blue-800
                                                                @elseif(optional($otherParticipant)->role === 'admin') bg-red-100 text-red-800
                                                                @else bg-gray-100 text-gray-800 @endif">
                                                                {{ ucfirst(optional($otherParticipant)->role ?? 'User') }}
                                                            </span>
                                                        </div>
                                                        <div class="text-sm leading-5 text-gray-500">{{ $otherParticipant->email }}</div>
                                                        @else
                                                            <div class="text-sm leading-5 text-gray-500">N/A</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                        <div class="text-sm leading-5 text-gray-900">
                                                            {{ $conversation->job->title ?? 'General Conversation' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 border-b border-gray-200">
                                                        <div class="text-sm leading-5 text-gray-900">
                                                            @if($conversation->messages->isNotEmpty())
                                                                {{ \Illuminate\Support\Str::limit($conversation->messages->sortByDesc('created_at')->first()->content, 50) }}
                                                            @else
                                                                {{ __('No messages yet') }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                                        {{ ($conversation->last_message_at ?? $conversation->updated_at)->diffForHumans() }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                                        <a href="{{ route('freelancer.messages.show', $conversation) }}" class="text-cyan-600 hover:text-cyan-900">{{ __('View') }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $conversations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
