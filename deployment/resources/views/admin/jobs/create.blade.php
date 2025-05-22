<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-architimex-sidebar leading-tight">
            {{ __('Create New Job') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-architimex-sidebar">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.jobs.store') }}" id="createJobForm">
                        @include('admin.jobs._form', ['job' => new \App\Models\Job()])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
