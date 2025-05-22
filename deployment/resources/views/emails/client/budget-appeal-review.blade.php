<x-mail::message>
# Budget Appeal for Your Review

A budget appeal has been submitted by {{ $budgetAppeal->freelancer->name }} for the job "{{ $budgetAppeal->jobAssignment->job->title }}".

The freelancer is requesting an increase to the not-to-exceed budget.

**Details:**
- **Current Budget:** ${{ number_format($budgetAppeal->current_budget, 2) }}
- **Requested Budget:** ${{ number_format($budgetAppeal->requested_budget, 2) }}
- **Reason:**
{{ $budgetAppeal->reason }}

@if ($budgetAppeal->evidence_path)
Evidence file is available for review by the admin. Please contact the admin if you wish to view it.
@endif

Please review this appeal and communicate your decision (approve or reject) to the admin.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
