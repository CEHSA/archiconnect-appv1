<x-client-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Description:') }}</strong>
                        <div class="mt-1 prose max-w-none">{!! $job->description !!}</div>
                    </div>                    
                    <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Budget:') }}</strong>
                        <p class="mt-1">{{ $job->budget ? format_currency($job->budget) : 'N/A' }}</p>
                    </div>
                     <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Hourly Rate:') }}</strong>
                        <p class="mt-1">{{ $job->hourly_rate ? format_currency($job->hourly_rate) . ' / hour' : 'N/A' }}</p>
                    </div>
                     <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Not to Exceed Budget:') }}</strong>
                        <p class="mt-1">{{ $job->not_to_exceed_budget ? format_currency($job->not_to_exceed_budget) : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Skills Required:') }}</strong>
                        <p class="mt-1">{{ $job->skills_required ?: 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Status:') }}</strong>
                        <div class="mt-1">
                            <x-status-badge :status="$job->status" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <strong class="text-gray-700">{{ __('Posted On:') }}</strong>
                        <p class="mt-1">{{ $job->created_at->toFormattedDateString() }}</p>
                    </div>

                    <!-- Assigned Freelancer/Assignment Section -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Assigned Freelancer / Assignment') }}</h3>
                        @if($job->assignments->isNotEmpty())
                            @foreach($job->assignments as $assignment)
                                <div class="mt-2 p-4 border rounded-md border-gray-200">
                                    <p><strong>Freelancer:</strong> {{ $assignment->freelancer->name }}</p>
                                    <p><strong>Assigned By:</strong> {{ $assignment->assignedByAdmin->name ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="mt-2 text-gray-600">No freelancer has been assigned to this job yet.</p>
                        @endif
                    </div>
                    
                    <!-- Actions Section -->
                    <div class="mt-6 flex flex-wrap gap-4 items-center">
                        <a href="{{ route('client.jobs.proposals', $job) }}" class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('View Proposals') }} ({{ $job->proposals->count() }})
                        </a>
                        <a href="{{ route('client.jobs.applications', $job) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('View Applications') }} 
                            {{-- Assuming $job->jobApplications relationship exists or is loaded --}}
                            ({{ $job->jobApplications->count() ?? $job->applications_count ?? 0 }}) 
                        </a>
                        <a href="{{ route('client.jobs.edit', $job) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit Job') }}
                        </a>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('client.jobs.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            &larr; {{ __('Back to Jobs List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
