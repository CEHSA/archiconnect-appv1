@component('mail::message')
# Job Completed: {{ $job->title }}

Your work on "{{ $job->title }}" has been marked as completed by the client.

@component('mail::button', ['url' => route('freelancer.jobs.show', $job)])
View Job Details
@endcomponent

Thanks for your work,<br>
{{ config('app.name') }}
@endcomponent
