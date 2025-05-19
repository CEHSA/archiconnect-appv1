<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Submit Proposal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Job Details Summary -->
                    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $job->title }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Posted by {{ $job->user->clientProfile->company_name ?? $job->user->name }}
                            · {{ $job->created_at->diffForHumans() }}
                        </p>
                        <div class="mt-2">
                            <p class="text-gray-700 dark:text-gray-300">{{ Str::limit($job->description, 200) }}</p>
                        </div>                        @if($job->budget)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Budget: R{{ number_format($job->budget) }}
                            </p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('freelancer.proposals.store', $job) }}" class="space-y-6">
                        @csrf                        <!-- Bid Amount -->
                        <div>
                            <x-input-label for="bid_amount" :value="__('Your Bid Amount (R)')" />
                            <x-text-input id="bid_amount" 
                                type="number" 
                                step="0.01" 
                                min="1"
                                name="bid_amount" 
                                :value="old('bid_amount')" 
                                class="block mt-1 w-full"
                                required />
                            <x-input-error :messages="$errors->get('bid_amount')" class="mt-2" />
                        </div>

                        <!-- Proposal Text -->
                        <div>
                            <x-input-label for="proposal_text" :value="__('Proposal Details')" />
                            <textarea id="proposal_text"
                                name="proposal_text"
                                rows="6"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-architimex-primary dark:focus:border-architimex-primary-darker focus:ring-architimex-primary dark:focus:ring-architimex-primary-darker rounded-md shadow-sm"
                                required>{{ old('proposal_text') }}</textarea>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Describe why you're the best fit for this job. Include relevant experience and your approach to the project. (Minimum 50 characters)
                            </p>
                            <x-input-error :messages="$errors->get('proposal_text')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Submit Proposal') }}</x-primary-button>
                            <a href="{{ route('freelancer.jobs.browse') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#proposal_text', {
            theme: 'snow' // Or 'bubble'
        });
    </script>
@endpush
</x-app-layout>
