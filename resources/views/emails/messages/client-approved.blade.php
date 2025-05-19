@component('mail::message')
# Message Regarding Your Job: {{ $jobTitle }}

Hello,

A message from **{{ $freelancerName }}** regarding your job, **"{{ $jobTitle }}"**, has been reviewed and approved by our admin team.

**Message Content:**
@component('mail::panel')
{{ $messageContent }}
@endcomponent

You can view the full conversation and manage your job by logging into your dashboard.

@component('mail::button', ['url' => $viewConversationUrl])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
