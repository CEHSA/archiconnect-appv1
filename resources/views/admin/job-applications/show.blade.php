<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Application Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 md:p-8 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Application for:') }} 
                                <a href="{{ route('admin.jobs.show', $application->job_id) }}" class="text-cyan-600 hover:text-cyan-800">
                                    {{ $application->job->title ?? 'N/A' }}
                                </a>
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Submitted by:') }} 
                                <!-- TODO: Link to freelancer admin view if exists -->
                                <span class="font-semibold">{{ $application->freelancer->name ?? 'N/A' }}</span>
                                ({{ $application->freelancer->email ?? 'N/A' }})
                            </p>
                            <p class="mt-1 text-sm text-gray-600">{{ __('Submitted on:') }} {{ $application->submitted_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="md:text-right">
                            <p class="text-sm font-medium text-gray-500">{{ __('Current Status:') }}</p>
                            <x-status-badge :status="$application->status" class="text-lg" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-1">{{ __('Cover Letter') }}</h4>
                        <div class="p-4 bg-gray-50 rounded-md border border-gray-200 whitespace-pre-wrap text-sm">
                            {{ $application->cover_letter ?? __('No cover letter provided.') }}
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Proposed Rate') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->proposed_rate ? format_currency($application->proposed_rate) : __('Not specified') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Estimated Timeline') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->estimated_timeline ?? __('Not specified') }}</dd>
                        </div>
                    </dl>

                    <!-- Update Status Form -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">{{ __('Update Application Status') }}</h4>
                        <form method="POST" action="{{ route('admin.job-applications.updateStatus', $application) }}">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center space-x-4">
                                <div class="flex-grow">
                                    <x-input-label for="status" :value="__('New Status')" class="sr-only" />
                                    <select id="status" name="status" class="block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">
                                        <option value="submitted" {{ $application->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="viewed" {{ $application->status == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                        <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                        <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="accepted_for_assignment" {{ $application->status == 'accepted_for_assignment' ? 'selected' : '' }}>Accepted for Assignment</option>
                                        {{-- Add other relevant statuses --}}
                                    </select>
                                </div>
                                <x-primary-button class="bg-cyan-700 hover:bg-cyan-600">
                                    {{ __('Update Status') }}
                                </x-primary-button>
                            </div>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </form>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('admin.job-applications.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            &larr; {{ __('Back to All Applications') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
