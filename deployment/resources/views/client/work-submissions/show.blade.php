<x-client-layout> {{-- Changed to client layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Work Submission') }}: {{ $workSubmission->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('client.work-submissions.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; {{ __('Back to Submissions List') }}</a>
                    </div>

                    <h3 class="text-lg font-medium text-gray-700">Submission Details for: {{ $workSubmission->jobAssignment->job->title }}</h3>
                    <dl class="mt-2 divide-y divide-gray-200 border-t border-b border-gray-200">
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Submission Title</dt>
                            <dd class="text-gray-900">{{ $workSubmission->title }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Submitted by Freelancer</dt>
                            <dd class="text-gray-900">{{ $workSubmission->freelancer->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Date Submitted to Admin</dt>
                            <dd class="text-gray-900">{{ $workSubmission->submitted_at ? $workSubmission->submitted_at->format('M d, Y H:i A') : 'N/A' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Current Status</dt>
                            <dd class="text-gray-900"><x-status-badge :status="$workSubmission->status" /></dd>
                        </div>
                        @if($workSubmission->description)
                        <div class="py-3 text-sm font-medium">
                            <dt class="text-gray-500 mb-1">Freelancer's Description</dt>
                            <dd class="text-gray-900 whitespace-pre-wrap">{{ $workSubmission->description }}</dd>
                        </div>
                        @endif
                        @if($workSubmission->file_path)
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Submitted File</dt>
                            <dd>
                                <a href="{{ route('client.work-submissions.download', $workSubmission) }}" class="text-indigo-600 hover:text-indigo-700">
                                    {{ $workSubmission->original_filename ?? 'Download File' }} ({{ \App\Helpers\FileHelper::formatSize($workSubmission->size) }})
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($workSubmission->admin_remarks)
                        <div class="py-3 text-sm font-medium">
                            <dt class="text-gray-500 mb-1">Admin Remarks</dt>
                            <dd class="text-gray-900 whitespace-pre-wrap">{{ $workSubmission->admin_remarks }}</dd>
                        </div>
                        @endif
                    </dl>

                    <!-- Comments Section (Admin comments for this submission) -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Admin Feedback & Comments</h4>
                        @php
                            $adminComments = $workSubmission->comments->filter(function($comment) {
                                return $comment->user_type === \App\Models\Admin::class;
                            });
                        @endphp
                        @if($adminComments->isEmpty())
                            <p class="text-sm text-gray-500">No admin comments yet for this submission.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($adminComments as $comment)
                                    <div class="border border-gray-200 rounded-md p-3">
                                        <div class="flex justify-between items-center mb-1">
                                            <p class="font-semibold text-sm text-gray-800">
                                                Admin
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y H:i A') }}</p>
                                        </div>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment_text }}</p>
                                        @if($comment->screenshot_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($comment->screenshot_path) }}" alt="Screenshot" class="max-w-xs max-h-48 rounded border border-gray-200">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if($workSubmission->status === \App\Models\WorkSubmission::STATUS_PENDING_CLIENT_REVIEW || $workSubmission->status === 'ready_for_client_review')
                        <!-- Add Client Comment Form -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Add Your Comment/Feedback</h4>
                            <form method="POST" action="{{ route('client.jobs.comments.store', $workSubmission->jobAssignment->job_id) }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="work_submission_id" value="{{ $workSubmission->id }}">

                                <div>
                                    <x-input-label for="client_comment_content" :value="__('Comment')"/>
                                    <textarea id="client_comment_content" name="content" rows="3" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" required>{{ old('content') }}</textarea>
                                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-input-label for="client_screenshot" :value="__('Attach Screenshot (Optional)')"/>
                                    <input type="file" id="client_screenshot" name="screenshot" class="mt-1 block w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <x-input-error :messages="$errors->get('screenshot')" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <x-primary-button>
                                        {{ __('Post Comment') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>

                        <!-- Client Actions Form -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="text-lg font-medium text-gray-700 mb-2">Your Decision</h4>
                            <form method="POST" action="{{ route('client.work-submissions.update', $workSubmission) }}">
                                @csrf
                                @method('PATCH') {{-- Or PUT, depending on controller setup --}}

                                <div class="mt-4">
                                    <x-input-label for="client_remarks" :value="__('Overall Remarks (Optional)')"/>
                                    <textarea id="client_remarks" name="client_remarks" rows="3" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">{{ old('client_remarks', $workSubmission->client_remarks) }}</textarea>
                                    <x-input-error :messages="$errors->get('client_remarks')" class="mt-2" />
                                </div>

                                <div class="flex items-center space-x-4 mt-6">
                                    <x-primary-button type="submit" name="client_status" value="{{ \App\Models\WorkSubmission::STATUS_APPROVED_BY_CLIENT }}" onclick="return confirm('Are you sure you want to approve this submission?')">
                                        {{ __('Approve Submission') }}
                                    </x-primary-button>

                                    <x-secondary-button type="submit" name="client_status" value="{{ \App\Models\WorkSubmission::STATUS_CLIENT_REVISION_REQUESTED }}" onclick="return confirm('Are you sure you want to request revisions? Please provide remarks.')">
                                        {{ __('Request Revisions') }}
                                    </x-secondary-button>
                                </div>
                            </form>
                        </div>
                    @else
                        <p class="mt-6 text-sm text-gray-600">This submission is not currently awaiting your review. Current status: <x-status-badge :status="$workSubmission->status" /></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-client-layout>
