<x-mail::message>
# You've Been Assigned to a New Job!

Hello,

You have been assigned to the following job on Archiconnect:

**Job Title:** {{ $jobTitle }}

**Job Description:**
<x-mail::panel>
{{ $jobDescription }}
</x-mail::panel>

@if ($adminRemarks)
**Admin Remarks:**
<x-mail::panel>
{{ $adminRemarks }}
</x-mail::panel>
@endif

Please log in to your dashboard to view more details and manage this assignment.
You may be required to accept or decline this assignment.

<x-mail::button :url="$jobUrl">
View Job Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
