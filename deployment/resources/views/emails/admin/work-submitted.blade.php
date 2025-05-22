<x-mail::message>
# New Work Submitted

Hello Admin,

A new piece of work has been submitted by **{{ $freelancerName }}** for the job: **{{ $jobTitle }}**.

**Submission Title:** {{ $submissionTitle }}

You can review the submission by clicking the button below:

<x-mail::button :url="$submissionUrl">
View Submission
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
