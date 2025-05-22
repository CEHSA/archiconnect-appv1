<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Current Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8"> {{-- Changed to max-w-full for wider table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('List of Current Active Jobs') }}</h3>
                        <a href="{{ route('admin.jobs.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-xs uppercase tracking-widest">
                            &larr; {{ __('Back to All Jobs') }}
                        </a>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.jobs.current') }}" class="mb-6 bg-gray-50 p-4 rounded-lg shadow">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700">{{ __('Client') }}</label>
                                <select name="client_id" id="client_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('All Clients') }}</option>
                                    @foreach($clients as $id => $name)
                                        <option value="{{ $id }}" {{ $request->get('client_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="assigned_freelancer_id" class="block text-sm font-medium text-gray-700">{{ __('Assigned Freelancer') }}</label>
                                <select name="assigned_freelancer_id" id="assigned_freelancer_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('Any Freelancer') }}</option>
                                    @foreach($freelancers as $id => $name)
                                        <option value="{{ $id }}" {{ $request->get('assigned_freelancer_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.jobs.current') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-cyan-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Job Title') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Client') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Assigned Freelancer') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Progress') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($jobs as $job)
                                    @php
                                        // Determine progress - assumes JobAssignment model has effective_progress accessor
                                        // and that a job has one primary assignment for progress display here.
                                        // This might need refinement based on how assignments are structured.
                                        $mainAssignment = $job->assignments->first(); // Or a specific one if multiple
                                        $progress = $mainAssignment ? $mainAssignment->effective_progress : 0;
                                        if ($job->status === 'completed') $progress = 100;
                                        if ($job->status === 'cancelled') $progress = 0; // Or handle differently
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $job->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $job->user->name ?? __('N/A') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $job->assignedFreelancer->name ?? ($mainAssignment->freelancer->name ?? __('N/A')) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm"><x-status-badge :status="$job->status" /></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs">{{ $progress }}%</span>
                                            {{-- Admin override button could go here --}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.jobs.show', $job) }}" class="text-blue-600 hover:text-blue-700 mr-2">{{ __('Details') }}</a>
                                            {{-- Potentially link to edit progress override --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No current jobs found matching your criteria.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $jobs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
