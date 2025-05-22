<x-layouts.freelancer>
    <x-slot name="header">
        {{ __('Message Admin About Job: ') }} {{ $job->title }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('freelancer.jobs.message-admin.store', $job) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Job Information (Read-only) -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-lg text-gray-800">Regarding Job: {{ $job->title }} (ID: {{ $job->id }})</h4>
                        </div>

                        <!-- Message Content -->
                        <div class="mt-4">
                            <x-input-label for="content" :value="__('Your Message')" />
                            <textarea id="content" name="content" rows="5" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-cyan-500 focus:ring-cyan-500" required autofocus>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Attachments -->
                        <div class="mt-4">
                            <x-input-label for="attachments" :value="__('Attachments (Optional)')" />
                            <input type="file" id="attachments" name="attachments[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 mt-1"/>
                            <p class="mt-1 text-xs text-gray-500">{{ __('You can upload multiple files. Max 5MB per file.') }}</p>
                            <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('freelancer.jobs.show', $job) }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="bg-cyan-700 hover:bg-cyan-600 focus:bg-cyan-600 active:bg-cyan-800 focus:ring-cyan-500">
                                {{ __('Send Message to Admin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.freelancer>
