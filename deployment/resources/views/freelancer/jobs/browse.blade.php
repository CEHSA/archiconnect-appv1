<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Browse Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($jobs->isEmpty())
                        <p class="text-center py-4">No jobs available at the moment.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($jobs as $job)
                                <div class="border dark:border-gray-700 rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold">{{ $job->title }}</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Posted by {{ $job->user->clientProfile->company_name ?? $job->user->name }}
                                                · {{ $job->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <span class="text-lg font-semibold text-architimex-primary">
                                            @if($job->budget)
                                                R{{ number_format($job->budget) }} per hour
                                            @else
                                                Budget: Negotiable
                                            @endif
                                        </span>
                                    </div>

                                    <div class="mt-4">
                                        <p class="text-gray-700 dark:text-gray-300">{{ Str::limit($job->description, 200) }}</p>
                                    </div>

                                    @if($job->skills_required)
                                        <div class="mt-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(explode(',', $job->skills_required) as $skill)
                                                    <span class="px-2 py-1 text-sm bg-architimex-lightbg dark:bg-gray-600 rounded">
                                                        {{ trim($skill) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-6 flex gap-4">
                                        <!-- Check if user has already submitted a proposal -->
                                        @if($job->proposals->where('user_id', auth()->id())->count() > 0)
                                            <span class="text-gray-600 dark:text-gray-400">
                                                You have already submitted a proposal
                                            </span>
                                        @else
                                            <form action="{{ route('freelancer.proposals.store', $job) }}" method="get">
                                                <x-primary-button>
                                                    {{ __('Submit Proposal') }}
                                                </x-primary-button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $jobs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
