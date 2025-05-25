@component('mail::message')
# Job Completed: {{ $job->title }}

The job "{{ $job->title }}" has been marked as completed by the freelancer.

@component('mail::button', ['url' => route('jobs.show', $job)])
View Job
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
