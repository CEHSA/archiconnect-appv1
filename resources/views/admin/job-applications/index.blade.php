<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Applications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('admin.job-applications.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="job_filter" :value="__('Job')" />
                                <select id="job_filter" name="job_id" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">
                                    <option value="">All Jobs</option>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }} (ID: {{ $job->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="freelancer_filter" :value="__('Freelancer')" />
                                <select id="freelancer_filter" name="freelancer_id" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">
                                    <option value="">All Freelancers</option>
                                    @foreach($freelancers as $freelancer)
                                        <option value="{{ $freelancer->id }}" {{ request('freelancer_id') == $freelancer->id ? 'selected' : '' }}>{{ $freelancer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="status_filter" :value="__('Status')" />
                                <select id="status_filter" name="status" class="mt-1 block w-full border-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm">
                                    <option value="">All Statuses</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                    <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="accepted_for_assignment" {{ request('status') == 'accepted_for_assignment' ? 'selected' : '' }}>Accepted for Assignment</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <x-primary-button class="bg-cyan-700 hover:bg-cyan-600">{{ __('Filter') }}</x-primary-button>
                                <a href="{{ route('admin.job-applications.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-900 underline">{{ __('Clear') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-cyan-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Job Title') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Freelancer') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Submitted') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($applications as $application)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.jobs.show', $application->job_id) }}" class="text-cyan-600 hover:text-cyan-800">
                                                {{ $application->job->title ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $application->freelancer->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $application->submitted_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <x-status-badge :status="$application->status" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.job-applications.show', $application) }}" class="text-cyan-600 hover:text-cyan-800">{{ __('View') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No job applications found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
