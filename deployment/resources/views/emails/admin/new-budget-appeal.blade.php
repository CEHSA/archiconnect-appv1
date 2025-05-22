<x-mail::message>
# New Budget Appeal Submitted

A new budget appeal has been submitted by {{ $budgetAppeal->freelancer->name }} for the job "{{ $budgetAppeal->jobAssignment->job->title }}".

**Details:**
- **Current Budget:** ${{ number_format($budgetAppeal->current_budget, 2) }}
- **Requested Budget:** ${{ number_format($budgetAppeal->requested_budget, 2) }}
- **Reason:**
{{ $budgetAppeal->reason }}

@if ($budgetAppeal->evidence_path)
Evidence file attached.
@endif

Review the appeal and take appropriate action.

<x-mail::button :url="route('admin.budget-appeals.show', $budgetAppeal)">
View Budget Appeal
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
