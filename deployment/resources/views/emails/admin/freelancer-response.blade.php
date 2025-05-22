@component('mail::message')
# Freelancer Responded to Job Assignment

Hello Admin,

The freelancer **{{ $freelancerName }}** has responded to the job assignment for: **{{ $jobTitle }}**.

**Response Status:** {{ ucfirst($status) }}

@if(isset($assignment->remarks) && $assignment->remarks)
**Remarks:**
{{ $assignment->remarks }}
@endif

You can view the details of this assignment and the freelancer's response by clicking the button below:

@component('mail::button', ['url' => $assignmentUrl])
View Assignment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
