<x-freelancer-layout>
    <x-slot name="header">
        {{ __('Send Message to Admin for Assignment: ') . $assignment->job->title }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Compose New Message</h3>

                    <form method="POST" action="{{ route('freelancer.messages.store') }}">
                        @csrf

                        <input type="hidden" name="job_assignment_id" value="{{ $assignment->id }}">

                        <!-- Message Content -->
                        <div>
                            <x-input-label for="content" :value="__('Message')" />
                            <textarea id="content" name="content" rows="6" class="block mt-1 w-full border-gray-300 focus:border-architimex-primary focus:ring-architimex-primary rounded-md shadow-sm" required>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4 bg-architimex-primary hover:bg-architimex-primary-darker">
                                {{ __('Send Message') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-freelancer-layout>
