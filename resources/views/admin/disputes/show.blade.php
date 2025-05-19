<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dispute Details') }} #{{ $dispute->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dispute Information</h3>
                            <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Dispute ID</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $dispute->id }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">
                                        <x-status-badge :status="$dispute->status" />
                                    </dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Reported On</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $dispute->created_at->format('Y-m-d H:i A') }}</dd>
                                </div>
                                <div class="py-3 flex justify-between text-sm font-medium">
                                    <dt class="text-gray-500 dark:text-gray-400">Last Updated</dt>
                                    <dd class="text-gray-900 dark:text-gray-100">{{ $dispute->updated_at->format('Y-m-d H:i A') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Associated Job Assignment</h3>
                            @if ($dispute->jobAssignment)
                                <dl class="mt-2 divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="py-3 flex justify-between text-sm font-medium">
                                        <dt class="text-gray-500 dark:text-gray-400">Assignment ID</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('admin.job-assignments.show', $dispute->job_assignment_id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                #{{ $dispute->job_assignment_id }}
                                            </a>
                                        </dd>
                                    </div>
                                    @if ($dispute->jobAssignment->job)
                                    <div class="py-3 flex justify-between text-sm font-medium">
                                        <dt class="text-gray-500 dark:text-gray-400">Job Title</dt>
                                        <dd class="text-gray-900 dark:text-gray-100">
                                             <a href="{{ route('admin.jobs.show', $dispute->jobAssignment->job->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                {{ $dispute->jobAssignment->job->title }}
                                            </a>
                                        </dd>
                                    </div>
                                    @endif
                                </dl>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 mt-2">No job assignment associated.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Parties Involved</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                            <div>
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300">Reporter</h4>
                                @if ($dispute->reporter)
                                    <p>
                                        <a href="{{ route('admin.users.show', $dispute->reporter) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $dispute->reporter->name }}
                                        </a>
                                        ({{ $dispute->reporter->role }})
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $dispute->reporter->email }}</p>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">N/A</p>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-md font-medium text-gray-700 dark:text-gray-300">Reported User</h4>
                                @if ($dispute->reportedUser)
                                    <p>
                                        <a href="{{ route('admin.users.show', $dispute->reportedUser) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $dispute->reportedUser->name }}
                                        </a>
                                        ({{ $dispute->reportedUser->role }})
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $dispute->reportedUser->email }}</p>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">N/A</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dispute Reason</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $dispute->reason }}</p>
                    </div>

                    @if ($dispute->evidence_path)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Evidence</h3>
                            <a href="{{ route('admin.disputes.downloadEvidence', $dispute) }}" class="mt-2 inline-block text-indigo-600 dark:text-indigo-400 hover:underline">
                                Download Evidence File
                            </a>
                        </div>
                    @endif

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Admin Remarks</h3>
                        @if ($dispute->admin_remarks)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $dispute->admin_remarks }}</p>
                        @else
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No admin remarks yet.</p>
                        @endif
                    </div>

                    {{-- Dispute Update History --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dispute History</h3>
                        @if ($dispute->updates->count() > 0)
                            <div class="mt-2 space-y-4">
                                @foreach ($dispute->updates as $update)
                                    <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                            Update by {{ $update->user ? $update->user->name : 'System' }} on {{ $update->created_at->format('Y-m-d H:i A') }}
                                        </p>
                                        <ul class="mt-2 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                            @if ($update->old_status !== $update->new_status)
                                                <li>Status changed from <x-status-badge :status="$update->old_status" /> to <x-status-badge :status="$update->new_status" /></li>
                                            @endif
                                            @if ($update->old_admin_remarks !== $update->new_admin_remarks)
                                                <li>Admin Remarks updated.</li>
                                            @endif
                                        </ul>
                                        @if ($update->new_admin_remarks)
                                            <div class="mt-2 p-3 bg-gray-200 dark:bg-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                                <strong>Remarks:</strong> {{ $update->new_admin_remarks }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No history available yet.</p>
                        @endif
                    </div>
                    
                    @if ($dispute->client_remarks) <!-- Assuming client_remarks might be used -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Client Remarks</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $dispute->client_remarks }}</p>
                        </div>
                    @endif

                    {{-- Guidance on Communication --}}
                    <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md bg-blue-50 dark:bg-blue-900/50">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Communication with Parties') }}</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            To communicate with the reporter ({{ $dispute->reporter->name ?? 'N/A' }}) or the reported user ({{ $dispute->reportedUser->name ?? 'N/A' }}) regarding this dispute, please use the existing messaging system.
                            Navigate to the relevant job assignment or user profile to initiate or continue a conversation.
                        </p>
                    </div>

                    <div class="mt-8 border-t pt-6 border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.disputes.edit', $dispute) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                            Edit Dispute (Update Status/Remarks)
                        </a>
                        <a href="{{ route('admin.disputes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Back to All Disputes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
