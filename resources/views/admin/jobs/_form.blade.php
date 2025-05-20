@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Job Title -->
    <div>
        <x-input-label for="title" :value="__('Job Title')" />
        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $job->title ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <!-- Client Assignment (User ID) -->
    <div>
        <x-input-label for="user_id" :value="__('Assign to Client (Optional)')" />
        <select name="user_id" id="user_id"
            class="block mt-1 w-full border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">{{ __('Select a Client') }}</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ old('user_id', $job->user_id ?? '') == $client->id ? 'selected' : '' }}>
                    {{ $client->name }} ({{ $client->email }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
    </div> <!-- Job Description -->
    <div class="md:col-span-2">
        <x-input-label for="editor-container" :value="__('Job Description')" />
        <div id="editor-container" class="mt-1 h-64 border border-gray-300 rounded-md"></div>
        <textarea id="description" name="description"
            class="hidden">{{ old('description', $job->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <!-- Budget -->
    <div>
        <x-input-label for="budget" :value="__('Budget (Optional)')" />
        <x-text-input id="budget" class="block mt-1 w-full" type="number" name="budget" :value="old('budget', $job->budget ?? '')" step="0.01" />
        <x-input-error :messages="$errors->get('budget')" class="mt-2" />
    </div>

    <!-- Hourly Rate -->
    <div>
        <x-input-label for="hourly_rate" :value="__('Hourly Rate (Optional)')" />
        <x-text-input id="hourly_rate" class="block mt-1 w-full" type="number" name="hourly_rate"
            :value="old('hourly_rate', $job->hourly_rate ?? '')" step="0.01" />
        <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
    </div>

    <!-- Not-to-Exceed Budget -->
    <div>
        <x-input-label for="not_to_exceed_budget" :value="__('Not-to-Exceed Budget (Optional)')" />
        <x-text-input id="not_to_exceed_budget" class="block mt-1 w-full" type="number" name="not_to_exceed_budget"
            :value="old('not_to_exceed_budget', $job->not_to_exceed_budget ?? '')" step="0.01" />
        <x-input-error :messages="$errors->get('not_to_exceed_budget')" class="mt-2" />
    </div>

    <!-- Skills Required -->
    <div>
        <x-input-label for="skills_required" :value="__('Skills Required (Optional, comma-separated)')" />
        <x-text-input id="skills_required" class="block mt-1 w-full" type="text" name="skills_required"
            :value="old('skills_required', $job->skills_required ?? '')" />
        <x-input-error :messages="$errors->get('skills_required')" class="mt-2" />
    </div> <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select name="status" id="status"
            class="block mt-1 w-full border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @php
                $statuses = ['pending', 'open', 'in_progress', 'submitted', 'under_review', 'approved', 'completed', 'on_hold', 'cancelled', 'closed'];
            @endphp
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ old('status', $job->status ?? 'pending') == $status ? 'selected' : '' }}>
                    {{ __(ucfirst(str_replace('_', ' ', $status))) }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-container {
            min-height: 10rem;
            height: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .ql-editor {
            height: 100%;
            overflow-y: auto;
            width: 100%;
        }

        #editor-container {
            height: 375px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                console.log('Initializing Quill editor...');
                var quill = new Quill('#editor-container', {
                    theme: 'snow',
                    placeholder: 'Write job description here...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'header': 1 }, { 'header': 2 }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'indent': '-1' }, { 'indent': '+1' }],
                            ['clean']
                        ]
                    }
                });
                console.log('Quill editor initialized.');

                var descriptionInput = document.querySelector('#description');
                if (!descriptionInput) {
                    console.error('Description textarea #description not found!');
                    return;
                }

                // Set initial content if exists
                if (descriptionInput.value) {
                    console.log('Setting initial Quill content from textarea.');
                    quill.root.innerHTML = descriptionInput.value;
                }

                // Try to get the form by ID from create.blade.php or edit.blade.php
                var form = document.getElementById('createJobForm') || document.getElementById('editJobForm');

                if (form) {
                    console.log('Form found:', form.id);
                    form.onsubmit = function () {
                        try {
                            console.log('Form submission triggered for:', form.id);
                            descriptionInput.value = quill.root.innerHTML;
                            console.log('Updated description textarea value:', descriptionInput.value);
                            if (!descriptionInput.value || descriptionInput.value === '<p><br></p>') {
                                console.warn('Description is empty or just a blank paragraph. This might cause validation errors.');
                            }
                        } catch (e) {
                            console.error('Error in onsubmit handler:', e);
                        }
                        return true; // Allow submission
                    };
                } else {
                    console.error('Could not find form with ID "createJobForm" or "editJobForm". Description will not be auto-updated.');
                    // Fallback for other forms if any, though less ideal
                    var genericForm = document.querySelector('form');
                    if (genericForm) {
                        console.warn('Attaching to the first form found on page as a fallback:', genericForm);
                        genericForm.onsubmit = function () {
                             try {
                                console.log('Form submission triggered for generic form.');
                                descriptionInput.value = quill.root.innerHTML;
                                console.log('Updated description textarea value (generic form):', descriptionInput.value);
                            } catch (e) {
                                console.error('Error in generic onsubmit handler:', e);
                            }
                            return true;
                        };
                    }
                }
            } catch (e) {
                console.error('Error during Quill setup or form binding:', e);
            }
        });
    </script>
@endpush

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('admin.jobs.index') }}"
        class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
        {{ __('Cancel') }}
    </a>
    <x-primary-button>
        {{ isset($job) ? __('Update Job') : __('Create Job') }}
    </x-primary-button>
</div>
