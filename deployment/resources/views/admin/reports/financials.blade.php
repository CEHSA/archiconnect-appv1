<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Financial Overview</h3>

                    <div class="mt-6">
                        <h4 class="font-semibold text-xl text-gray-800 mb-4">Summary</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">                            <div class="bg-gray-100 p-6 rounded-md shadow-sm">
                                <p class="text-gray-600 text-sm">Total Estimated Earnings from Completed Assignments</p>
                                <p class="text-2xl font-semibold text-green-700">R{{ number_format($financialData['totalEstimatedEarningsFromCompletedAssignments'], 2) }}</p>
                            </div>
                            <div class="bg-gray-100 p-6 rounded-md shadow-sm">
                                <p class="text-gray-600 text-sm">Total Payouts Processed</p>
                                <p class="text-2xl font-semibold text-red-700">R{{ number_format($financialData['totalPayouts'], 2) }}</p>
                            </div>
                        </div>

                        <h4 class="font-semibold text-xl text-gray-800 mb-4">Completed Assignments (Estimated Earnings)</h4>
                        @if ($financialData['completedAssignments']->isEmpty())
                            <p class="text-gray-600 text-sm">No completed assignments found.</p>
                        @else
                            <div class="overflow-x-auto mb-8">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Freelancer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Hours</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Earnings</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Budget</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hourly Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($financialData['completedAssignments'] as $assignment)
                                            <tr>                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $assignment['job_title'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment['client_name'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment['freelancer_name'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment['total_approved_hours'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-700">R{{ number_format($assignment['estimated_earnings'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment['job_budget'] ? 'R' . number_format($assignment['job_budget'], 2) : 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment['job_hourly_rate'] ? 'R' . number_format($assignment['job_hourly_rate'], 2) : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <h4 class="font-semibold text-xl text-gray-800 mb-4">Payment History</h4>
                         @if ($financialData['payments']->isEmpty())
                            <p class="text-gray-600 text-sm">No payment records found.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Freelancer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($financialData['payments'] as $payment)
                                            <tr>                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->jobAssignment->job->title ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->freelancer->name ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-700">R{{ number_format($payment->amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
