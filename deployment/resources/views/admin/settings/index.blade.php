<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Site Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.store') }}">
                        @csrf
                        <div class="space-y-8">
                            @foreach ($settings as $group => $groupSettings)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 capitalize mb-4">{{ str_replace('_', ' ', $group) }} Settings</h3>
                                    <div class="space-y-6">
                                        @foreach ($groupSettings as $setting)
                                            <div>
                                                <x-input-label :for="$setting->key" :value="$setting->label" />
                                                @if ($setting->type === 'boolean')
                                                    <label class="inline-flex items-center mt-1">
                                                        <input type="hidden" name="{{ $setting->key }}" value="0"> <!-- Hidden input for false value -->
                                                        <input id="{{ $setting->key }}" type="checkbox" name="{{ $setting->key }}" value="1" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-architimex-primary shadow-sm focus:ring-architimex-primary dark:focus:ring-architimex-primary dark:focus:ring-offset-gray-800" {{ old($setting->key, \App\Models\Setting::castValue($setting->value, $setting->type)) ? 'checked' : '' }}>
                                                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Enable') }}</span>
                                                    </label>
                                                @elseif ($setting->type === 'integer')
                                                    <x-text-input id="{{ $setting->key }}" name="{{ $setting->key }}" type="number" class="mt-1 block w-full md:w-1/2" :value="old($setting->key, \App\Models\Setting::castValue($setting->value, $setting->type))" />
                                                @elseif ($setting->type === 'text')
                                                    <textarea id="{{ $setting->key }}" name="{{ $setting->key }}" rows="3" class="mt-1 block w-full md:w-1/2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-architimex-primary dark:focus:border-architimex-primary focus:ring-architimex-primary dark:focus:ring-architimex-primary rounded-md shadow-sm">{{ old($setting->key, \App\Models\Setting::castValue($setting->value, $setting->type)) }}</textarea>
                                                @else {{-- string --}}
                                                    <x-text-input id="{{ $setting->key }}" name="{{ $setting->key }}" type="text" class="mt-1 block w-full md:w-1/2" :value="old($setting->key, \App\Models\Setting::castValue($setting->value, $setting->type))" />
                                                @endif
                                                @if ($setting->description)
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                                @endif
                                                <x-input-error :messages="$errors->get($setting->key)" class="mt-2" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr class="border-gray-200 dark:border-gray-700">
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-end">
                            <x-primary-button>
                                {{ __('Save Settings') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
