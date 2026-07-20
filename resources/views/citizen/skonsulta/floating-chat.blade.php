<div x-data="skonsultaFloatingChat()" class="fixed bottom-6 right-6 z-50 flex flex-col items-end font-sans">
    
    <!-- Chat Box Window -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-250 transform"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="w-[330px] sm:w-[360px] h-[480px] bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2rem] shadow-2xl overflow-hidden flex flex-col mb-4 max-w-[calc(100vw-2rem)]"
         x-cloak>
        
        <!-- Header -->
        <header class="bg-blue-600 dark:bg-indigo-650 px-5 py-3.5 flex items-center justify-between text-white shrink-0 shadow-sm">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="space-y-0.5 min-w-0">
                    <h3 class="text-xs font-black uppercase tracking-wider font-display">SKONSULTA Support</h3>
                    <div class="flex items-center gap-1.5 text-[9px] font-bold text-blue-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span x-text="activeThread ? 'Connected • Status: ' + activeThread.status : 'SK Officials Online'"></span>
                    </div>
                </div>
            </div>
            
            <button @click="closeChat()" class="text-blue-100 hover:text-white transition p-1 rounded-lg hover:bg-white/10 shrink-0">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </header>
 
        <!-- Chat Body (Messages Stream) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-slate-955" id="floatingChatBody">
            <!-- Welcome message if no thread exists -->
            <template x-if="!activeThread && !loading">
                <div class="flex flex-col items-center justify-center text-center py-12 px-4 space-y-3">
                    <div class="space-y-1">
                        <h4 class="text-xs font-black text-slate-855 dark:text-white uppercase tracking-tight">Hello, {{ explode(' ', auth()->user()->name)[0] }}!</h4>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 leading-normal max-w-[200px]">Send a message to Sangguniang Kabataan officials to start your private consultation session.</p>
                    </div>
                </div>
            </template>
 
            <!-- Loading Indicator -->
            <template x-if="loading">
                <div class="flex items-center justify-center py-12 text-xs text-slate-400 dark:text-slate-500">
                    <span class="animate-pulse">Loading conversation history...</span>
                </div>
            </template>
 
            <!-- Message Feed -->
            <template x-if="activeThread && !loading">
                <div class="space-y-4">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex flex-col" :class="msg.is_citizen ? 'items-end' : 'items-start'">
                            <div class="max-w-[85%] rounded-2xl px-3.5 py-2.5 shadow-xs text-[11px] leading-relaxed font-sans" 
                                 :class="msg.is_citizen 
                                    ? 'bg-blue-600 dark:bg-indigo-650 text-white rounded-br-none' 
                                    : 'bg-white dark:bg-slate-900 text-slate-850 dark:text-slate-200 border border-slate-100 dark:border-slate-800 rounded-bl-none'">
                                <p class="whitespace-pre-line" x-text="msg.body"></p>
                                
                                <template x-if="msg.attachment_path">
                                    <div class="mt-2 pt-1.5 border-t border-white/10 dark:border-slate-800/60 text-[9px] font-bold">
                                        <a :href="msg.attachment_path" target="_blank" class="hover:underline inline-flex items-center gap-1" :class="msg.is_citizen ? 'text-white' : 'text-blue-600 dark:text-blue-400'">
                                            📎 Attachment: View File
                                        </a>
                                    </div>
                                </template>
                            </div>
                            <div class="flex items-center gap-1 mt-1 text-[8px] font-semibold text-slate-400 dark:text-slate-500">
                                <span x-text="msg.is_citizen ? 'You' : msg.sender_name"></span>
                                <span>&bull;</span>
                                <span x-text="msg.formatted_time"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
 
        <!-- Sticky Bottom Chat Input Bar -->
        <div class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 p-3 shrink-0 space-y-1.5">
            <!-- File attachment preview -->
            <div x-show="attachment" class="flex items-center justify-between bg-slate-50 dark:bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[9px]" x-cloak>
                <span class="truncate font-medium text-slate-550 dark:text-slate-450" x-text="attachment ? '📎 File: ' + attachment.name : ''"></span>
                <button @click="attachment = null" class="text-rose-500 hover:text-rose-700 font-bold transition">Remove</button>
            </div>
 
            <div class="flex items-center gap-2">
                <!-- Follow Up Request Plus Button -->
                <div class="relative shrink-0" x-data="{ openPopover: false }" @click.outside="openPopover = false">
                    <button type="button" 
                            @click="openPopover = !openPopover; if(openPopover) fetchCitizenRequests();" 
                            class="w-8.5 h-8.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-955 border border-slate-200 dark:border-slate-800 text-slate-450 dark:text-slate-400 flex items-center justify-center transition active:scale-95"
                            title="Follow-up on a request">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                    
                    <!-- Popover Panel -->
                    <div x-show="openPopover" 
                         x-transition:enter="transition ease-out duration-105"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute bottom-10 left-0 z-50 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-3 space-y-2 max-h-60 overflow-y-auto"
                         x-cloak>
                        <h4 class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 font-display">Follow-up on a Request</h4>
                        <hr class="border-slate-100 dark:border-slate-800">
                        
                        <div class="space-y-1.5">
                            <!-- Loading state -->
                            <div x-show="loadingRequests" class="text-center py-4 text-[10px] text-slate-400">
                                Loading your requests...
                            </div>
                            
                            <!-- Empty state -->
                            <div x-show="!loadingRequests && citizenRequests.length === 0" class="text-center py-4 text-[10px] text-slate-400 italic">
                                No active requests found.
                            </div>
                            
                            <!-- List of requests -->
                            <template x-for="req in citizenRequests" :key="req.id">
                                <button type="button" 
                                        @click="insertRequestFollowUp(req); openPopover = false;" 
                                        class="w-full text-left p-2 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-950 transition flex flex-col gap-0.5">
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-[9px] font-bold text-blue-650 dark:text-blue-400 font-mono" x-text="req.ref"></span>
                                        <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-855 text-slate-650 dark:text-slate-400 font-display" x-text="req.status"></span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-700 dark:text-slate-200 truncate w-full" x-text="req.type"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <button type="button" @click="$refs.chatFileInput.click()" class="w-8.5 h-8.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-955 border border-slate-200 dark:border-slate-800 text-slate-450 dark:text-slate-400 flex items-center justify-center transition shrink-0 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                </button>
                <input type="file" x-ref="chatFileInput" @change="handleFile" class="hidden">
 
                <input type="text" 
                       x-model="replyText" 
                       @keydown.enter.prevent="sendMessage"
                       placeholder="Type your message here..." 
                       class="flex-1 bg-slate-50 dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2 text-xs dark:text-white outline-none focus:border-blue-500 transition font-sans">
 
                <button @click="sendMessage" :disabled="sending || loading" class="w-8.5 h-8.5 rounded-lg bg-blue-600 hover:bg-blue-750 text-white flex items-center justify-center transition shrink-0 active:scale-95 disabled:opacity-50">
                    <svg class="w-3.5 h-3.5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
 
    <button @click="toggleChat()" 
            class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg transition duration-200 hover:scale-105 active:scale-95 focus:outline-none relative group border border-blue-500/20 overflow-visible"
            aria-label="SKONSULTA Chat Support">
        
        @if(($unreadMessagesCount ?? $unreadChatsCount ?? 0) > 0)
            <span class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm select-none z-10 animate-pulse">
                {{ $unreadMessagesCount ?? $unreadChatsCount }}
            </span>
        @else
            <!-- Pulsing indicator if chat has updates -->
            <span x-show="!open && activeThread" class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 flex h-3 w-3" x-cloak>
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
        @endif
        
        <!-- Chat Icon (w-5 h-5) -->
        <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.25">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 10.742h.01m3.999 0h.01m3.999 0h.01M9 16.5h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        
        <!-- Minimize Icon (w-5 h-5) -->
        <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" x-cloak>
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
        </svg>
    </button>
