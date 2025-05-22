@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Job Title -->
    <div>
        <x-input-label for="title" :value="__('Job Title')" class="text-gray-700" />
        <x-text-input id="title" class="block mt-1 w-full placeholder-gray-700" type="text" name="title" :value="old('title', $job->title ?? '')" placeholder="Enter job title" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <!-- Client Assignment (User ID) -->
    <div>
        <x-input-label for="user_id" :value="__('Assign to Client (Optional)')" class="text-gray-700" />
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
        <x-input-label for="editor-container" :value="__('Job Description')" class="text-gray-700" />
        <div id="editor-container" class="mt-1 h-64 border border-gray-300 rounded-md"></div>
        <textarea id="description" name="description"
            class="hidden">{{ old('description', $job->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <!-- Budget -->
    <div>
        <x-input-label for="budget" :value="__('Budget (Optional)')" class="text-gray-700" />
        <x-text-input id="budget" class="block mt-1 w-full placeholder-gray-700" type="number" name="budget" :value="old('budget', $job->budget ?? '')" placeholder="e.g., 5000.00" step="0.01" />
        <x-input-error :messages="$errors->get('budget')" class="mt-2" />
    </div>

    <!-- Hourly Rate -->
    <div>
        <x-input-label for="hourly_rate" :value="__('Hourly Rate (Optional)')" class="text-gray-700" />
        <x-text-input id="hourly_rate" class="block mt-1 w-full placeholder-gray-700" type="number" name="hourly_rate"
            :value="old('hourly_rate', $job->hourly_rate ?? '')" placeholder="e.g., 50.00" step="0.01" />
        <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
    </div>

    <!-- Not-to-Exceed Budget -->
    <div>
        <x-input-label for="not_to_exceed_budget" :value="__('Not-to-Exceed Budget (Optional)')" class="text-gray-700" />
        <x-text-input id="not_to_exceed_budget" class="block mt-1 w-full placeholder-gray-700" type="number" name="not_to_exceed_budget"
            :value="old('not_to_exceed_budget', $job->not_to_exceed_budget ?? '')" placeholder="e.g., 10000.00" step="0.01" />
        <x-input-error :messages="$errors->get('not_to_exceed_budget')" class="mt-2" />
    </div>

    <!-- Skills Required -->
    <div>
        <x-input-label for="skills_required" :value="__('Skills Required (Optional, comma-separated)')" class="text-gray-700" />
        <x-text-input id="skills_required" class="block mt-1 w-full placeholder-gray-700" type="text" name="skills_required"
            :value="old('skills_required', $job->skills_required ?? '')" placeholder="e.g., Drafting, 3D Modeling" />
        <x-input-error :messages="$errors->get('skills_required')" class="mt-2" />
    </div> <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" class="text-gray-700" />
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

    <!-- Freelancer Assignment (assigned_freelancer_id) -->
    <div>
        <x-input-label for="assigned_freelancer_id" :value="__('Assign to Freelancer (Optional)')" class="text-gray-700" />
        <select name="assigned_freelancer_id" id="assigned_freelancer_id"
            class="block mt-1 w-full border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">{{ __('Select a Freelancer') }}</option>
            {{-- Assuming $freelancers is passed to the view --}}
            @isset($freelancers)
                @foreach($freelancers as $freelancer)
                    <option value="{{ $freelancer->id }}" {{ old('assigned_freelancer_id', $job->assigned_freelancer_id ?? '') == $freelancer->id ? 'selected' : '' }}>
                        {{ $freelancer->name }} ({{ $freelancer->email }}) {{-- Assuming freelancer model has name and email --}}
                    </option>
                @endforeach
            @endisset
        </select>
        <x-input-error :messages="$errors->get('assigned_freelancer_id')" class="mt-2" />
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
        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded mr-4">
        {{ __('Cancel') }}
    </a>
    @if(isset($job) && $job->id)
        <a href="{{ route('admin.jobs.post-to-freelancers', $job) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
            {{ __('Post to Freelancers') }}
        </a>
    @endif
    <x-primary-button>
        {{ isset($job) ? __('Update Job') : __('Create Job') }}
    </x-primary-button>
</div>
