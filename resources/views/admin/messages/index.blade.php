<x-admin-layout>
    <x-slot name="header">
        {{ __('Messages Management') }}
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h3 class="text-gray-700 text-3xl font-medium">{{ __('Messages Management') }}</h3>
            <a href="{{ route('admin.messages.create') }}" class="bg-architimex-primary hover:bg-architimex-primary-darker text-white font-bold py-2 px-4 rounded">
                {{ __('New Message') }}
            </a>
        </div>

        <!-- Pending Messages Section -->
        @if($pendingMessages->count() > 0)
        <div class="mt-8">
            <h4 class="text-xl font-medium text-gray-700 mb-4">{{ __('Pending Messages for Review') }}</h4>
            <div class="flex flex-col">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Sender') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Conversation/Job') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Content') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Received At') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach ($pendingMessages as $message)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm leading-5 font-medium text-gray-900">{{ $message->user->name }}</div>
                                                    <div class="text-sm leading-5 text-gray-500">{{ $message->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">
                                                {{ $message->conversation->job->title ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">{{ \Illuminate\Support\Str::limit($message->content, 100) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                            {{ $message->created_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                            <a href="{{ route('admin.messages.show', $message) }}" class="text-architimex-primary hover:text-architimex-primary-darker">{{ __('Review') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Conversations Section -->
        <div class="mt-8">
            <h4 class="text-xl font-medium text-gray-700 mb-4">{{ __('All Conversations') }}</h4>
            <div class="flex flex-col">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Participants') }}
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
                                @forelse ($conversations as $conversation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex flex-col">
                                                <div class="text-sm leading-5 font-medium text-gray-900">
                                                    {{ $conversation->participant1->name }}
                                                    <span class="ml-1 px-2 py-1 text-xs rounded-full {{ $conversation->participant1->role === 'client' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ ucfirst($conversation->participant1->role) }}
                                                    </span>
                                                </div>
                                                <div class="text-sm leading-5 font-medium text-gray-900 mt-1">
                                                    {{ $conversation->participant2->name }}
                                                    <span class="ml-1 px-2 py-1 text-xs rounded-full {{ $conversation->participant2->role === 'client' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ ucfirst($conversation->participant2->role) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">
                                                {{ $conversation->job->title ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200">
                                            <div class="text-sm leading-5 text-gray-900">
                                                @if($conversation->messages->isNotEmpty())
                                                    {{ \Illuminate\Support\Str::limit($conversation->messages->sortByDesc('created_at')->first()->content, 100) }}
                                                @else
                                                    {{ __('No messages yet') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                            {{ $conversation->last_message_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                            <a href="{{ route('admin.messages.showConversation', $conversation) }}" class="text-architimex-primary hover:text-architimex-primary-darker">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center text-sm leading-5 text-gray-500">
                                            {{ __('No conversations found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $conversations->links() }}
        </div>
    </div>
</x-admin-layout>
