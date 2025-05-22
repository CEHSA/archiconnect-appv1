<x-client-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Job Proposals') }}
            </h2>
            <a href="{{ route('client.jobs.show', $job) }}" class="text-cyan-700 hover:text-cyan-600">
                ← Back to Job Details
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Job Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-green-300">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $job->title }}</h3>
                    @if($job->budget)
                        <p class="mt-2 text-sm text-gray-600">
                            Budget: R{{ number_format($job->budget) }} per hour
                        </p>
                    @endif
                    <p class="mt-2 text-gray-700">{{ Str::limit($job->description, 200) }}</p>
                </div>
            </div>

            <!-- Proposals List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6">
                    @if($proposals->isEmpty())
                        <p class="text-center py-4">No proposals received yet.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($proposals as $proposal)
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <!-- Freelancer Info -->
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $proposal->user->name }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $proposal->user->freelancerProfile->experience_level }}
                                                · {{ $proposal->user->freelancerProfile->availability }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-gray-900">
                                                R{{ number_format($proposal->bid_amount) }} per hour
                                            </span>
                                            <p class="text-sm text-gray-600">
                                                Submitted {{ $proposal->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Proposal Text -->
                                    <div class="mt-4">
                                        <p class="text-gray-700">
                                            {{ $proposal->proposal_text }}
                                        </p>
                                    </div>

                                    <!-- Status and Actions -->
                                    <div class="mt-6 flex items-center justify-between">
                                        <div>
                                            <span class="px-3 py-1 rounded-full text-sm
                                                @if($proposal->status === 'accepted')
                                                    bg-green-100 text-green-800
                                                @elseif($proposal->status === 'rejected')
                                                    bg-red-100 text-red-800
                                                @else
                                                    bg-yellow-100 text-yellow-800
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
</x-client-layout>
