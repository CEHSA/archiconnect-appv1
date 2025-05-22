<x-client-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Work Submissions for Review') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 text-gray-900">
                    @if ($workSubmissions->isEmpty())
                        <p>There are no work submissions ready for your review at this time.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-cyan-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                            Job Title
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                            Freelancer
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                            Submitted At
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">View</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($workSubmissions as $submission)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $submission->jobAssignment->job->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $submission->jobAssignment->freelancer->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $submission->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <x-status-badge :status="$submission->status" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('client.work-submissions.show', $submission) }}" class="text-cyan-700 hover:text-cyan-600">View</a>
                                            </td>
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
</x-client-layout>
