<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-6">
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-2">
                            {{ __('Back to Payments List') }}
                        </a>
                         <a href="{{ route('admin.payments.edit', $payment) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Edit Payment') }}
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

                    <h3 class="text-2xl font-semibold mb-4">{{ __('Payment #') }}{{ $payment->id }}</h3>

                    {{-- Display Assignment and Job Details --}}
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Assignment Details') }}</h4>
                        <p><strong>{{ __('Job Title:') }}</strong> {{ $payment->jobAssignment->job->title }}</p>
                        <p><strong>{{ __('Freelancer:') }}</strong> {{ $payment->freelancer->name }}</p>
                        <p><strong>{{ __('Assignment Status:') }}</strong> {{ $payment->jobAssignment->status }}</p>                        <p><strong>{{ __('Job Hourly Rate:') }}</strong> R{{ number_format($payment->jobAssignment->job->hourly_rate, 2) }}</p>
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

                    {{-- Display Payment Details --}}
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">                         <h4 class="font-semibold text-lg mb-2">{{ __('Payment Details') }}</h4>
                        <p><strong>{{ __('Amount:') }}</strong> R{{ number_format($payment->amount, 2) }}</p>
                        <p><strong>{{ __('Status:') }}</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($payment->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                    @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                    @case('completed') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                    @case('failed') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                    @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100
                                @endswitch">
                                {{ Str::title($payment->status) }}
                            </span>
                        </p>
                        <p><strong>{{ __('Transaction ID:') }}</strong> {{ $payment->transaction_id ?: '-' }}</p>
                        <p><strong>{{ __('Created At:') }}</strong> {{ $payment->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>{{ __('Last Updated:') }}</strong> {{ $payment->updated_at->format('M d, Y H:i') }}</p>
                    </div>


                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                        <h4 class="font-semibold text-lg mb-2">{{ __('Admin Notes') }}</h4>
                        <p class="whitespace-pre-wrap">{{ $payment->admin_notes ?: '-' }}</p>
                    </div>

                    <div class="mt-6">
                         <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment?');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">
                                {{ __('Delete Payment') }}
                            </x-danger-button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
