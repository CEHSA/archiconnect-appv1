<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Assignment for Job:') }} {{ $assignment->job?->title ?? __('Job Not Found') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-architimex-sidebar">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('admin.job-assignments.update', $assignment) }}">
                        @csrf
                        @method('PUT')

                        <!-- Freelancer (Readonly) -->
                        <div class="mt-4">
                            <x-input-label for="freelancer_name" :value="__('Freelancer')" class="text-gray-700" />
                            <x-text-input id="freelancer_name" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" type="text" name="freelancer_name" :value="$assignment->freelancer->name ?? 'N/A'" readonly />
                        </div>

                        <!-- Status Selection -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status')" class="text-gray-700" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $assignment->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Admin Remarks -->
                        <div class="mt-4">
                            <x-input-label for="admin_remarks" :value="__('Admin Remarks')" class="text-gray-700" />
                            <textarea id="admin_remarks" name="admin_remarks" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm placeholder-gray-500" placeholder="Enter admin remarks...">{{ old('admin_remarks', $assignment->admin_remarks) }}</textarea>
                            <x-input-error :messages="$errors->get('admin_remarks')" class="mt-2" />
                        </div>

                        <!-- Freelancer Remarks (Potentially Readonly or Editable by Admin) -->
                        <div class="mt-4">
                            <x-input-label for="freelancer_remarks" :value="__('Freelancer Remarks')" class="text-gray-700" />
                            <textarea id="freelancer_remarks" name="freelancer_remarks" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm placeholder-gray-500" placeholder="Enter freelancer remarks...">{{ old('freelancer_remarks', $assignment->freelancer_remarks) }}</textarea>
                            <x-input-error :messages="$errors->get('freelancer_remarks')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.job-assignments.show', $assignment) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Update Assignment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
