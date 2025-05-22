<x-mail::message>
# Payment Processed

Hello,

This email is to inform you that a payment has been processed for your work on the job "{{ $payment->jobAssignment->job->title }}".

**Payment Details:**
- **Amount:** R{{ number_format($payment->amount, 2) }}
- **Payment Date:** {{ $payment->payment_date->format('Y-m-d') }}
- **Status:** {{ ucfirst($payment->status) }}
- **Transaction ID:** {{ $payment->transaction_id ?? 'N/A' }}
- **Notes:** {{ $payment->notes ?? 'N/A' }}

You can view the details of this payment and the associated job assignment on the platform:

<x-mail::button :url="route('freelancer.assignments.show', $payment->jobAssignment->id)">
View Job Assignment
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
