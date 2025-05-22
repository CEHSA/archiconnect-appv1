<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Budget Appeal Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <a href="{{ route('admin.budget-appeals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Budget Appeals') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Budget Appeal #') }}{{ $budgetAppeal->id }}</h3>

                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <p><strong>{{ __('Freelancer:') }}</strong> {{ $budgetAppeal->freelancer->name }}</p>
                        <p><strong>{{ __('Job:') }}</strong> {{ $budgetAppeal->jobAssignment->job->title }}</p>
                        <p><strong>{{ __('Current Budget:') }}</strong> ${{ number_format($budgetAppeal->current_budget, 2) }}</p>
                        <p><strong>{{ __('Requested Budget:') }}</strong> ${{ number_format($budgetAppeal->requested_budget, 2) }}</p>
                        <p><strong>{{ __('Status:') }}</strong> <x-status-badge :status="$budgetAppeal->status" /></p>
                        <p><strong>{{ __('Submitted At:') }}</strong> {{ $budgetAppeal->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Reason for Appeal') }}</h4>
                        <p class="whitespace-pre-wrap">{{ $budgetAppeal->reason }}</p>
                    </div>

                    @if ($budgetAppeal->evidence_path)
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Evidence') }}</h4>
                            <a href="{{ route('admin.budget-appeals.download-evidence', $budgetAppeal) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                {{ __('Download Evidence File') }}
                            </a>
                        </div>
                    @endif

                    @if ($budgetAppeal->status === 'pending')
                        <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="font-semibold text-lg mb-3">{{ __('Admin Review') }}</h4>
                            <form method="POST" action="{{ route('admin.budget-appeals.update', $budgetAppeal) }}">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <x-input-label for="admin_remarks" :value="__('Admin Remarks (Optional)')" />
                                    <textarea id="admin_remarks" name="admin_remarks" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('admin_remarks', $budgetAppeal->admin_remarks) }}</textarea>
                                    <x-input-error :messages="$errors->get('admin_remarks')" class="mt-2" />
                                </div>

                                <div class="flex items-center space-x-4">
                                    <x-primary-button type="submit" name="status" value="under_review_by_client" class="bg-blue-600 hover:bg-blue-500 focus:bg-blue-700 focus:ring-blue-500">
                                        {{ __('Forward to Client for Review') }}
                                    </x-primary-button>
                                    <x-danger-button type="submit" name="status" value="rejected">
                                        {{ __('Reject Appeal (Admin Only)') }}
                                    </x-danger-button>
                                </div>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </form>
                        </div>
                    @else
                         <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md bg-blue-50 dark:bg-blue-900">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Admin Remarks') }}</h4>
                            <p class="whitespace-pre-wrap">{{ $budgetAppeal->admin_remarks ?: '-' }}</p>
                        </div>
                    @endif

                    @if ($budgetAppeal->status === 'under_review_by_client')
                         <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="font-semibold text-lg mb-3">{{ __('Client Decision') }}</h4>
                             {{-- TODO: Implement Client Interface for this --}}
                            <p class="text-gray-600 dark:text-gray-400">Awaiting client decision.</p>
                        </div>
                    @elseif (in_array($budgetAppeal->status, ['approved', 'rejected']))
                         <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md @if($budgetAppeal->status === 'approved') bg-green-50 dark:bg-green-900 @else bg-red-50 dark:bg-red-900 @endif">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Client Decision') }}</h4>
                            <p><strong>{{ __('Decision:') }}</strong> {{ Str::title(str_replace('_', ' ', $budgetAppeal->client_decision)) }}</p>
                            <p><strong>{{ __('Client Remarks:') }}</strong> {{ $budgetAppeal->client_remarks ?: '-' }}</p>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
