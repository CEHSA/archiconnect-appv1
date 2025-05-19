<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Budget Appeal Review') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

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

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Budget Appeal for Job:') }} {{ $budgetAppeal->jobAssignment->job->title }}</h3>

                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <p><strong>{{ __('Freelancer:') }}</strong> {{ $budgetAppeal->freelancer->name }}</p>
                        <p><strong>{{ __('Current Budget:') }}</strong> ${{ number_format($budgetAppeal->current_budget, 2) }}</p>
                        <p><strong>{{ __('Requested Budget:') }}</strong> ${{ number_format($budgetAppeal->requested_budget, 2) }}</p>
                        <p><strong>{{ __('Submitted At:') }}</strong> {{ $budgetAppeal->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Reason for Appeal') }}</h4>
                        <p class="whitespace-pre-wrap">{{ $budgetAppeal->reason }}</p>
                    </div>

                    @if ($budgetAppeal->admin_remarks)
                         <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md bg-blue-50 dark:bg-blue-900">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Admin Remarks') }}</h4>
                            <p class="whitespace-pre-wrap">{{ $budgetAppeal->admin_remarks ?: '-' }}</p>
                        </div>
                    @endif

                    @if ($budgetAppeal->status === 'under_review_by_client')
                        <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                            <h4 class="font-semibold text-lg mb-3">{{ __('Your Decision') }}</h4>
                            <form method="POST" action="{{ route('client.budget-appeals.update', $budgetAppeal) }}">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <x-input-label for="client_remarks" :value="__('Your Remarks (Optional)')" />
                                    <textarea id="client_remarks" name="client_remarks" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('client_remarks', $budgetAppeal->client_remarks) }}</textarea>
                                    <x-input-error :messages="$errors->get('client_remarks')" class="mt-2" />
                                </div>

                                <div class="flex items-center space-x-4">
                                    <x-primary-button type="submit" name="client_decision" value="approved" class="bg-green-600 hover:bg-green-500 focus:bg-green-700 focus:ring-green-500">
                                        {{ __('Approve Appeal') }}
                                    </x-primary-button>
                                    <x-danger-button type="submit" name="client_decision" value="rejected">
                                        {{ __('Reject Appeal') }}
                                    </x-danger-button>
                                </div>
                                <x-input-error :messages="$errors->get('client_decision')" class="mt-2" />
                            </form>
                        </div>
                    @elseif (in_array($budgetAppeal->status, ['approved', 'rejected']))
                         <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md @if($budgetAppeal->status === 'approved') bg-green-50 dark:bg-green-900 @else bg-red-50 dark:bg-red-900 @endif">
                            <h4 class="font-semibold text-lg mb-2">{{ __('Your Decision') }}</h4>
                            <p><strong>{{ __('Decision:') }}</strong> {{ Str::title(str_replace('_', ' ', $budgetAppeal->client_decision)) }}</p>
                            <p><strong>{{ __('Your Remarks:') }}</strong> {{ $budgetAppeal->client_remarks ?: '-' }}</p>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
