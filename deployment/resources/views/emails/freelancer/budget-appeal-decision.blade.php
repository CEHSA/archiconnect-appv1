<x-mail::message>
# Budget Appeal Decision

Your budget appeal for the job "{{ $budgetAppeal->jobAssignment->job->title }}" has been reviewed.

**Details:**
- **Requested Budget:** ${{ number_format($budgetAppeal->requested_budget, 2) }}
- **Status:** {{ Str::title(str_replace('_', ' ', $budgetAppeal->status)) }}

@if ($budgetAppeal->admin_remarks)
**Admin Remarks:**
{{ $budgetAppeal->admin_remarks }}
@endif

@if ($budgetAppeal->client_remarks)
**Client Remarks:**
{{ $budgetAppeal->client_remarks }}
@endif

@if ($budgetAppeal->status === 'approved')
The budget for this assignment has been updated to ${{ number_format($budgetAppeal->requested_budget, 2) }}.
<x-mail::button :url="route('freelancer.assignments.show', $budgetAppeal->jobAssignment)">
View Assignment
</x-mail::button>
@elseif ($budgetAppeal->status === 'rejected')
Your budget appeal was not approved. The budget for this assignment remains ${{ number_format($budgetAppeal->current_budget, 2) }}.
<x-mail::button :url="route('freelancer.assignments.show', $budgetAppeal->jobAssignment)">
View Assignment
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
