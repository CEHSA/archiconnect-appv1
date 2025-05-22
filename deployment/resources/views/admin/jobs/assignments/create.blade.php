<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Freelancer to Job:') }} {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-architimex-sidebar">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.job-assignments.store') }}">
                        @csrf
                        <input type="hidden" name="job_id" value="{{ $job->id }}">

                        <!-- Freelancer Selection -->
                        <div class="mt-4">
                            <x-input-label for="freelancer_id" :value="__('Select Freelancer')" class="text-gray-700" />
                            <select id="freelancer_id" name="freelancer_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900" required>
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
                            <x-input-label for="admin_remarks" :value="__('Admin Remarks (Optional)')" class="text-gray-700" />
                            <textarea id="admin_remarks" name="admin_remarks" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900 placeholder-gray-700" placeholder="Enter remarks here...">{{ old('admin_remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('admin_remarks')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.jobs.show', $job) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-4">
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
</x-admin-layout>
