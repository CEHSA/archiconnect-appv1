<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen px-4 w-full">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg transform transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col items-center mb-4">
                {{-- Using the bird logo image --}}
                <img src="{{ asset('images/bird_logo.png') }}" alt="Archi-TimeX Keeper Logo" class="w-24 h-auto mb-4 transform transition-all duration-500 hover:scale-105">
                <h1 class="text-2xl font-bold text-center text-gray-800">Create an Account</h1>
                <p class="text-sm text-center text-gray-500 mt-1">Join Archi-TimeX Keeper today</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5 w-full">
                @csrf

                <!-- Name -->
                <div class="space-y-2">
                    <x-input-label for="name" :value="__('Name')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <x-text-input id="name" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md focus:border-architimex-primary focus:ring focus:ring-architimex-primary focus:ring-opacity-50 transition-all duration-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <x-text-input id="email" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md focus:border-architimex-primary focus:ring focus:ring-architimex-primary focus:ring-opacity-50 transition-all duration-300" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <x-input-label for="role" :value="__('Register as')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <select id="role" name="role" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md shadow-sm focus:border-architimex-primary focus:ring focus:ring-architimex-primary focus:ring-opacity-50 transition-all duration-300 appearance-none">
                            <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>{{ __('Client') }}</option>
                            <option value="freelancer" {{ old('role') == 'freelancer' ? 'selected' : '' }}>{{ __('Freelancer') }}</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md focus:border-architimex-primary focus:ring focus:ring-architimex-primary focus:ring-opacity-50 transition-all duration-300" type="password" name="password" required autocomplete="new-password" placeholder="Create a password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password_confirmation" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md focus:border-architimex-primary focus:ring focus:ring-architimex-primary focus:ring-opacity-50 transition-all duration-300" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex flex-col items-center pt-4">
                    <button type="submit" class="w-full px-4 py-3 text-sm font-medium text-white uppercase bg-architimex-primary rounded-md hover:bg-architimex-primary-darker focus:outline-none focus:ring-2 focus:ring-architimex-primary focus:ring-offset-2 transition-all duration-300 transform hover:translate-y-[-2px] active:translate-y-0">
                        {{ __('Register') }}
                    </button>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? <a href="{{ route('login') }}" class="font-medium text-architimex-primary hover:text-architimex-primary-darker transition-colors duration-300 underline">Log in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
