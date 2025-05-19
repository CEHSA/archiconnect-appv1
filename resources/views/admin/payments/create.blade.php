<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="{
                    jobAssignments: {{ json_encode($jobAssignments) }},
                    selectedAssignmentId: '{{ old('job_assignment_id', '') }}',
                    selectedAssignment: null,
                    totalLoggedDuration: '00:00:00',

                    updateSelectedAssignment() {
                        this.selectedAssignment = this.jobAssignments.find(assignment => assignment.id == this.selectedAssignmentId);
                        this.calculateTotalLoggedDuration();
                    },

                    calculateTotalLoggedDuration() {
                        if (!this.selectedAssignment || !this.selectedAssignment.time_logs) {
                            this.totalLoggedDuration = '00:00:00';
                            return;
                        }

                        let totalSeconds = 0;
                        this.selectedAssignment.time_logs.forEach(log => {
                            // Assuming duration is stored as seconds or can be converted
                            // If duration is already in a time format like HH:MM:SS,
                            // you'll need to parse it. Assuming it's stored as total seconds for simplicity.
                            // If duration is HH:MM:SS string, parse it:
                            const parts = log.duration.split(':');
                            if (parts.length === 3) {
                                totalSeconds += parseInt(parts[0]) * 3600 + parseInt(parts[1]) * 60 + parseInt(parts[2]);
                            } else {
                                // Handle cases where duration might be stored differently
                                // For now, log a warning or handle based on actual data format
                                console.warn('Unexpected duration format:', log.duration);
                            }
                        });

                        const hours = Math.floor(totalSeconds / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);
                        const seconds = totalSeconds % 60;

                        this.totalLoggedDuration = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                }" x-init="updateSelectedAssignment()">

                    <div class="mb-6">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Payments List') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Create New Payment') }}</h3>

                    <form method="POST" action="{{ route('admin.payments.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="job_assignment_id" :value="__('Job Assignment')" />
                            <select id="job_assignment_id" name="job_assignment_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required x-model="selectedAssignmentId" @change="updateSelectedAssignment()">
                                <option value="">{{ __('Select a completed job assignment') }}</option>
                                @foreach ($jobAssignments as $assignment)
                                    <option value="{{ $assignment->id }}">
                                        {{ $assignment->job->title }} (Freelancer: {{ $assignment->freelancer->name }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('job_assignment_id')" class="mt-2" />
                        </div>

                        {{-- Display Assignment Details when selected --}}
                        <template x-if="selectedAssignment">
                            <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                                <h4 class="text-lg font-semibold mb-2">{{ __('Assignment Details') }}</h4>                                <p><strong>Job:</strong> <span x-text="selectedAssignment.job.title"></span></p>
                                <p><strong>Freelancer:</strong> <span x-text="selectedAssignment.freelancer.name"></span></p>
                                <p><strong>Assignment Status:</strong> <span x-text="selectedAssignment.status"></span></p>
                                <p><strong>Job Hourly Rate:</strong> R<span x-text="selectedAssignment.job.hourly_rate"></span></p>
                                <p><strong>Job Not-to-Exceed Budget:</strong> R<span x-text="selectedAssignment.job.not_to_exceed_budget"></span></p>
                                <p><strong>Total Logged Hours:</strong> <span x-text="totalLoggedDuration"></span></p>
                                {{-- TODO: Add display for approved hours if that logic is implemented --}}
                                {{-- TODO: Add suggested payment amount calculation based on logged/approved hours and hourly rate --}}
                            </div>
                        </template>

                        <div class="mb-4">
                            <x-input-label for="amount" :value="__('Payment Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                         <div class="mb-4">
                            <x-input-label for="admin_notes" :value="__('Admin Notes (Optional)')" />
                            <textarea id="admin_notes" name="admin_notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('admin_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Payment') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
