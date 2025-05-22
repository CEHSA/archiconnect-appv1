<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proposal Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6">
                    <!-- Status Banner -->
                    <div class="mb-6 p-4 rounded-lg
                        @if($proposal->status === 'accepted')
                            bg-green-100
                        @elseif($proposal->status === 'rejected')
                            bg-red-100
                        @else
                            bg-yellow-100
                        @endif">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold
                                    @if($proposal->status === 'accepted')
                                        text-green-800
                                    @elseif($proposal->status === 'rejected')
                                        text-red-800
                                    @else
                                        text-yellow-800
                                    @endif">
                                    Proposal Status
                                </h3>
                                <p class="text-sm mt-1
                                    @if($proposal->status === 'accepted')
                                        text-green-700
                                    @elseif($proposal->status === 'rejected')
                                        text-red-700
                                    @else
                                        text-yellow-700
                                    @endif">
                                    Last updated: {{ $proposal->updated_at->diffForHumans() }}
                                </p>                            </div>
                            <span class="text-lg font-bold text-gray-900">
                                R{{ number_format($proposal->bid_amount) }}
                            </span>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $proposal->job->title }}</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Posted by {{ $proposal->job->user->clientProfile->company_name ?? $proposal->job->user->name }}
                            · Job posted {{ $proposal->job->created_at->diffForHumans() }}
                        </p>
                        <div class="mt-2">
                            <p class="text-gray-700">{{ $proposal->job->description }}</p>
                        </div>                        @if($proposal->job->budget)
                            <p class="mt-2 text-sm text-gray-600">
                                Client's Budget: R{{ number_format($proposal->job->budget) }}
                            </p>
                        @endif
                        @if($proposal->job->skills_required)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-700">Required Skills:</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach(explode(',', $proposal->job->skills_required) as $skill)
                                        <span class="px-2 py-1 text-sm bg-gray-200 rounded">
                                            {{ trim($skill) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Proposal Details -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">Your Proposal</h4>
                            <div class="mt-4 text-gray-700">
                                {{ $proposal->proposal_text }}
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    Submitted {{ $proposal->created_at->diffForHumans() }}
                                </div>
                                <a href="{{ route('freelancer.proposals.index') }}" class="text-blue-600 hover:text-blue-700">
                                    ← Back to Proposals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
