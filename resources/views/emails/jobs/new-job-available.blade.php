<x-mail::message>
# New Job Opportunity: {{ $jobTitle }}

Hello {{ $freelancerName }},

A new job, **"{{ $jobTitle }}"**, has been posted on {{ config('app.name') }} that you might be interested in.

**Description:**
{{ $jobDescription }}

You can view more details and express your interest by clicking the button below:

<x-mail::button :url="$jobUrl">
View Job Details
</x-mail::button>

If you are not interested in this opportunity, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
