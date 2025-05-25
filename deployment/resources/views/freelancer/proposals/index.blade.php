<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Proposals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    @if($proposals->isEmpty())
                        <div class="text-center">
                            <p class="mb-4">You haven't submitted any proposals yet.</p>
                            <a href="{{ route('freelancer.jobs.browse') }}" class="text-cyan-700 hover:text-cyan-600">
                                Browse available jobs
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($proposals as $proposal)
                                <div class="border border-green-300 rounded-lg p-6">
                                    <div class="flex justify-between items-start">
                                        <div>                                            <h3 class="text-lg font-semibold">{{ $proposal->job->title }}</h3>
                                            <p class="text-sm text-gray-600">
                                                Bid Amount: R{{ number_format($proposal->bid_amount) }}
                                                · Submitted {{ $proposal->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <x-status-badge :status="$proposal->status" />
                                    </div>

                                    <div class="mt-4">
                                        <p class="text-sm text-gray-700">
                                            {{ Str::limit($proposal->proposal_text, 200) }}
                                        </p>
                                    </div>

                                    <div class="mt-4">
                                        <a href="{{ route('freelancer.proposals.show', $proposal) }}" class="text-sm text-blue-600 hover:text-blue-700">
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
</x-layouts.freelancer>
