<x-admin-layout>
    <x-slot name="header">
        @if(isset($conversation))
            {{ __('Reply to Conversation') }}
        @else
            {{ __('Create New Message') }}
        @endif
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium">
            @if(isset($conversation))
                {{ __('Reply to Conversation') }}
                @if($conversation->job)
                    {{ __('for Job:') }} {{ $conversation->job->title }}
                @endif
            @else
                {{ __('Create New Message') }}
            @endif
        </h3>

        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if(isset($conversation))
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    
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
                    </div>
                @else
                    <div class="mb-6">
                        <label for="recipient_id" class="block text-sm font-medium text-gray-700">{{ __('Select Recipient') }}</label>
                        <select id="recipient_id" name="recipient_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-architimex-primary focus:border-architimex-primary sm:text-sm rounded-md">
                            <option value="">{{ __('-- Select a user --') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}</option>
                            @endforeach
                        </select>
                        @error('recipient_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="job_id" class="block text-sm font-medium text-gray-700">{{ __('Link to Job (Optional)') }}</label>
                        <select id="job_id" name="job_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-architimex-primary focus:border-architimex-primary sm:text-sm rounded-md">
                            <option value="">{{ __('-- No job selected --') }}</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job->id }}">{{ $job->title }} - {{ $job->user->name }} ({{ ucfirst($job->status) }})</option>
                            @endforeach
                        </select>
                        @error('job_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700">{{ __('Message') }}</label>
                    <textarea id="content" name="content" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-architimex-primary focus:border-architimex-primary sm:text-sm" placeholder="{{ __('Type your message here...') }}">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-gray-700">{{ __('Attachments (Optional)') }}</label>
                    <input type="file" id="attachments" name="attachments[]" multiple class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-medium
                        file:bg-architimex-primary file:text-white
                        hover:file:bg-architimex-primary-darker
                    ">
                    <p class="mt-1 text-xs text-gray-500">{{ __('You can select multiple files. Max 5MB per file.') }}</p>
                    @error('attachments.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ isset($conversation) ? route('admin.messages.showConversation', $conversation) : route('admin.messages.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-xs text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-architimex-primary mr-3">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button class="bg-architimex-primary hover:bg-architimex-primary-darker">
                        {{ __('Send Message') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
