<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ jobIdToDelete: null }" @delete-confirmed.window="if(jobIdToDelete) { $refs['deleteJobForm' + jobIdToDelete].submit(); } jobIdToDelete = null; $dispatch('close-modal', 'confirm-job-deletion')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('admin.dashboard') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded text-xs uppercase tracking-widest">
                            &larr; {{ __('Back to Dashboard') }}
                        </a>
                        <a href="{{ route('admin.jobs.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-cyan-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Create New Job') }}
                        </a>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('admin.jobs.index') }}" class="mb-6 bg-gray-50 p-4 rounded-lg shadow">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    @foreach($statuses as $statusValue)
                                        <option value="{{ $statusValue }}" {{ $request->get('status') == $statusValue ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $statusValue)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
                                <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('Date From') }}</label>
                                <input type="date" name="date_from" id="date_from" value="{{ $request->get('date_from') }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('Date To') }}</label>
                                <input type="date" name="date_to" id="date_to" value="{{ $request->get('date_to') }}" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </div>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Filter') }}
                            </button>
                            <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Clear') }}
                            </a>
                        </div>
                    </form>

                    <!-- Session Messages -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-cyan-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Title') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Client') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Budget') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Hourly Rate') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Created By') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($jobs as $job)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $job->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $job->user->name ?? __('N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <x-status-badge :status="$job->status" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ \App\Helpers\CurrencyHelper::formatZAR($job->budget) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ \App\Helpers\CurrencyHelper::formatZAR($job->hourly_rate) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $job->createdByAdmin->name ?? __('N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.jobs.show', $job) }}"
                                                class="text-blue-600 hover:text-blue-700 mr-2">{{ __('View') }}</a>
                                            <a href="{{ route('admin.jobs.edit', $job) }}"
                                                class="text-blue-600 hover:text-blue-700 mr-2">{{ __('Edit') }}</a>
                                            <form x-ref="deleteJobForm{{ $job->id }}"
                                                action="{{ route('admin.jobs.destroy', $job) }}" method="POST"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    @click.prevent="$dispatch('open-modal', 'confirm-job-deletion'); jobIdToDelete = {{ $job->id }}"
                                                    class="text-red-600 hover:text-red-700">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No jobs found.') }}
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
    {{-- The modal's visibility is now controlled by its internal state, triggered by events --}}
    <x-modal name="confirm-job-deletion" :show="false" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete this job?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once the job is deleted, all of its resources and data will be permanently deleted.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="$dispatch('close-modal', 'confirm-job-deletion')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" @click="$dispatch('delete-confirmed')">
                    {{ __('Delete Job') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</x-admin-layout>
