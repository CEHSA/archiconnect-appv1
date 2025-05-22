<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Apply for Job') }}: {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 md:p-8">
                    <!-- Job Summary -->
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">{{ $job->title }}</h3>
                        <p class="mt-1 text-sm text-gray-600">Posted by: {{ $job->user->name ?? 'N/A' }}</p>
                        <div class="mt-2 text-sm text-gray-700 prose max-w-none">
                            {!! Str::limit($job->description, 200) !!}
                        </div>
                        <a href="{{ route('freelancer.jobs.show', $job) }}" class="text-sm text-cyan-600 hover:text-cyan-800 mt-2 inline-block">View full job details &rarr;</a>
                    </div>

                    <form method="POST" action="{{ route('freelancer.job-applications.store') }}">
                        @csrf
                        <input type="hidden" name="job_posting_id" value="{{ $jobPosting->id }}">

                        <!-- Cover Letter -->
                        <div>
                            <x-input-label for="cover_letter" :value="__('Cover Letter')" />
                            <x-textarea-input id="cover_letter" name="cover_letter" class="mt-1 block w-full" rows="8" required>{{ old('cover_letter') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('cover_letter')" class="mt-2" />
                        </div>

                        <!-- Proposed Rate (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="proposed_rate" :value="__('Your Proposed Rate (USD, optional if job has fixed rate)')" />
                            <x-text-input id="proposed_rate" name="proposed_rate" type="number" step="0.01" class="mt-1 block w-full" :value="old('proposed_rate', $job->hourly_rate ?? $job->budget ?? '')" />
                            <x-input-error :messages="$errors->get('proposed_rate')" class="mt-2" />
                        </div>

                        <!-- Estimated Timeline (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="estimated_timeline" :value="__('Estimated Timeline (e.g., 2 weeks, 1 month, optional)')" />
                            <x-text-input id="estimated_timeline" name="estimated_timeline" type="text" class="mt-1 block w-full" :value="old('estimated_timeline')" />
                            <x-input-error :messages="$errors->get('estimated_timeline')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('freelancer.jobs.show', $jobPosting->job_id) }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="bg-green-600 hover:bg-green-500 focus:bg-green-700 active:bg-green-800 focus:ring-green-500">
                                {{ __('Submit Application') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
