<x-mail::message>
# New Task Progress Update

A freelancer has submitted a new task progress update for a job assignment.

**Job Title:** {{ $taskProgress->jobAssignment->job->title }}
**Freelancer:** {{ $taskProgress->freelancer->name }}
**Submitted At:** {{ $taskProgress->submitted_at->format('M d, Y H:i') }}

**Progress Description:**
{{ $taskProgress->description }}

@if($taskProgress->file_path)
A file has been attached to this update.
@endif

<x-mail::button :url="route('admin.jobs.assignments.show', $taskProgress->jobAssignment->id)">
View Assignment Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
