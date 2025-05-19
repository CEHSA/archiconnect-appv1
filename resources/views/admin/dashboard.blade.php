{{-- resources/views/admin/dashboard.blade.php --}}
{{-- Use the 'layouts.admin' layout.
     Set the header slot to "Admin Dashboard".
     Create summary cards for: Total Projects, Active Projects, Completed Projects, Total Hours Logged, using the 'stats' variable.
     Style cards with a white background, shadow, padding, and an icon (use heroicons or fontawesome if installed, else text).
     Create two sections below: "Recent Jobs" and "Recent Activity", displaying data from '$recentJobs' and '$recentActivity'.
     Style these sections as cards or lists as in the image.
--}}
<x-layouts.admin>
    <x-slot name="header">
        {{ __('Admin Dashboard') }}
    </x-slot>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Projects Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-blue-500 bg-opacity-20 rounded-full">
                {{-- Heroicon: collection --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Projects</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['totalProjects'] }}</p>
            </div>
        </div>
        <!-- Active Projects Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-yellow-500 bg-opacity-20 rounded-full">
                {{-- Heroicon: clock --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Active Projects</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['activeProjects'] }}</p>
            </div>
        </div>
        <!-- Completed Projects Card -->
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
             <div class="p-3 bg-green-500 bg-opacity-20 rounded-full">
                {{-- Heroicon: check-circle --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Completed Projects</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $stats['completedProjects'] }}</p>
            </div>
        </div>
        <!-- Total Hours Logged Card -->
         <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-indigo-500 bg-opacity-20 rounded-full">
                {{-- Heroicon: chart-bar --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Hours Logged</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($stats['totalHoursLogged'], 1) }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Jobs and Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Jobs -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Jobs</h3>
            <div class="space-y-4">
                @forelse ($recentJobs as $job)
                    <div class="p-4 border rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center">
                            <p class="font-medium text-gray-700">{{ $job['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $job['date'] }}</p>
                        </div>
                        <p class="text-sm text-gray-500 capitalize">Status: {{ $job['status'] }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No recent jobs.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
             <div class="space-y-4">
                @forelse ($recentActivity as $activity)
                    <div class="p-4 border rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center">
                            <p class="font-medium text-gray-700">{{ $activity['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['date'] }}</p>
                        </div>
                        <p class="text-sm text-gray-500">{{ $activity['status'] }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No recent activity.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