</div>
 
<script>
function skonsultaFloatingChat() {
    return {
        open: false,
        threads: [],
        messages: [],
        activeThread: null,
        loading: false,
        replyText: '',
        attachment: null,
        sending: false,
        echoListener: null,
        pollingInterval: null,
        citizenRequests: [],
        loadingRequests: false,
        init() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('skonsulta') === 'open') {
                this.open = true;
            }
            this.fetchLatestThread();
        },
        toggleChat() {
            this.open = !this.open;
            if (this.open) {
                this.fetchLatestThread();
            } else {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                }
            }
        },
        async fetchCitizenRequests() {
            this.loadingRequests = true;
            try {
                const response = await fetch('/skonsulta/api/citizen-requests');
                if (response.ok) {
                    const data = await response.json();
                    this.citizenRequests = data.requests || [];
                }
            } catch (error) {
                console.error('Failed to load citizen requests:', error);
            } finally {
                this.loadingRequests = false;
            }
        },
        insertRequestFollowUp(req) {
            this.replyText = `Regarding my ${req.type} request with Ref #${req.ref} (Status: ${req.status}), I would like to ask for an update. Thank you!`;
        },
        async fetchLatestThread() {
            this.loading = true;
            try {
                const response = await fetch('/skonsulta/api/threads');
                if (response.ok) {
                    const data = await response.json();
                    this.threads = data.threads || [];
                    if (this.threads.length > 0) {
                        this.activeThread = this.threads[0]; // Bind latest thread
                        await this.loadMessages();
                        this.setupEcho();
                    } else {
                        this.activeThread = null;
                        this.messages = [];
                    }
                }
            } catch (error) {
                console.error('Failed to query user threads:', error);
            } finally {
                this.loading = false;
            }
        },
        async loadMessages() {
            if (!this.activeThread) return;
            try {
                const response = await fetch(`/skonsulta/${this.activeThread.id}/messages`);
                if (response.ok) {
                    const data = await response.json();
                    this.messages = data.messages;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to load message log:', error);
            }
        },
        setupEcho() {
            if (this.echoListener) {
                this.echoListener.stopListening('MessageSent');
            }
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
            
            if (window.Echo) {
                this.echoListener = window.Echo.private('consultation.' + this.activeThread.id)
                    .listen('MessageSent', (e) => {
                        this.messages.push(e);
                        this.scrollToBottom();
                    });
            } else {
                // Fallback polling (every 4 seconds)
                this.pollingInterval = setInterval(() => this.loadMessages(), 4000);
            }
        },
        handleFile(e) {
            this.attachment = e.target.files[0];
        },
        async sendMessage() {
            if (!this.replyText.trim() && !this.attachment) return;
            this.sending = true;
            
            // If there's no thread, create one dynamically with this text
            if (!this.activeThread) {
                const formData = new FormData();
                formData.append('message', this.replyText);
                if (this.attachment) {
                    formData.append('attachment', this.attachment);
                }
                try {
                    const response = await fetch('{{ route("consultations.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    if (response.ok) {
                        this.replyText = '';
                        this.attachment = null;
                        await this.fetchLatestThread();
                    }
                } catch (error) {
                    console.error('Failed to initialize chat thread:', error);
                } finally {
                    this.sending = false;
                }
                return;
            }
 
            // Otherwise, send a direct response message to the existing thread
            const formData = new FormData();
            formData.append('body', this.replyText);
            if (this.attachment) {
                formData.append('attachment', this.attachment);
            }
 
            try {
                const response = await fetch(`/skonsulta/${this.activeThread.id}/messages`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
 
                if (response.ok) {
                    const data = await response.json();
                    this.messages.push(data.message);
                    this.replyText = '';
                    this.attachment = null;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to send message:', error);
            } finally {
                this.sending = false;
            }
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const chatBody = document.getElementById('floatingChatBody');
                if (chatBody) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            });
        },
        closeChat() {
            this.open = false;
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        }
    }
}
</script>
