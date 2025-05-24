<x-admin-layout>
    <x-slot name="header">
        {{ __('Current Jobs') }}
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.jobs.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-xs uppercase tracking-widest">
                    {{ __('All Jobs') }}
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.jobs.current') }}" class="mb-6 bg-gray-50 p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Client Filter -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700">{{ __('Client') }}</label>
                    <select id="client_id" name="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">{{ __('All Clients') }}</option>
                        @foreach($clients as $id => $name)
                            <option value="{{ $id }}" {{ request('client_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Freelancer Filter -->
                <div>
                    <label for="assigned_freelancer_id" class="block text-sm font-medium text-gray-700">{{ __('Freelancer') }}</label>
                    <select id="assigned_freelancer_id" name="assigned_freelancer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">{{ __('All Freelancers') }}</option>
                        @foreach($freelancers as $id => $name)
                            <option value="{{ $id }}" {{ request('assigned_freelancer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded-md hover:bg-cyan-700">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.jobs.current') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        {{ __('Clear') }}
                    </a>
                </div>
            </div>
        </form>

        <!-- Jobs List -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Title') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Client') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Freelancer') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Progress') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($jobs as $job)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.jobs.show', $job) }}" class="hover:text-cyan-600">
                                            {{ $job->title }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $job->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($job->assignments->isNotEmpty())
                                        <div class="text-sm text-gray-900">
                                            {{ $job->assignments->first()->freelancer->name }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">{{ __('Not Assigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($job->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($job->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($job->status === 'completed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($job->assignments->isNotEmpty())
                                        @php
                                            $assignment = $job->assignments->first();
                                            $progress = $assignment->tasks->count() > 0 
                                                ? round(($assignment->tasks->where('status', 'completed')->count() / $assignment->tasks->count()) * 100) 
                                                : 0;
                                        @endphp
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-cyan-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1">{{ $progress }}%</span>
                                    @else
                                        <span class="text-sm text-gray-500">{{ __('N/A') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="text-cyan-600 hover:text-cyan-900">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ __('No current jobs found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
