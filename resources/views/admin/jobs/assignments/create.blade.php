<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Assign Freelancer to Job:') }} {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.jobs.assignments.store', $job) }}">
                        @csrf

                        <!-- Freelancer Selection -->
                        <div class="mt-4">
                            <x-input-label for="freelancer_id" :value="__('Select Freelancer')" />
                            <select id="freelancer_id" name="freelancer_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="" disabled selected>{{ __('Choose a freelancer...') }}</option>
                                @foreach ($freelancers as $freelancer)
                                    <option value="{{ $freelancer->id }}" {{ in_array($freelancer->id, $assignedFreelancerIds) ? 'disabled' : '' }}>
                                        {{ $freelancer->name }} ({{ $freelancer->email }})
                                        {{ in_array($freelancer->id, $assignedFreelancerIds) ? ' - Already Assigned' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('freelancer_id')" class="mt-2" />
                        </div>

                        <!-- Admin Remarks -->
                        <div class="mt-4">
                            <x-input-label for="admin_remarks" :value="__('Admin Remarks (Optional)')" />
                            <textarea id="admin_remarks" name="admin_remarks" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('admin_remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_remarks')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.jobs.assignments.index', $job) }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Assign Freelancer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
