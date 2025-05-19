<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Proposals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($proposals->isEmpty())
                        <div class="text-center">
                            <p class="mb-4">You haven't submitted any proposals yet.</p>
                            <a href="{{ route('freelancer.jobs.browse') }}" class="text-architimex-primary hover:text-architimex-primary-darker">
                                Browse available jobs
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($proposals as $proposal)
                                <div class="border dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex justify-between items-start">
                                        <div>                                            <h3 class="text-lg font-semibold">{{ $proposal->job->title }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Bid Amount: R{{ number_format($proposal->bid_amount) }}
                                                · Submitted {{ $proposal->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <x-status-badge :status="$proposal->status" />
                                    </div>

                                    <div class="mt-4">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ Str::limit($proposal->proposal_text, 200) }}
                                        </p>
                                    </div>

                                    <div class="mt-4">
                                        <a href="{{ route('freelancer.proposals.show', $proposal) }}" class="text-sm text-architimex-primary hover:text-architimex-primary-darker">
                                            View Details →
                                        </a>
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
