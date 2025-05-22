<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Job Proposals') }}
            </h2>
            <a href="{{ route('client.jobs.show', $job) }}" class="text-architimex-primary hover:text-architimex-primary-darker">
                ← Back to Job Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Job Summary -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $job->title }}</h3>
                    @if($job->budget)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Budget: R{{ number_format($job->budget) }} per hour
                        </p>
                    @endif
                    <p class="mt-2 text-gray-700 dark:text-gray-300">{{ Str::limit($job->description, 200) }}</p>
                </div>
            </div>

            <!-- Proposals List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($proposals->isEmpty())
                        <p class="text-center py-4">No proposals received yet.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($proposals as $proposal)
                                <div class="border dark:border-gray-700 rounded-lg p-6">
                                    <!-- Freelancer Info -->
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $proposal->user->name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $proposal->user->freelancerProfile->experience_level }}
                                                · {{ $proposal->user->freelancerProfile->availability }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                                R{{ number_format($proposal->bid_amount) }} per hour
                                            </span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Submitted {{ $proposal->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Proposal Text -->
                                    <div class="mt-4">
                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ $proposal->proposal_text }}
                                        </p>
                                    </div>

                                    <!-- Status and Actions -->
                                    <div class="mt-6 flex items-center justify-between">
                                        <div>
                                            <span class="px-3 py-1 rounded-full text-sm
                                                @if($proposal->status === 'accepted')
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @elseif($proposal->status === 'rejected')
                                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @else
                                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                @endif">
                                                {{ ucfirst($proposal->status) }}
                                            </span>
                                        </div>
                                        @if($proposal->status === 'pending')
                                            <div class="flex gap-4">
                                                <form method="POST" action="{{ route('client.proposals.update-status', $proposal) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <x-primary-button>
                                                        {{ __('Accept Proposal') }}
                                                    </x-primary-button>
                                                </form>

                                                <form method="POST" action="{{ route('client.proposals.update-status', $proposal) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <x-danger-button>
                                                        {{ __('Reject Proposal') }}
                                                    </x-danger-button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
