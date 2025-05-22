<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Proposal Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Status Banner -->
                    <div class="mb-6 p-4 rounded-lg
                        @if($proposal->status === 'accepted')
                            bg-green-100 dark:bg-green-900
                        @elseif($proposal->status === 'rejected')
                            bg-red-100 dark:bg-red-900
                        @else
                            bg-yellow-100 dark:bg-yellow-900
                        @endif">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold
                                    @if($proposal->status === 'accepted')
                                        text-green-800 dark:text-green-300
                                    @elseif($proposal->status === 'rejected')
                                        text-red-800 dark:text-red-300
                                    @else
                                        text-yellow-800 dark:text-yellow-300
                                    @endif">
                                    Proposal Status
                                </h3>
                                <p class="text-sm mt-1
                                    @if($proposal->status === 'accepted')
                                        text-green-700 dark:text-green-400
                                    @elseif($proposal->status === 'rejected')
                                        text-red-700 dark:text-red-400
                                    @else
                                        text-yellow-700 dark:text-yellow-400
                                    @endif">
                                    Last updated: {{ $proposal->updated_at->diffForHumans() }}
                                </p>                            </div>
                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                R{{ number_format($proposal->bid_amount) }}
                            </span>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $proposal->job->title }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Posted by {{ $proposal->job->user->clientProfile->company_name ?? $proposal->job->user->name }}
                            · Job posted {{ $proposal->job->created_at->diffForHumans() }}
                        </p>
                        <div class="mt-2">
                            <p class="text-gray-700 dark:text-gray-300">{{ $proposal->job->description }}</p>
                        </div>                        @if($proposal->job->budget)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Client's Budget: R{{ number_format($proposal->job->budget) }}
                            </p>
                        @endif
                        @if($proposal->job->skills_required)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Required Skills:</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach(explode(',', $proposal->job->skills_required) as $skill)
                                        <span class="px-2 py-1 text-sm bg-gray-200 dark:bg-gray-600 rounded">
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
                            <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">Your Proposal</h4>
                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                {{ $proposal->proposal_text }}
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Submitted {{ $proposal->created_at->diffForHumans() }}
                                </div>
                                <a href="{{ route('freelancer.proposals.index') }}" class="text-architimex-primary hover:text-architimex-primary-darker">
                                    ← Back to Proposals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
