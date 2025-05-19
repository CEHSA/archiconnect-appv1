<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Dispute') }} #{{ $dispute->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.disputes.update', $dispute) }}">
                        @csrf
                        @method('PUT')

                        <!-- Dispute Summary (Read-only) -->
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Dispute Summary</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>ID:</strong> {{ $dispute->id }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Job Assignment:</strong> <a href="{{ route('admin.jobs.assignments.show', $dispute->job_assignment_id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">#{{ $dispute->job_assignment_id }}</a> ({{ $dispute->jobAssignment && $dispute->jobAssignment->job ? $dispute->jobAssignment->job->title : 'N/A' }})</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Reporter:</strong> {{ $dispute->reporter ? $dispute->reporter->name : 'N/A' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Reported User:</strong> {{ $dispute->reportedUser ? $dispute->reportedUser->name : 'N/A' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2"><strong>Reason:</strong><br>{{ $dispute->reason }}</p>
                             @if ($dispute->evidence_path)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2"><strong>Evidence:</strong> <a href="{{ route('admin.disputes.downloadEvidence', $dispute) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Download Evidence</a></p>
                            @endif
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <x-select-input id="status" name="status" class="block mt-1 w-full">
                                @foreach ($statuses as $statusValue)
                                    <option value="{{ $statusValue }}" {{ old('status', $dispute->status) == $statusValue ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $statusValue)) }}
                                    </option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Admin Remarks -->
                        <div class="mb-4">
                            <x-input-label for="admin_remarks" :value="__('Admin Remarks')" />
                            <x-textarea-input id="admin_remarks" name="admin_remarks" class="block mt-1 w-full" rows="5">{{ old('admin_remarks', $dispute->admin_remarks) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('admin_remarks')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.disputes.show', $dispute) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Update Dispute') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
