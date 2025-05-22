<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ confirmJobDeletion: false, jobIdToDelete: null }">
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
                                                    @click.prevent="confirmJobDeletion = true; jobIdToDelete = {{ $job->id }}"
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
    <x-modal name="confirm-job-deletion" :show="false" x-show="confirmJobDeletion"
        @close.stop="confirmJobDeletion = false" focusable>
        <form method="post" @submit.prevent="$refs['deleteJobForm' + jobIdToDelete].submit()" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete this job?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once the job is deleted, all of its resources and data will be permanently deleted.') }}
                {{-- Password confirmation is typically needed for sensitive actions like deletion --}}
            </p>

            {{-- Password confirmation input (optional but recommended for sensitive actions) --}}
            {{-- <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div> --}}

            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="confirmJobDeletion = false">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Job') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
    </x-app-layout>
