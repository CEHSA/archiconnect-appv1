<x-mail::message>
# Job Completed: {{ $jobAssignment->job->title }}

Hello,

This email is to inform you that the job "{{ $jobAssignment->job->title }}" has been marked as completed.

**Job Details:**
- **Title:** {{ $jobAssignment->job->title }}
- **Description:** {{ $jobAssignment->job->description }}

You can view the details of the completed job assignment on the platform:

@if(auth()->user()->hasRole('client'))
<x-mail::button :url="route('client.work-submissions.show', $jobAssignment->latestWorkSubmission->id)">
View Completed Job
</x-mail::button>
@elseif(auth()->user()->hasRole('freelancer'))
<x-mail::button :url="route('freelancer.assignments.show', $jobAssignment->id)">
View Completed Job
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
