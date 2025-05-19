<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tasks for Assignment on Job: ') }} {{ $jobAssignment->job->title }}
                <span class="text-sm text-gray-500">(Freelancer: {{ $jobAssignment->freelancer->name }})</span>
            </h2>
            <a href="{{ route('admin.job-assignments.tasks.create', $jobAssignment) }}" class="bg-architimex-primary hover:bg-architimex-primary-darker text-white font-bold py-2 px-4 rounded">
                {{ __('Add New Task') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($tasks->isEmpty())
                        <p class="text-gray-500">No tasks have been added to this assignment yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $task->order }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $task->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @switch($task->status)
                                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                                        @case('in_progress') bg-blue-100 text-blue-800 @break
                                                        @case('completed') bg-green-100 text-green-800 @break
                                                        @case('cancelled') bg-red-100 text-red-800 @break
                                                        @default bg-gray-100 text-gray-800
                                                    @endswitch">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                     <div class="mt-6">
                        <a href="{{ route('admin.job-assignments.show', $jobAssignment) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            &larr; {{ __('Back to Assignment Details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
