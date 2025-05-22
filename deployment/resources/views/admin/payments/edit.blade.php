<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Back to Payment Details') }}
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

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Edit Payment #') }}{{ $payment->id }}</h3>

                     {{-- Display Assignment and Job Details --}}
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Assignment Details') }}</h4>                        <p><strong>{{ __('Job Title:') }}</strong> {{ $payment->jobAssignment->job->title }}</p>
                        <p><strong>{{ __('Freelancer:') }}</strong> {{ $payment->freelancer->name }}</p>
                        <p><strong>{{ __('Assignment Status:') }}</strong> {{ $payment->jobAssignment->status }}</p>
                        <p><strong>{{ __('Job Hourly Rate:') }}</strong> R{{ number_format($payment->jobAssignment->job->hourly_rate, 2) }}</p>
                        <p><strong>{{ __('Job Not-to-Exceed Budget:') }}</strong> R{{ number_format($payment->jobAssignment->job->not_to_exceed_budget, 2) }}</p>

                         @php
                            $totalSeconds = 0;
                            foreach ($payment->jobAssignment->timeLogs as $log) {
                                $parts = explode(':', $log->duration);
                                if (count($parts) === 3) {
                                    $totalSeconds += (int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2];
                                }
                            }
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                            $seconds = $totalSeconds % 60;
                            $totalLoggedDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        @endphp
                        <p><strong>{{ __('Total Logged Hours:') }}</strong> {{ $totalLoggedDuration }}</p>
                        {{-- TODO: Add display for approved hours if that logic is implemented --}}
                    </div>


                    <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <x-input-label for="amount" :value="__('Payment Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" :value="old('amount', $payment->amount)" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="processing" {{ old('status', $payment->status) == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="transaction_id" :value="__('Transaction ID (Optional)')" />
                            <x-text-input id="transaction_id" class="block mt-1 w-full" type="text" name="transaction_id" :value="old('transaction_id', $payment->transaction_id)" />
                            <x-input-error :messages="$errors->get('transaction_id')" class="mt-2" />
                        </div>

                         <div class="mb-4">
                            <x-input-label for="admin_notes" :value="__('Admin Notes (Optional)')" />
                            <textarea id="admin_notes" name="admin_notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('admin_notes', $payment->admin_notes) }}</textarea>
                            <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Payment') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
