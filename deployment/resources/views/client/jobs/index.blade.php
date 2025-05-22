<x-layouts.client>
    <x-slot name="header">
        {{ __('My Jobs') }}
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('client.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('Post New Job') }}
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg">

        @if($jobs->count())
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Your Jobs') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Title') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Budget') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Posted On') }}
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">{{ __('Actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($jobs as $job)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $job->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <x-status-badge :status="$job->status" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $job->budget ? '$' . number_format($job->budget, 2) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $job->created_at->toFormattedDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('client.jobs.show', $job) }}" class="text-architimex-primary hover:text-architimex-primary-darker mr-2 transition-colors">{{ __('View') }}</a>
                                    <a href="{{ route('client.jobs.edit', $job) }}" class="text-architimex-primary hover:text-architimex-primary-darker mr-2 transition-colors">{{ __('Edit') }}</a>
                                    <form action="{{ route('client.jobs.destroy', $job) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">{{ __("You haven't posted any jobs yet.") }}</p>
                <a href="{{ route('client.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-architimex-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Create Your First Job') }}
                </a>
            </div>
        @endif
    </div>
</x-layouts.client>
