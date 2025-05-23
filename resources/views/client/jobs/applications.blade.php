<x-client-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Applications for: ') }} {{ $job->title }}
            </h2>
            <a href="{{ route('client.jobs.show', $job) }}" class="text-sm text-cyan-600 hover:text-cyan-800">
                &larr; {{ __('Back to Job Details') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    @if($applications->isEmpty())
                        <p class="text-center text-gray-500 py-4">{{ __('No applications received for this job posting yet.') }}</p>
                    @else
                        <div class="space-y-6">
                            @foreach($applications as $application)
                                <div class="border border-gray-200 rounded-lg p-4 sm:p-6">
                                    <div class="flex flex-col sm:flex-row justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ __('Applicant:') }} {{ $application->freelancer->name ?? 'N/A' }}
                                            </h4>
                                            @if($application->freelancer->freelancerProfile)
                                            <p class="text-sm text-gray-600">
                                                {{ $application->freelancer->freelancerProfile->experience_level ? ucfirst($application->freelancer->freelancerProfile->experience_level) . ' Level' : '' }}
                                                {{ $application->freelancer->freelancerProfile->availability ? '· ' . ucfirst($application->freelancer->freelancerProfile->availability) : '' }}
                                            </p>
                                            @endif
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ __('Submitted:') }} {{ $application->submitted_at->format('M d, Y H:i A') }}
                                            </p>
                                        </div>
                                        <div class="mt-2 sm:mt-0 sm:text-right">
                                            @if($application->proposed_rate)
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ format_currency($application->proposed_rate) }}
                                            </span>
                                            <p class="text-sm text-gray-600">{{ __('Proposed Rate') }}</p>
                                            @endif
                                            <div class="mt-1">
                                                <x-status-badge :status="$application->status" />
                                            </div>
                                        </div>
                                    </div>

                                    @if($application->estimated_timeline)
                                    <div class="mt-3">
                                        <p class="text-sm"><span class="font-medium">{{ __('Est. Timeline:') }}</span> {{ $application->estimated_timeline }}</p>
                                    </div>
                                    @endif

                                    <div class="mt-4">
                                        <h5 class="text-sm font-medium text-gray-700 mb-1">{{ __('Cover Letter:') }}</h5>
                                        <div class="p-3 bg-gray-50 rounded-md border border-gray-200 text-sm whitespace-pre-wrap">
                                            {{ $application->cover_letter ?? __('N/A') }}
                                        </div>
                                    </div>
                                    
                                    {{-- TODO: Add client actions if they are allowed to manage these applications (e.g., shortlist, comment) --}}
                                    {{-- This depends on workflow decisions. For now, it's view-only for clients. --}}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
