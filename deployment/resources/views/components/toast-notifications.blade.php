<div
    x-data="toastComponent()"
    @toast-message.window="addToast($event.detail)"
    class="fixed top-5 right-5 z-50 space-y-2 w-full max-w-xs sm:max-w-sm"
    aria-live="assertive"
>
    {{-- Toasts will be dynamically added here by Alpine.js --}}
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter-start="opacity-0 translate-x-12"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-12"
            class="p-4 rounded-md shadow-lg text-sm relative"
            :class="{
                'bg-green-500 text-white': toast.type === 'success',
                'bg-red-500 text-white': toast.type === 'error',
                'bg-yellow-400 text-black': toast.type === 'warning',
                'bg-blue-500 text-white': toast.type === 'info',
                'bg-gray-700 text-white': !['success', 'error', 'warning', 'info'].includes(toast.type)
            }"
            role="alert"
        >
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <!-- Heroicon mini: check-circle (success), x-circle (error), exclamation (warning), information-circle (info) -->
                    <svg x-show="toast.type === 'success'" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="toast.type === 'error'" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 101.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="toast.type === 'warning'" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1.75-4.5a1.75 1.75 0 00-3.5 0v.25a1.75 1.75 0 003.5 0v-.25zM10 12a1 1 0 100-2 1 1 0 000 2zM8.25 9.5A.75.75 0 019 8.75h2a.75.75 0 010 1.5H9A.75.75 0 018.25 9.5z" clip-rule="evenodd" />
                    </svg>
                     <svg x-show="toast.type === 'info'" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 1.5A8.5 8.5 0 1018.5 10 8.5 8.5 0 0010 1.5zM9 12a1 1 0 112 0v1a1 1 0 11-2 0v-1zm1-6.75a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 0010 5.25z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p x-text="toast.message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="removeToast(toast.id)" class="inline-flex rounded-md text-current opacity-70 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current">
                        <span class="sr-only">Close</span>
                        <!-- Heroicon mini: x -->
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Session-based toasts --}}
    @if (session('success'))
        <div x-init="addToast({ id: Date.now(), type: 'success', message: '{{ session('success') }}', visible: true })"></div>
    @endif
    @if (session('error'))
        <div x-init="addToast({ id: Date.now(), type: 'error', message: '{{ session('error') }}', visible: true })"></div>
    @endif
    @if (session('warning'))
        <div x-init="addToast({ id: Date.now(), type: 'warning', message: '{{ session('warning') }}', visible: true })"></div>
    @endif
    @if (session('info'))
        <div x-init="addToast({ id: Date.now(), type: 'info', message: '{{ session('info') }}', visible: true })"></div>
    @endif
    {{-- Handle Breeze 'status' for profile updates as success --}}
    @if (session('status') && session('status') === 'profile-updated')
        <div x-init="addToast({ id: Date.now(), type: 'success', message: 'Profile updated successfully.', visible: true })"></div>
    @endif
     @if (session('status') && session('status') === 'password-updated')
        <div x-init="addToast({ id: Date.now(), type: 'success', message: 'Password updated successfully.', visible: true })"></div>
    @endif
    {{-- Add more specific status messages from Breeze if needed --}}

</div>

<script>
    function toastComponent() {
        return {
            toasts: [],
            autoHideDelay: 5000, // 5 seconds
            addToast(toast) {
                toast.id = toast.id || Date.now();
                toast.visible = true;
                this.toasts.push(toast);
                this.scheduleHide(toast.id);
            },
            removeToast(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
            scheduleHide(id) {
                setTimeout(() => {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) {
                        toast.visible = false;
                        // Optionally remove from array after transition
                        setTimeout(() => this.removeToast(id), 500); // Wait for fade out
                    }
                }, this.autoHideDelay);
            }
        }
    }
</script>
