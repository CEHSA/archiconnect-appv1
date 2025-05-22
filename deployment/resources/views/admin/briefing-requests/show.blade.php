<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Briefing Request Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Request Details</h3>
                        <p><strong>Client:</strong> {{ $briefingRequest->client->name }}</p>
                        <p><strong>Preferred Date:</strong> {{ $briefingRequest->preferred_date }}</p>
                        <p><strong>Preferred Time:</strong> {{ $briefingRequest->preferred_time }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($briefingRequest->status) }}</p>
                        <p><strong>Submitted At:</strong> {{ $briefingRequest->created_at->format('Y-m-d H:i') }}</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Project Overview</h3>
                        <p>{{ $briefingRequest->project_overview }}</p>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Update Status</h3>
                        <form method="POST" action="{{ route('admin.briefing-requests.update', $briefingRequest) }}">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="pending" {{ $briefingRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="scheduled" {{ $briefingRequest->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="completed" {{ $briefingRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $briefingRequest->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button class="ms-4">
                                    {{ __('Update Status') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.briefing-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Briefing Requests') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
