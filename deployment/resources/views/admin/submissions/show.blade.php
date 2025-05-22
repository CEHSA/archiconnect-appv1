<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Work Submission Details') }}: {{ $submission->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-architimex-sidebar">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('admin.jobs.show', $submission->jobAssignment->job_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition ease-in-out duration-150">&larr; {{ __('Back to Job Details') }}</a>
                    </div>

                    <h3 class="text-lg font-medium text-gray-700">Submission Information</h3>
                    <dl class="mt-2 divide-y divide-gray-200 border-t border-b border-gray-200">
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Title</dt>
                            <dd class="text-gray-900">{{ $submission->title }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Submitted by</dt>
                            <dd class="text-gray-900">{{ $submission->freelancer->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Submitted At</dt>
                            <dd class="text-gray-900">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i A') : 'N/A' }}</dd>
                        </div>
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Status</dt>
                            <dd class="text-gray-900"><x-status-badge :status="$submission->status" /></dd>
                        </div>
                        @if($submission->description)
                        <div class="py-3 text-sm font-medium">
                            <dt class="text-gray-500 mb-1">Description</dt>
                            <dd class="text-gray-900 whitespace-pre-wrap">{{ $submission->description }}</dd>
                        </div>
                        @endif
                        @if($submission->file_path)
                        <div class="py-3 flex justify-between text-sm font-medium">
                            <dt class="text-gray-500">Submitted File</dt>
                            <dd>
                                <a href="{{ route('admin.work-submissions.download', $submission) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $submission->original_filename ?? 'Download File' }} ({{ \App\Helpers\FileHelper::formatBytes($submission->size) }})
                                </a>
                            </dd>
                        </div>
                        @endif
                         @if($submission->admin_remarks)
                        <div class="py-3 text-sm font-medium">
                            <dt class="text-gray-500 mb-1">Admin Remarks</dt>
                            <dd class="text-gray-900 whitespace-pre-wrap">{{ $submission->admin_remarks }}</dd>
                        </div>
                        @endif
                    </dl>

                    <!-- Comments Section -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Comments on this Submission</h4>
                        @if($submission->comments->isEmpty())
                            <p class="text-sm text-gray-500">No comments yet for this submission.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($submission->comments as $comment)
                                    <div class="border rounded-md p-3">
                                        <div class="flex justify-between items-center mb-1">
                                            <p class="font-semibold text-sm text-gray-800">
                                                {{ $comment->user->name ?? ($comment->user_type === 'App\\Models\\Admin' ? 'Admin' : 'User') }}
                                                @if($comment->user_type === 'App\\Models\\Admin' && $comment->user_id === Auth::guard('admin')->id())
                                                    (You)
                                                @elseif($comment->user_type !== 'App\\Models\\Admin' && $comment->user_id === Auth::id())
                                                    (You)
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y H:i A') }}</p>
                                        </div>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment_text }}</p>
                                        @if($comment->screenshot_path)
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($comment->screenshot_path) }}" alt="Screenshot" class="max-w-xs max-h-48 rounded border">
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Add Comment Form -->
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Add Your Comment</h4>
                        <form method="POST" action="{{ route('admin.jobs.comments.store', $submission->jobAssignment->job_id) }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="work_submission_id" value="{{ $submission->id }}">

                            <div>
                                <x-input-label for="comment_content" :value="__('Comment')" />
                                <textarea id="comment_content" name="content" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900" required>{{ old('content') }}</textarea>
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="screenshot" :value="__('Attach Screenshot (Optional)')" />
                                <input type="file" id="screenshot" name="screenshot" class="mt-1 block w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <x-input-error :messages="$errors->get('screenshot')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-primary-button>
                                    {{ __('Post Comment') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    <!-- Admin Actions -->
                    <div class="mt-8 pt-6 border-t">
                        <h4 class="text-lg font-medium text-gray-700 mb-2">Admin Actions</h4>
                        <div class="flex space-x-3">
                            <!-- Form to request revisions -->
                            <form method="POST" action="{{ route('admin.work-submissions.update', $submission) }}" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ \App\Models\WorkSubmission::STATUS_ADMIN_REVISION_REQUESTED }}">
                                <!-- Optionally add a field for admin remarks specific to this action -->
                                <x-secondary-button type="submit" onclick="return confirm('Are you sure you want to request revisions?')">
                                    {{ __('Request Revisions from Freelancer') }}
                                </x-secondary-button>
                            </form>

                            <!-- Form to submit to client -->
                            <form method="POST" action="{{-- route('admin.work-submissions.submit-to-client', $submission) --}}" class="inline-block"> {{-- Placeholder route --}}
                                @csrf
                                <!-- This will likely be a POST to a new method, or reuse update with a specific status -->
                                <input type="hidden" name="status" value="{{ \App\Models\WorkSubmission::STATUS_PENDING_CLIENT_REVIEW }}">
                                <x-primary-button type="submit" onclick="return confirm('Are you sure you want to submit this to the client?')">
                                    {{ __('Submit to Client for Review') }}
                                </x-primary-button>
                            </form>
                        </div>
                         <div class="mt-4">
                             <a href="{{ route('admin.work-submissions.edit', $submission) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('Edit Submission Status/Remarks (Advanced)') }}</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
