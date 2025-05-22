<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen px-4 w-full">
        <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg transform transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col items-center mb-4">
                {{-- Using the bird logo image --}}
                <img src="{{ asset('images/bird_logo.png') }}" alt="Architex Axis Logo" class="w-24 h-auto mb-4 transform transition-all duration-500 hover:scale-105">
                <h1 class="text-2xl font-bold text-center text-gray-800">Welcome to Architex Axis</h1>
                <p class="text-sm text-center text-gray-500 mt-1">Log in to your account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5 w-full">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <x-text-input id="email" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md focus:border-cyan-500 focus:ring focus:ring-cyan-500 focus:ring-opacity-50 transition-all duration-300" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
                        <x-text-input id="password" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md focus:border-cyan-500 focus:ring focus:ring-cyan-500 focus:ring-opacity-50 transition-all duration-300" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- User Role -->
                <div class="space-y-2">
                    <x-input-label for="role" :value="__('User Role')" class="text-sm font-medium text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <select id="role" name="role" class="block w-full pl-10 px-4 py-3 text-gray-700 bg-[#EBF0F5] border-0 rounded-md shadow-sm focus:border-cyan-500 focus:ring focus:ring-cyan-500 focus:ring-opacity-50 transition-all duration-300 appearance-none" onchange="toggleRegisterLink(this.value)">
                            <option value="freelancer">Freelancer</option>
                            <option value="client">Client</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-cyan-700 hover:text-cyan-600 transition-colors duration-300 underline" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <div class="flex flex-col items-center pt-4">
                    <button type="submit" class="w-full px-4 py-3 text-sm font-medium text-white uppercase bg-cyan-700 rounded-md hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition-all duration-300 transform hover:translate-y-[-2px] active:bg-cyan-800 active:translate-y-0">
                        Log in
                    </button>
                </div>

                <div class="mt-6 text-center" id="register-link-container">
                    <p class="text-sm text-gray-600">
                        Don't have an account? <a href="{{ route('register') }}" class="font-medium text-cyan-700 hover:text-cyan-600 transition-colors duration-300 underline">Register now</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleRegisterLink(role) {
            const registerLinkContainer = document.getElementById('register-link-container');
            if (role === 'client') {
                registerLinkContainer.style.display = 'block';
            } else {
                registerLinkContainer.style.display = 'none';
            }
        }
        // Initialize based on the current selection on page load
        document.addEventListener('DOMContentLoaded', function() {
            const roleDropdown = document.getElementById('role');
            if (roleDropdown) {
                let currentRole = "{{ old('role', 'freelancer') }}"; // Default to freelancer
                if (currentRole === 'admin') {
                    currentRole = 'freelancer';
                }
                roleDropdown.value = currentRole;
                toggleRegisterLink(roleDropdown.value);
            }
        });
    </script>
</x-guest-layout>
