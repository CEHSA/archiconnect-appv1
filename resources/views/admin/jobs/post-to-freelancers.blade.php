<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Post Job to Freelancers') }} - {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-green-300">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Job Details</h3>
                    <p><strong>Title:</strong> {{ $job->title }}</p>
                    <p><strong>Description:</strong> {!! $job->description !!}</p>
                    <p><strong>Budget:</strong> {{ $job->budget ? number_format($job->budget, 2) : 'N/A' }}</p>
                    <hr class="my-6">

                    <h3 class="text-lg font-medium text-gray-900 mb-4">Select Freelancers to Notify</h3>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.jobs.post-to-freelancers', $job) }}" class="mb-6 p-4 bg-gray-50 rounded-md">
                        <h4 class="font-semibold text-gray-700 mb-2">Filters</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="filter_availability" :value="__('Availability')" />
                                <select name="filter_availability" id="filter_availability" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All</option>
                                    {{-- Assuming availability options are stored or predefined. Example: --}}
                                    @php
                                        $availabilityOptions = ['available', 'busy', 'part_time_available']; // Example options
                                    @endphp
                                    @foreach($availabilityOptions as $option)
                                        <option value="{{ $option }}" {{ (isset($filters['filter_availability']) && $filters['filter_availability'] == $option) ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $option)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="filter_skills" :value="__('Skills (comma-separated)')" />
                                <x-text-input id="filter_skills" class="block mt-1 w-full" type="text" name="filter_skills" placeholder="e.g., PHP, Laravel" :value="$filters['filter_skills'] ?? ''" />
                            </div>
                            <div>
                                <x-input-label for="filter_experience" :value="__('Experience Level')" />
                                <select name="filter_experience" id="filter_experience" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All</option>
                                     {{-- Assuming experience levels are stored or predefined. Example: --}}
                                    @php
                                        $experienceLevels = ['entry', 'intermediate', 'expert', 'junior', 'senior']; // Example options
                                    @endphp
                                    @foreach($experienceLevels as $level)
                                        <option value="{{ $level }}" {{ (isset($filters['filter_experience']) && $filters['filter_experience'] == $level) ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $level)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-primary-button type="submit">{{ __('Apply Filters') }}</x-primary-button>
                             <a href="{{ route('admin.jobs.post-to-freelancers', $job) }}" class="ml-2 text-sm text-gray-600 hover:text-gray-900">{{ __('Clear Filters') }}</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.jobs.send-postings', $job) }}">
                        @csrf
                        {{-- job_id is part of the route, no need for hidden input if using route model binding correctly in controller --}}

                        <div class="mb-4">
                            <label for="select_all_freelancers" class="inline-flex items-center">
                                <input type="checkbox" id="select_all_freelancers" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Select All Freelancers') }}</span>
                            </label>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-lg shadow">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-cyan-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white"></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Skills</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Experience</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Availability</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white">Hours Logged</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($freelancers as $freelancer)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="freelancer_ids[]" value="{{ $freelancer->id }}" class="freelancer-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $freelancer->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $freelancer->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $freelancer->freelancerProfile->skills ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $freelancer->freelancerProfile->experience_level ? ucfirst(str_replace('_', ' ', $freelancer->freelancerProfile->experience_level)) : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                @if($freelancer->freelancerProfile->availability === 'available')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ ucfirst($freelancer->freelancerProfile->availability) }}
                                                    </span>
                                                @elseif($freelancer->freelancerProfile->availability === 'busy')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        {{ ucfirst($freelancer->freelancerProfile->availability) }}
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        {{ $freelancer->freelancerProfile->availability ? ucfirst(str_replace('_', ' ', $freelancer->freelancerProfile->availability)) : 'N/A' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $freelancer->total_hours_logged ?? 0 }} hrs
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                No freelancers found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.jobs.edit', $job) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button type="submit">
                                {{ __('Post Job to Selected Freelancers') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select_all_freelancers');
            const freelancerCheckboxes = document.querySelectorAll('.freelancer-checkbox');

            if(selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    freelancerCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            freelancerCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        // Check if all are checked
                        let allChecked = true;
                        freelancerCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                allChecked = false;
                            }
                        });
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>
