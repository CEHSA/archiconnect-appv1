{{-- resources/views/freelancer/dashboard.blade.php --}}
{{-- Use the 'layouts.freelancer' layout.
     Set the header slot to "Freelancer Dashboard".
     Display summary cards: "Assigned Jobs", "Tasks Due Today", "Hours Logged This Week" (dummy data).
     Display a list of "My Active Jobs" (dummy data).
     Style consistently.
--}}
<x-layouts.freelancer>
    <x-slot name="header">
        {{ __('Freelancer Dashboard') }}
    </x-slot>

     @php
        // Dummy data for freelancer dashboard
        $freelancerStats = [
            'assignedJobs' => 3,
            'tasksDueToday' => 1,
            'hoursThisWeek' => 22.5,
        ];
        $activeFreelancerJobs = [
            ['name' => 'New Patio Design - Phase 1', 'client' => 'Client B', 'deadline' => '07/05/2023'],
            ['name' => 'Modern Residential - Kitchen Details', 'client' => 'Client A', 'deadline' => '07/10/2023'],
        ];
    @endphp

    <!-- Freelancer Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-purple-500 bg-opacity-20 rounded-full"> {{-- Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 13h4m-4-4h4" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Assigned Jobs</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $freelancerStats['assignedJobs'] }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-red-500 bg-opacity-20 rounded-full"> {{-- Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Tasks Due Today</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $freelancerStats['tasksDueToday'] }}</p>
            </div>
        </div>
         <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-teal-500 bg-opacity-20 rounded-full"> {{-- Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Hours Logged (Week)</p>
                <p class="text-2xl font-semibold text-gray-800">{{ number_format($freelancerStats['hoursThisWeek'],1) }}</p>
            </div>
        </div>
    </div>

    <!-- My Active Jobs -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">My Active Jobs</h3>
        <div class="space-y-4">
             @forelse ($activeFreelancerJobs as $job)
                <div class="p-4 border rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-center">
                        <p class="font-medium text-gray-700">{{ $job['name'] }}</p>
                        <p class="text-xs text-gray-500">Client: {{ $job['client'] }}</p>
                    </div>
                    <p class="text-sm text-red-500">Deadline: {{ $job['deadline'] }}</p>
                </div>
            @empty
                <p class="text-gray-500">You have no active jobs assigned.</p>
            @endforelse
        </div>
    </div>
</x-layouts.freelancer>
