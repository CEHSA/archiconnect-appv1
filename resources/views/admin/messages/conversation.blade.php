<x-admin-layout>
    <x-slot name="header">
        {{ __('Conversation Details') }}
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Chat Header -->
            <div class="p-4 border-b flex items-center justify-between bg-gray-50">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        @if($conversation->job)
                            {{ $conversation->job->title }}
                        @else
                            {{ __('Group Chat') }}
                        @endif
                    </h3>
                    <div class="text-sm text-gray-500 flex items-center mt-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        {{ $conversation->participants->count() }} {{ __('participants') }}
                    </div>
                </div>
                <button type="button" class="text-gray-500 hover:text-gray-600" id="showParticipants">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </button>
            </div>

            <!-- Participants Modal -->
            <div class="hidden bg-white p-4 border-b" id="participantsPanel">
                <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Participants') }}</h4>
                <div class="space-y-2">
                    @foreach($conversation->participants as $participant)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $participant->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $participant->email }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                $participant->role === 'client' ? 'bg-blue-100 text-blue-800' : 
                                ($participant->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') 
                            }}">
                                {{ ucfirst($participant->role) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- Messages Container -->
            <div class="h-[calc(100vh-20rem)] overflow-y-auto p-4 space-y-4 bg-gray-100" id="messagesContainer">
                @forelse($conversation->messages->sortBy('created_at') as $message)
                    <div class="flex flex-col {{ auth()->id() === $message->user_id ? 'items-end' : 'items-start' }}">
                        <!-- Sender Name -->
                        <div class="text-xs text-gray-500 mb-1 {{ auth()->id() === $message->user_id ? 'text-right' : 'text-left' }}">
                            {{ $message->user->name }}
                        </div>
                        
                        <!-- Message Bubble -->
                        <div class="max-w-[75%]">
                            <div class="rounded-xl p-3 {{ 
                                auth()->id() === $message->user_id 
                                    ? 'bg-cyan-700 text-white rounded-tr-none' 
                                    : 'bg-white text-gray-800 rounded-tl-none border border-gray-200'
                            }}">
                                <div class="text-sm break-words">
                                    {{ $message->content }}
                                </div>
                            
                                @if($message->attachments->count() > 0)
                                    <div class="mt-2 space-y-2">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" 
                                               class="flex items-center text-xs {{ auth()->id() === $message->user_id ? 'text-cyan-100' : 'text-cyan-700' }} hover:underline">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                </svg>
                                                {{ $attachment->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Message Status for Admin -->
                                @if($message->status === 'pending' && auth()->user()->isAdmin())
                                    <div class="mt-2 flex space-x-2">
                                        <form action="{{ route('admin.messages.update', $message) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="text-xs py-1 px-2 rounded {{ 
                                                auth()->id() === $message->user_id 
                                                    ? 'bg-cyan-800 text-white hover:bg-cyan-900' 
                                                    : 'bg-cyan-700 text-white hover:bg-cyan-800'
                                            }}">
                                                {{ __('Approve') }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.messages.show', $message) }}" 
                                           class="text-xs py-1 px-2 rounded bg-gray-500 text-white hover:bg-gray-600">
                                            {{ __('Review') }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Message Meta -->
                            <div class="flex items-center mt-1 space-x-2">
                                <span class="text-xs text-gray-400">
                                    {{ $message->created_at->format('g:i A') }}
                                </span>
                                @if($message->status !== 'pending')
                                    <span class="text-xs {{ 
                                        $message->status === 'approved' ? 'text-green-500' : 'text-red-500' 
                                    }}">
                                        @if($message->status === 'approved')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-xs text-yellow-500">
                                        {{ __('Pending') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-full text-gray-500">
                        {{ __('No messages in this conversation yet.') }}
                    </div>
                @endforelse
            </div>

            <!-- Reply Form -->
            <div class="border-t border-gray-200 p-4 bg-white">
                <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    
                    <div class="flex items-end space-x-4">
                        <div class="flex-grow">
                            <textarea id="content" name="content" rows="1" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-cyan-500 focus:border-cyan-500 resize-none"
                                placeholder="{{ __('Type a message...') }}" required>{{ old('content') }}</textarea>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <label for="attachments" class="cursor-pointer text-gray-500 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <input type="file" id="attachments" name="attachments[]" multiple class="hidden">
                            </label>

                            <button type="submit" class="bg-cyan-700 text-white rounded-full p-2 hover:bg-cyan-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="attachmentPreview" class="hidden">
                        <p class="text-xs text-gray-500 mb-2">{{ __('Selected files:') }}</p>
                        <div id="fileList" class="text-xs text-gray-600"></div>
                    </div>

                    @error('content')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                    
                    @error('attachments.*')
                        <p class="text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>

        <!-- Load emoji picker -->
        <link href="https://cdn.jsdelivr.net/npm/emoji-mart@latest/css/emoji-mart.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/emoji-mart.js"></script>

        <!-- JavaScript -->
        <script>
            // Participants panel toggle
            const showParticipantsBtn = document.getElementById('showParticipants');
            const participantsPanel = document.getElementById('participantsPanel');
            
            showParticipantsBtn.addEventListener('click', () => {
                participantsPanel.classList.toggle('hidden');
            });

            // Auto-resize textarea
            const textarea = document.getElementById('content');
            textarea.addEventListener('input', function() {
                this.style.height = '0px';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Scroll messages container to bottom on load and when new messages arrive
            const messagesContainer = document.getElementById('messagesContainer');
            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            scrollToBottom();

            // Add emoji picker
            const picker = new EmojiMart.Picker({
                onSelect: emoji => {
                    const textarea = document.getElementById('content');
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    const newText = text.substring(0, start) + emoji.native + text.substring(end);
                    textarea.value = newText;
                    textarea.dispatchEvent(new Event('input')); // Trigger auto-resize
                }
            });
            picker.style.display = 'none';
            document.body.appendChild(picker);

            // Toggle emoji picker
            const emojiButton = document.createElement('button');
            emojiButton.type = 'button';
            emojiButton.className = 'text-gray-500 hover:text-gray-600';
            emojiButton.innerHTML = `
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-3.937 6.535a1 1 0 00.79.375h.002a1 1 0 00.79-.375c.622-.885 1.79-1.535 3.355-1.535a1 1 0 100-2c-2.353 0-4.185 1.045-5.002 2.534a1 1 0 00.066 1.001z" clip-rule="evenodd"/>
                </svg>
            `;

            const attachmentLabel = document.querySelector('label[for="attachments"]');
            attachmentLabel.parentNode.insertBefore(emojiButton, attachmentLabel);

            let emojiPickerVisible = false;
            emojiButton.addEventListener('click', () => {
                emojiPickerVisible = !emojiPickerVisible;
                picker.style.display = emojiPickerVisible ? 'block' : 'none';
                if (emojiPickerVisible) {
                    const rect = emojiButton.getBoundingClientRect();
                    picker.style.position = 'absolute';
                    picker.style.bottom = window.innerHeight - rect.top + 10 + 'px';
                    picker.style.left = rect.left + 'px';
                }
            });

            // Close emoji picker when clicking outside
            document.addEventListener('click', (e) => {
                if (emojiPickerVisible && !picker.contains(e.target) && !emojiButton.contains(e.target)) {
                    emojiPickerVisible = false;
                    picker.style.display = 'none';
                }
            });

            // File attachment preview
            const attachmentInput = document.getElementById('attachments');
            const attachmentPreview = document.getElementById('attachmentPreview');
            const fileList = document.getElementById('fileList');

            attachmentInput.addEventListener('change', () => {
                fileList.innerHTML = '';
                if (attachmentInput.files.length > 0) {
                    attachmentPreview.classList.remove('hidden');
                    Array.from(attachmentInput.files).forEach(file => {
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        fileList.innerHTML += `
                            <div class="flex items-center justify-between py-1">
                                <span>${file.name}</span>
                                <span>${fileSize} MB</span>
                            </div>
                        `;
                    });
                } else {
                    attachmentPreview.classList.add('hidden');
                }
            });
        </script>
    </div>
</x-admin-layout>
