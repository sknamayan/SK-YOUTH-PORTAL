@extends('layouts.app')
 
@section('content')
<div class="flex-1 flex flex-col md:flex-row bg-slate-50 dark:bg-slate-955 min-h-[calc(100vh-4rem)] p-3 md:p-0 flex items-center justify-center">
 
    <!-- Left Dashboard Sidebar -->
    @include('layouts.dashboard-sidebar')
 
    <!-- Main Dashboard Chat Content Pane (h-[92vh] w-full md:h-[calc(100vh-4rem)] md:w-full to look floating on mobile and fill screen on desktop) -->
    <div class="h-[92vh] w-full md:h-[calc(100vh-4rem)] md:w-full bg-white dark:bg-slate-900 md:border-0 border border-slate-150 dark:border-slate-800/80 rounded-[2rem] md:rounded-none shadow-xl md:shadow-none overflow-hidden flex flex-col md:flex-row"
         x-data="skonsultaAdminChat()">
        
        <!-- Left Pane: Thread List Sidebar -->
        <div class="w-full md:w-80 lg:w-96 border-r border-slate-150 dark:border-slate-800 flex flex-col bg-white dark:bg-slate-900 shrink-0 h-full"
             :class="activeId ? 'hidden md:flex' : 'flex'">
            
            <!-- Search & Filter Area -->
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 space-y-3 shrink-0">
                <div>
                    <span class="text-[9px] font-black uppercase text-blue-600 dark:text-blue-400 font-display tracking-widest">SKonsulta Platform</span>
                    <h2 class="text-sm font-black text-slate-850 dark:text-white uppercase tracking-tight font-display">Citizen Complaints</h2>
                </div>
 
                <form method="GET" action="{{ route('admin.consultations.index') }}" class="space-y-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Search complaints..." 
                            class="pl-8 pr-3 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 dark:text-white transition font-sans placeholder-slate-400 dark:placeholder-slate-500"
                        >
                    </div>
 
                    <select 
                        name="status" 
                        onchange="this.form.submit()"
                        class="block w-full py-2.5 pl-3 pr-8 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-700 dark:text-slate-300 outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition cursor-pointer appearance-none"
                    >
                        <option value="">All Statuses</option>
                        <option value="Open" {{ $statusFilter === 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ $statusFilter === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ $statusFilter === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </form>
            </div>
 
            <!-- Threads Scroll Log -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60 bg-slate-50/50 dark:bg-slate-900">
                @forelse($consultations as $item)
                    @php
                        $isActive = $activeConsultation && $activeConsultation->id === $item->id;
                        $badgeColor = match($item->status) {
                            'Open' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                            'In Progress' => 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-500/20',
                            'Resolved' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                        };
                        $latestMsg = $item->messages->first();
                        
                        // PHP logic for citizen's initials avatar
                        $citizenInitials = 'U';
                        if ($item->user) {
                            $userModel = $item->user;
                            $citizenInitials = strtoupper(substr($userModel->first_name ?? $userModel->name ?? 'U', 0, 1) . substr($userModel->last_name ?? '', 0, 1));
                            if (strlen($citizenInitials) < 2 && isset($userModel->name)) {
                                $words = explode(' ', $userModel->name);
                                $citizenInitials = strtoupper(substr($words[0] ?? 'U', 0, 1) . substr($words[1] ?? '', 0, 1));
                            }
                        }
                    @endphp
                    <a href="{{ route('admin.consultations.show', $item) }}" 
                       class="block p-4 hover:bg-slate-100 dark:hover:bg-slate-850/60 transition duration-150 relative border-l-4 {{ $isActive ? 'border-blue-600 bg-slate-100 dark:bg-slate-850/40' : 'border-transparent' }}">
                        
                        <div class="flex items-center gap-3">
                            <!-- Initials Avatar -->
                            <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-450 font-black text-xs flex items-center justify-center font-display shadow-xs shrink-0 select-none">
                                {{ $citizenInitials }}
                            </div>
 
                            <div class="flex-1 min-w-0 space-y-0.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[9px] font-mono font-bold text-slate-400 truncate">{{ $item->tracking_id }}</span>
                                    <span class="px-2 py-0.5 border rounded-full text-[8px] font-black uppercase tracking-wide shrink-0 {{ $badgeColor }}">
                                        {{ $item->status }}
                                    </span>
                                </div>
                                <h3 class="text-xs font-black text-slate-800 dark:text-slate-200 font-display truncate uppercase">{{ $item->subject }}</h3>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                    {{ $latestMsg ? $latestMsg->body : $item->message }}
                                </p>
                            </div>
                        </div>
 
                        <div class="flex items-center justify-between mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/40 text-[9px] font-bold text-slate-400 dark:text-slate-500">
                            <span>{{ $item->user ? $item->user->name : 'Anonymous Citizen' }}</span>
                            <span class="font-mono">{{ $item->updated_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 dark:text-slate-500">
                        No active complaints found.
                    </div>
                @endforelse
            </div>
            
            @if($consultations->hasPages())
                <div class="p-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50">
                    {{ $consultations->links() }}
                </div>
            @endif
        </div>
 
        <!-- Right Pane: Active Chat Room -->
        <div class="flex-1 flex flex-col bg-slate-50 dark:bg-slate-955 overflow-hidden h-full"
             :class="!activeId ? 'hidden md:flex' : 'flex'">
            
            @if($activeConsultation)
                @php
                    $activeUser = $activeConsultation->user;
                    $activeInitials = 'U';
                    if ($activeUser) {
                        $activeInitials = strtoupper(substr($activeUser->first_name ?? $activeUser->name ?? 'U', 0, 1) . substr($activeUser->last_name ?? '', 0, 1));
                        if (strlen($activeInitials) < 2 && isset($activeUser->name)) {
                            $words = explode(' ', $activeUser->name);
                            $activeInitials = strtoupper(substr($words[0] ?? 'U', 0, 1) . substr($words[1] ?? '', 0, 1));
                        }
                    }
                @endphp
                <!-- Active Chat Header -->
                <div class="bg-white dark:bg-slate-900 border-b border-slate-150 dark:border-slate-800 py-3.5 px-6 shrink-0 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <!-- Back arrow on mobile to return to threads list -->
                        <button @click="backToList()" class="md:hidden text-slate-400 hover:text-slate-650 dark:hover:text-white transition shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
 
                        <!-- Citizen Avatar -->
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-450 font-black text-xs flex items-center justify-center font-display shadow-xs shrink-0 select-none">
                            {{ $activeInitials }}
                        </div>
                        
                        <div class="min-w-0 space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-mono font-black text-slate-400">{{ $activeConsultation->tracking_id }}</span>
                                <span class="text-[10px] text-slate-400">•</span>
                                <span class="text-[9px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ $activeConsultation->category }}</span>
                            </div>
                            <h1 class="text-sm font-black text-slate-850 dark:text-slate-100 font-display truncate uppercase">{{ $activeConsultation->subject }}</h1>
                        </div>
                    </div>
                    
                    <!-- Ticket Status Selector -->
                    <div class="flex items-center gap-2">
                        <label class="text-[9px] font-black uppercase text-slate-450 dark:text-slate-500 hidden sm:inline">Status:</label>
                        <select 
                            x-model="status" 
                            @change="updateStatus($event.target.value)"
                            class="py-1.5 pl-3 pr-8 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-[10px] font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-blue-500 transition cursor-pointer appearance-none"
                        >
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>
                    </div>
                </div>
 
                <!-- Message Thread Area -->
                <div class="flex-1 overflow-y-auto px-6 py-6" id="adminChatArea">
                    <div class="space-y-6 max-w-3xl mx-auto">
                        
                        <!-- Initial Complaint Summary Card -->
                        <div class="p-4 bg-white dark:bg-slate-900 border border-slate-150 dark:border-slate-800 rounded-2xl space-y-2.5 shadow-xs text-xs">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/60 pb-1.5">
                                <span class="text-[9px] font-bold text-slate-450 uppercase">Complaint description</span>
                                <span class="text-[9px] font-semibold text-slate-400">Citizen: {{ $activeConsultation->user ? $activeConsultation->user->name : 'Anonymous' }}</span>
                            </div>
                            <p class="text-slate-650 dark:text-slate-350 leading-relaxed whitespace-pre-line">{{ $activeConsultation->message }}</p>
                            
                            @if($activeConsultation->attachment)
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-[10px]">
                                    <span class="text-slate-400">Attachment:</span>
                                    <a href="{{ asset('storage/' . $activeConsultation->attachment) }}" target="_blank" class="font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                                        📎 View Attached File
                                    </a>
                                </div>
                            @endif
                        </div>
 
                        <!-- Message Loop -->
                        <div class="space-y-4">
                            <template x-for="(msg, index) in messages" :key="index">
                                <div class="flex flex-col" :class="!msg.is_citizen ? 'items-end' : 'items-start'">
                                    <div class="max-w-[80%] sm:max-w-[70%] rounded-3xl p-4 shadow-xs text-xs leading-relaxed font-sans" 
                                         :class="!msg.is_citizen 
                                            ? 'bg-blue-600 dark:bg-indigo-650 text-white rounded-br-none' 
                                            : 'bg-white dark:bg-slate-900 text-slate-850 dark:text-slate-200 border border-slate-150 dark:border-slate-800 rounded-bl-none'">
                                        <p class="whitespace-pre-line" x-text="msg.body"></p>
                                        
                                        <template x-if="msg.attachment_path">
                                            <div class="mt-2.5 pt-2.5 border-t border-white/10 dark:border-slate-800/60 text-[10px]">
                                                <a :href="msg.attachment_path" target="_blank" class="inline-flex items-center gap-1.5 font-bold hover:underline" :class="!msg.is_citizen ? 'text-white' : 'text-blue-600 dark:text-blue-400'">
                                                    📎 Attachment: View File
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 px-1 text-[9px] font-semibold text-slate-400 dark:text-slate-500">
                                        <span x-text="!msg.is_citizen ? 'You' : msg.sender_name"></span>
                                        <span>&bull;</span>
                                        <span x-text="msg.formatted_time"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
 
                    </div>
                </div>
 
                <!-- Sticky Bottom Input Reply Area -->
                <div class="bg-white dark:bg-slate-900 border-t border-slate-150 dark:border-slate-800 p-4 shrink-0">
                    <div class="max-w-3xl mx-auto space-y-2">
                        
                        <!-- File upload preview badge -->
                        <div x-show="attachment" class="flex items-center justify-between bg-slate-50 dark:bg-slate-950 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 text-[10px] text-slate-500 dark:text-slate-400" x-cloak>
                            <span class="truncate font-medium" x-text="attachment ? '📎 File attached: ' + attachment.name : ''"></span>
                            <button @click="attachment = null" class="text-rose-500 hover:text-rose-700 font-bold transition">Remove</button>
                        </div>
 
                        <div class="flex items-end gap-3">
                            <button type="button" @click="$refs.adminFileInput.click()" class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 flex items-center justify-center transition shrink-0 active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>
                            <input type="file" x-ref="adminFileInput" @change="handleFile" class="hidden">
 
                            <div class="flex-1 relative">
                                <textarea 
                                    x-model="replyText" 
                                    @keydown.enter.prevent="sendMessage"
                                    placeholder="Write responsive reply message..." 
                                    rows="1" 
                                    class="w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-955 px-4 py-3 text-xs dark:text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition resize-none max-h-32"
                                ></textarea>
                            </div>
 
                            <button @click="sendMessage" type="button" :disabled="sending" class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-750 text-white flex items-center justify-center transition shrink-0 shadow-sm active:scale-95 disabled:opacity-50 font-display">
                                <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4">
                    <div class="w-20 h-20 bg-white dark:bg-slate-900 text-slate-350 border border-slate-100 dark:border-slate-800 rounded-3xl flex items-center justify-center text-3xl shadow-xs">💬</div>
                    <div class="space-y-1">
                        <h2 class="text-sm font-black text-slate-850 dark:text-white uppercase font-display tracking-tight">No Complaint Selected</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs leading-normal font-sans">Choose a complaint thread from the list on the left to review messages and reply in real-time.</p>
                    </div>
                </div>
            @endif
 
        </div>
 
    </div>
 
</div>
 
<script>
function skonsultaAdminChat() {
    return {
        activeId: {{ $activeConsultation ? $activeConsultation->id : 'null' }},
        status: '{{ $activeConsultation ? $activeConsultation->status : "" }}',
        replyText: '',
        messages: [],
        attachment: null,
        sending: false,
        init() {
            if (this.activeId) {
                this.loadMessages();
                this.setupBroadcasting();
            }
        },
        async loadMessages() {
            try {
                const response = await fetch(`/skonsulta/${this.activeId}/messages`);
                if (response.ok) {
                    const data = await response.json();
                    this.messages = data.messages;
                    this.status = data.consultation.status;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to fetch message history:', error);
            }
        },
        setupBroadcasting() {
            if (window.Echo) {
                window.Echo.private('consultation.' + this.activeId)
                    .listen('MessageSent', (e) => {
                        this.messages.push(e);
                        this.scrollToBottom();
                    });
            } else {
                setInterval(() => this.loadMessages(), 4000);
            }
        },
        handleFile(e) {
            this.attachment = e.target.files[0];
        },
        async sendMessage() {
            if (!this.replyText.trim() && !this.attachment) return;
            
            this.sending = true;
            const formData = new FormData();
            formData.append('body', this.replyText);
            if (this.attachment) {
                formData.append('attachment', this.attachment);
            }
 
            try {
                const response = await fetch(`/skonsulta/${this.activeId}/messages`, {
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
                    this.status = data.consultation_status;
                    this.replyText = '';
                    this.attachment = null;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Failed to send reply:', error);
            } finally {
                this.sending = false;
            }
        },
        async updateStatus(newStatus) {
            try {
                const response = await fetch(`/dashboard/consultations/${this.activeId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                if (response.ok) {
                    this.status = newStatus;
                    window.location.reload();
                }
            } catch (error) {
                console.error('Failed to update status:', error);
            }
        },
        backToList() {
            this.activeId = null;
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const chatArea = document.getElementById('adminChatArea');
                if (chatArea) {
                    chatArea.scrollTop = chatArea.scrollHeight;
                }
            });
        }
    }
}
</script>
@endsection
