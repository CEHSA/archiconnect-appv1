<x-layouts.freelancer>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Message to Administrator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('freelancer.messages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- We need a way to select an admin or have a default. For now, assume first admin. --}}
                        {{-- In a real scenario, you might have a dropdown of admins or a general support inbox. --}}
                        @php
                            $adminUser = \App\Models\Admin::first(); // Assuming sending to the first admin for now
                        @endphp
                        @if($adminUser)
                            <input type="hidden" name="admin_recipient_id" value="{{ $adminUser->id }}">
                        @else
                            <p class="text-red-500">No administrator found to send the message to. Please contact support.</p>
                        @endif

                        <div x-data="{ selectedAssignment: '{{ old('job_assignment_id') }}', tasks: [], allAssignments: {{ json_encode($assignmentOptions) }} }" 
                             x-init="tasks = allAssignments.find(a => a.id == selectedAssignment)?.tasks || []">

                            <!-- Job Assignment Dropdown -->
                            <div class="mb-4">
                                <x-input-label for="job_assignment_id" :value="__('Relating to Job Assignment (Optional)')" />
                                <select name="job_assignment_id" id="job_assignment_id" 
                                        x-model="selectedAssignment"
                                        @change="tasks = allAssignments.find(a => a.id == selectedAssignment)?.tasks || []"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- General Message (No Specific Assignment) --</option>
                                    @foreach($assignmentOptions as $assignment)
                                        <option value="{{ $assignment['id'] }}" {{ old('job_assignment_id') == $assignment['id'] ? 'selected' : '' }}>
                                            {{ $assignment['job_title'] }} (ID: {{ $assignment['id'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('job_assignment_id')" class="mt-2" />
                            </div>

                            <!-- Task Dropdown (Visible if an assignment is selected and has tasks) -->
                            <div class="mb-4" x-show="selectedAssignment && tasks.length > 0">
                                <x-input-label for="assignment_task_id" :value="__('Relating to Task (Optional)')" />
                                <select name="assignment_task_id" id="assignment_task_id"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- General to Assignment (No Specific Task) --</option>
                                    <template x-for="task in tasks" :key="task.id">
                                        <option :value="task.id" x-text="task.title"></option>
                                    </template>
                                </select>
                                <x-input-error :messages="$errors->get('assignment_task_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="subject" :value="__('Subject')" />
                            <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject')" required autofocus />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Message')" />
                            <x-textarea-input id="content" name="content" rows="5" class="mt-1 block w-full" required>{{ old('content') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="attachments" :value="__('Attachments (optional)')" />
                            <input id="attachments" name="attachments[]" type="file" multiple class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                            <p class="mt-1 text-xs text-gray-500">Max 5 files, each up to 5MB.</p>
                            <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                            <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('freelancer.messages.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Send Message') }}
                            </x-primary-button>
                        </div>
                         <p class="mt-2 text-xs text-gray-500 text-right">
                            Note: Your message will be reviewed by an admin.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
