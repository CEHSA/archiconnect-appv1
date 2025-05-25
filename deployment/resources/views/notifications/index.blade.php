@php
    $layoutComponent = 'layouts.app'; // Default layout
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'freelancer') {
            $layoutComponent = 'layouts.freelancer';
        } elseif ($role === 'client') {
            $layoutComponent = 'layouts.client';
        } elseif ($role === 'admin') {
            // Assuming admin notifications are viewed within the admin layout
            // If admin has a separate top-level notification page, adjust this
            $layoutComponent = 'layouts.admin'; 
        }
    }
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="mb-4 flex justify-end">
                        @if ($notifications->isNotEmpty())
                            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                @csrf
                                <x-primary-button>
                                    {{ __('Mark All As Read') }}
                                </x-primary-button>
                            </form>
                        @endif
                    </div>

                    <h3 class="text-lg font-semibold mb-2">Unread Notifications</h3>
                    @if ($notifications->isEmpty())
                        <p>You have no unread notifications.</p>
                    @else
                        <ul class="space-y-4">
                            @foreach ($notifications as $notification)
                                <li class="p-4 bg-gray-100 rounded-md shadow">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <!-- Basic notification data display -->
                                            <p class="font-semibold">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                            <p>{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                                            @if(isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}" class="text-sm text-blue-600 hover:underline">View Details</a>
                                            @endif
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                                            @csrf
                                            <x-secondary-button type="submit">
                                                Mark as Read
                                            </x-secondary-button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <h3 class="text-lg font-semibold mt-8 mb-2">Read Notifications</h3>
                    @if ($readNotifications->isEmpty())
                        <p>You have no read notifications.</p>
                    @else
                        <ul class="space-y-4">
                            @foreach ($readNotifications as $notification)
                                <li class="p-4 bg-gray-200 rounded-md shadow">
                                    <p class="font-semibold">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p>{{ $notification->data['message'] ?? 'Notification content.' }}</p>
                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" class="text-sm text-blue-600 hover:underline">View Details</a>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-1">
                                        Read: {{ $notification->read_at->diffForHumans() }} (Received: {{ $notification->created_at->diffForHumans() }})
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
