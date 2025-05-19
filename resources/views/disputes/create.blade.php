<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report a Dispute for Assignment') }} #{{ $jobAssignment->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Job Assignment Details</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Assignment ID:</strong> {{ $jobAssignment->id }}</p>
                        @if ($jobAssignment->job)
                            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Job Title:</strong> {{ $jobAssignment->job->title }}</p>
                            @if (Auth::user()->id === $jobAssignment->job->user_id) <!-- User is Client -->
                                <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Freelancer:</strong> {{ $jobAssignment->freelancer ? $jobAssignment->freelancer->name : 'N/A' }}</p>
                            @else <!-- User is Freelancer -->
                                <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Client:</strong> {{ $jobAssignment->job->client ? $jobAssignment->job->client->name : 'N/A' }}</p>
                            @endif
                        @endif
                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Status:</strong> {{ ucfirst($jobAssignment->status) }}</p>
                    </div>

                    <form method="POST" action="{{ route('job_assignments.disputes.store', $jobAssignment) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Reason -->
                        <div class="mb-4">
                            <x-input-label for="reason" :value="__('Reason for Dispute')" />
                            <x-textarea-input id="reason" name="reason" class="block mt-1 w-full" rows="6" required>{{ old('reason') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Please describe the issue clearly and concisely (max 2000 characters).</p>
                        </div>

                        <!-- Evidence -->
                        <div class="mb-4">
                            <x-input-label for="evidence" :value="__('Evidence (Optional)')" />
                            <input type="file" id="evidence" name="evidence" class="block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 mt-1 p-2.5">
                            <x-input-error :messages="$errors->get('evidence')" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Supported formats: JPG, JPEG, PNG, PDF, DOC, DOCX. Max file size: 5MB.</p>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            @php
                                $dashboardRoute = 'dashboard'; // Fallback
                                if (Auth::user()->hasRole('client')) $dashboardRoute = route('client.dashboard');
                                elseif (Auth::user()->hasRole('freelancer')) $dashboardRoute = route('freelancer.dashboard');
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Submit Dispute') }}
                            </x-primary-button>
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
        var quill = new Quill('#reason', {
            theme: 'snow' // Or 'bubble'
        });
    </script>
@endpush
</x-app-layout>
