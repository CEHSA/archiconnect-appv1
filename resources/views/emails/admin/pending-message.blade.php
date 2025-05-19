<x-mail::message>
# New Pending Message for Review

A new message has been submitted by a freelancer that requires your review.

**Sender:** {{ $message->user->name }} ({{ $message->user->email }})
**Conversation/Job:** {{ $message->conversation->title ?? 'N/A' }}
**Received At:** {{ $message->created_at }}

**Message Content:**
{{ $message->content }}

<x-mail::button :url="route('admin.messages.show', $message)">
Review Message
</x-mail::button>

Please review this message in the admin panel to approve or reject it.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
