<x-mail::message>
# Work Submission Reviewed

Hello {{ $workSubmission->jobAssignment->freelancer->name }},

Your work submission for the job **"{{ $jobTitle }}"** has been reviewed by the admin.

**Status:** {{ ucfirst($status) }}

@if($adminRemarks)
**Admin Remarks:**
{{ $adminRemarks }}
@endif

You can view the submission details here:
<x-mail::button :url="route('freelancer.assignments.show', $workSubmission->jobAssignment)">
View Assignment
</x-mail::button>

Thanks,
{{ config('app.name') }}
</x-mail::message>
