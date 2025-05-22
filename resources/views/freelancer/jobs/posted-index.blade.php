<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jobs Posted to You') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($jobs->isEmpty())
                        <p class="text-gray-500">{{ __('No jobs have been posted directly to you yet.') }}</p>
                    @else
                        <div class="space-y-6">
                            @foreach ($jobs as $job)
                                <div class="p-6 bg-white border border-green-300 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 ease-in-out">
                                    <div class="flex justify-between items-start">
                                        <h3 class="text-2xl font-semibold text-cyan-700 hover:text-cyan-600">
                                            <a href="{{ route('freelancer.jobs.show', $job) }}">{{ $job->title }}</a>
                                        </h3>
                                        <span class="text-sm text-gray-500">Posted: {{ $job->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        By: {{ $job->user->clientProfile->company_name ?? $job->user->name }}
                                    </p>
                                    <div class="mt-4 prose max-w-none text-gray-700">
                                        {!! Str::limit(strip_tags($job->description), 250) !!}
                                    </div>
                                    <div class="mt-4 flex justify-between items-center">
                                        <div>
                                            @if ($job->budget)
                                                <span class="text-lg font-bold text-green-600">Budget: ${{ number_format($job->budget, 2) }}</span>
                                            @endif
                                            @if ($job->hourly_rate)
                                                <span class="text-lg font-bold text-green-600 ml-4">Rate: ${{ number_format($job->hourly_rate, 2) }}/hr</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('freelancer.jobs.show', $job) }}"
                                           class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('View Details & Apply') }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $jobs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
