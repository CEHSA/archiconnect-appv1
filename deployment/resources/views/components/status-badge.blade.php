@props(['status', 'text' => null])

@php
    $status = strtolower($status ?? '');
    $displayText = $text ?? str_replace('_', ' ', $status);

    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize';
    $statusClasses = '';

    switch ($status) {
        case 'pending':
        case 'pending_review':
        case 'unpaid':
        case 'submitted':
            $statusClasses = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
            break;
        case 'under_review':
        case 'in_progress':
        case 'open':
        case 'assigned':
            $statusClasses = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
            break;
        case 'approved':
        case 'completed':
        case 'active':
        case 'paid':
            $statusClasses = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
            break;
        case 'rejected':
        case 'cancelled':
        case 'escalated':
            $statusClasses = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
            break;
        case 'closed':
        case 'inactive':
        case 'draft':
            $statusClasses = 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            break;
        case 'requires_action':
        case 'action_required': // Alias
        case 'on_hold':
            $statusClasses = 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300';
            if ($status === 'action_required') $displayText = $text ?? 'Requires Action'; // Normalize display text
            break;
        default:
            $statusClasses = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
            $displayText = $text ?? $status; // Display the raw status if unknown
            break;
    }
@endphp

<span class="{{ $baseClasses }} {{ $statusClasses }}">
    {{ $displayText }}
</span>
