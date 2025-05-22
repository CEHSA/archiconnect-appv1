<x-admin-layout>
    <x-slot name="header">
        {{ __('Message Review History') }}
    </x-slot>

    <div class="container mx-auto px-6 py-8">
        <h3 class="text-gray-700 text-3xl font-medium mb-6">{{ __('Message Review History') }}</h3>

        <!-- Filters Section -->
        <div class="mb-8 p-4 bg-white shadow-md rounded-lg">
            <form method="GET" action="{{ route('admin.messages.history') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="user_group" class="block text-sm font-medium text-gray-700">User Group</label>
                    <select name="user_group" id="user_group" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Groups</option>
                        <option value="{{ App\Models\User::ROLE_CLIENT }}" {{ request('user_group') == App\Models\User::ROLE_CLIENT ? 'selected' : '' }}>Client</option>
                        <option value="{{ App\Models\User::ROLE_FREELANCER }}" {{ request('user_group') == App\Models\User::ROLE_FREELANCER ? 'selected' : '' }}>Freelancer</option>
                        <!-- Add other roles if necessary -->
                    </select>
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700">Specific User (Sender of Original Message)</label>
                    <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Users</option>
                        @foreach($usersForFilter as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="admin_id" class="block text-sm font-medium text-gray-700">Reviewing Admin</label>
                    <select name="admin_id" id="admin_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Admins</option>
                        @foreach($adminsForFilter as $admin)
                            <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                </div>
                
                <div class="col-span-1 md:col-span-2 lg:col-span-4 flex items-end space-x-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 transition ease-in-out duration-150">
                        Filter
                    </button>
                    <a href="{{ route('admin.messages.history') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Message Review Activity Log Section -->
        <div class="mt-8">
            <h4 class="text-lg font-medium text-gray-700 mb-2">{{ __('Recent Activity') }}</h4>
            @if($messageActivityLogs && $messageActivityLogs->count() > 0)
            <div id="activity-log-container" class="h-96 overflow-y-auto border border-gray-200 rounded-md shadow"> {{-- Added ID and fixed height for scrolling --}}
                <div class="flex flex-col">
                    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                        <div class="align-middle inline-block min-w-full"> {{-- Removed shadow and border from here --}}
                            <table class="min-w-full">
                                <thead class="sticky top-0 bg-gray-50 z-10"> {{-- Make header sticky --}}
                                    <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Admin') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Action Description') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Related Message') }}
                                    </th>
                                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Timestamp') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach ($messageActivityLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-900">
                                            {{ $log->admin->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm leading-5 text-gray-900">
                                            {{ $log->description }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm leading-5">
                                            @if($log->loggable_type === App\Models\Message::class && $log->loggable)
                                                <a href="{{ route('admin.messages.show', $log->loggable_id) }}" class="text-architimex-primary hover:text-architimex-primary-darker">
                                                    {{ \Illuminate\Support\Str::limit($log->loggable->content, 50) }} (Review ID: {{ $log->loggable_id }})
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                            {{ $log->created_at->diffForHumans() }} ({{ $log->created_at->format('Y-m-d H:i') }})
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                {{ $messageActivityLogs->links() }}
            </div>
            @else
            <p class="text-gray-500 mt-4">{{ __('No message review activities found.') }}</p>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('activity-log-container');
            
            if (container && container.scrollHeight > container.clientHeight) {
                let scrollSpeed = 0.5; // Pixels per animation frame, adjust for speed
                let isHovering = false;
                let animationFrameId = null;

                container.addEventListener('mouseenter', () => {
                    isHovering = true;
                    if (animationFrameId) {
                        cancelAnimationFrame(animationFrameId);
                        animationFrameId = null;
                    }
                });

                container.addEventListener('mouseleave', () => {
                    isHovering = false;
                    if (!animationFrameId) { // Restart animation if it was paused
                        animateScroll();
                    }
                });

                function animateScroll() {
                    if (isHovering) {
                        animationFrameId = null; 
                        return;
                    }

                    container.scrollTop += scrollSpeed;
                    
                    // Check if scrolled to the bottom (with a small tolerance)
                    if (container.scrollTop >= container.scrollHeight - container.clientHeight - 1) {
                        container.scrollTop = 0; // Reset to top
                    }
                    animationFrameId = requestAnimationFrame(animateScroll);
                }
                animateScroll(); // Start the animation
            }
        });
    </script>
    @endpush
</x-admin-layout>
