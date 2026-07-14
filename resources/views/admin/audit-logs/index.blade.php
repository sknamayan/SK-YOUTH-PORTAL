@extends('layouts.app')

@section('content')
<div x-data="{ mobileSidebar: false, selectedLog: null }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

    <!-- Main Content Pane -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 h-16 px-4 flex items-center justify-between md:hidden shrink-0">
            <button @click="mobileSidebar = true" class="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white active:scale-95 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain rounded-full bg-white p-0.5 border" alt="SK Logo">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white font-display">SK Namayan</span>
            </div>
            <div class="w-10"></div>
        </header>

        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af] dark:text-slate-500 dark:hover:text-blue-400 transition duration-150">Dashboard</a>
                    <span class="text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $type === 'dpo' ? 'DPO Audit Logs' : 'System Audit Logs' }}</span>
                </div>
            </div>

            <!-- Page Title -->
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="text-[10px] font-black {{ $type === 'dpo' ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#1e40af] dark:text-blue-400' }} uppercase tracking-widest block font-display">
                        {{ $type === 'dpo' ? 'Data Privacy Office' : 'System Integrity' }}
                    </span>
                    <span class="px-2 py-0.5 {{ $type === 'dpo' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-450 border-emerald-100 dark:border-emerald-900/30' : 'bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-400 border-blue-100 dark:border-blue-900/30' }} rounded-md text-[9px] font-bold uppercase font-mono border">
                        {{ $logs->total() }} Log Entries
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white font-display uppercase mt-1">
                    {{ $type === 'dpo' ? 'DPO Audit Logs' : 'System Audit Logs' }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ $type === 'dpo' 
                        ? 'Review and export system activity logs filtered specifically for compliance and data privacy oversight (sensitive PII data access).' 
                        : 'Review activity trails of authentication events, user updates, structure edits, logo assets changes, and request lifecycles.' }}
                </p>
                <div class="pt-2">
                    @include('admin.audit-logs.partials.dpo-export-modal')
                </div>
            </div>

            <!-- Tabbed UI -->
            <div class="border-b border-slate-200 dark:border-slate-800">
                <nav class="flex space-x-6" aria-label="Tabs">
                    <a href="{{ route('admin.logs.index', ['type' => 'system']) }}" 
                       class="border-b-2 pb-3 px-1 text-xs font-black uppercase tracking-wider transition-all duration-150 {{ $type === 'system' ? 'border-[#1e40af] text-[#1e40af] dark:border-blue-500 dark:text-blue-450' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-350 hover:border-slate-300 dark:hover:border-slate-700' }}">
                        System Logs
                    </a>
                    <a href="{{ route('admin.logs.index', ['type' => 'dpo']) }}" 
                       class="border-b-2 pb-3 px-1 text-xs font-black uppercase tracking-wider transition-all duration-150 {{ $type === 'dpo' ? 'border-emerald-600 text-emerald-600 dark:border-emerald-500 dark:text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-350 hover:border-slate-300 dark:hover:border-slate-700' }}">
                        DPO Logs
                    </a>
                </nav>
            </div>

            <!-- Search & Filters Card -->
            <div class="card p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm">
                <form id="filterForm" method="GET" action="{{ route('admin.logs.index') }}" class="space-y-4">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <!-- Row 1: Search, Action, Year -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search Box (Col span 6) -->
                        <div class="md:col-span-6 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $search }}" 
                                placeholder="Search logs by action, IP address, payload details..." 
                                class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-805 rounded-2xl text-xs outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-[#1e40af] dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-600/5 dark:text-white transition font-sans placeholder-slate-400 dark:placeholder-slate-500"
                            >
                        </div>

                        <!-- Action Type Dropdown (Col span 3) -->
                        <div class="md:col-span-3 relative">
                            <select 
                                name="action_type" 
                                onchange="this.form.submit()"
                                class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 dark:bg-slate-955/40 border border-slate-200/60 dark:border-slate-800 rounded-2xl text-xs text-slate-700 dark:text-slate-300 outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-[#1e40af] dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                            >
                                <option value="">All Actions</option>
                                @foreach($uniqueActions as $action)
                                    <option value="{{ $action }}" {{ $actionFilter == $action ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <!-- Year Picker Dropdown (Col span 3) -->
                        <div class="md:col-span-3 relative">
                            <select 
                                name="year" 
                                onchange="this.form.submit()"
                                class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 dark:bg-slate-955/40 border border-slate-200/60 dark:border-slate-800 rounded-2xl text-xs text-slate-700 dark:text-slate-300 outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-[#1e40af] dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                            >
                                <option value="">All Created Years</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Limit, Reset -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-slate-100/60 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <!-- Page Size Limit select -->
                            <div class="relative w-32 shrink-0">
                                <select 
                                    name="limit" 
                                    onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 dark:bg-slate-955/40 border border-slate-200/60 dark:border-slate-800 rounded-2xl text-[11px] text-slate-600 dark:text-slate-400 outline-none focus:bg-white dark:focus:bg-slate-950 focus:border-[#1e40af] dark:focus:border-blue-500 focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                >
                                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 rows</option>
                                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 rows</option>
                                    <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25 rows</option>
                                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 rows</option>
                                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 rows</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Reset Filter Link -->
                            @if($search || $actionFilter || $yearFilter || $limit != 20)
                                <a href="{{ route('admin.logs.index', ['type' => $type]) }}" 
                                   class="inline-flex items-center text-[11px] font-bold text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-350 transition space-x-1 select-none cursor-pointer pl-2 py-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                        </div>

                        <div>
                            <button type="submit" class="hidden"></button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Logs Grid/Table -->
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl shadow-sm">
                @if($logs->isEmpty())
                    <div class="text-center py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">No Activity Logged</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto">Either no actions match your filter criteria or no system events have occurred yet.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Timestamp</th>
                                    <th class="p-4">Action</th>
                                    <th class="p-4 hidden md:table-cell">Subject Target</th>
                                    <th class="p-4">Actor</th>
                                    <th class="p-4 hidden sm:table-cell">IP Address</th>
                                    <th class="p-4 pr-6 text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                                @foreach($logs as $log)
                                    @php
                                        // Dynamic color classes based on action types
                                        $badgeColor = match(true) {
                                            str_contains($log->action, 'created') || str_contains($log->action, 'registered') => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border-emerald-150 dark:border-emerald-900/30',
                                            str_contains($log->action, 'updated') || $log->action === 'status_changed' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border-amber-150 dark:border-amber-900/30',
                                            str_contains($log->action, 'deleted') || str_contains($log->action, 'cancelled') || str_contains($log->action, 'failed') => 'bg-rose-50 dark:bg-rose-955/40 text-rose-700 dark:text-rose-455 border-rose-150 dark:border-rose-900/30',
                                            $log->action === 'pii_accessed' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border-indigo-150 dark:border-indigo-900/30',
                                            default => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-150 dark:border-blue-900/30'
                                        };
                                        
                                        $subjectDisplay = class_basename($log->subject_type);
                                        if ($log->subject_id > 0) {
                                            $subjectDisplay .= " (#{$log->subject_id})";
                                        } else {
                                            $subjectDisplay = 'System / None';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <!-- Timestamp -->
                                        <td class="p-4 pl-6 text-slate-500 dark:text-slate-400 font-mono whitespace-nowrap">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 block">{{ $log->created_at->diffForHumans() }}</span>
                                        </td>
                                        <!-- Action -->
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 border rounded-full text-[9px] font-extrabold uppercase tracking-wide font-display {{ $badgeColor }}">
                                                {{ str_replace('_', ' ', $log->action) }}
                                            </span>
                                        </td>
                                        <!-- Target -->
                                        <td class="p-4 font-mono text-slate-600 dark:text-slate-450 hidden md:table-cell">
                                            {{ $subjectDisplay }}
                                        </td>
                                        <!-- Actor -->
                                        <td class="p-4">
                                            @if($log->user)
                                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $log->user->name }}</span>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 block font-mono">{{ $log->user->email }}</span>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 italic">Guest / System</span>
                                            @endif
                                        </td>
                                        <!-- IP Address -->
                                        <td class="p-4 font-mono text-slate-400 dark:text-slate-500 hidden sm:table-cell">
                                            {{ $log->ip_address ?? 'N/A' }}
                                        </td>
                                        <!-- Actions/Details button -->
                                        <td class="p-4 pr-6 text-right whitespace-nowrap">
                                            <button @click='selectedLog = {
                                                        id: {{ $log->id }},
                                                        action: "{{ $log->action }}",
                                                        subject: "{{ $subjectDisplay }}",
                                                        ip: "{{ $log->ip_address ?? "N/A" }}",
                                                        date: "{{ $log->created_at->format("Y-m-d H:i:s") }}",
                                                        actor: "{{ $log->user ? $log->user->name : "Guest/System" }}",
                                                        actor_email: "{{ $log->user ? $log->user->email : "" }}",
                                                        payload: {!! json_encode($log->payload ?? []) !!}
                                                    }' 
                                                    class="inline-flex items-center px-2.5 py-1 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-[#1e40af] dark:hover:text-blue-400 hover:border-[#1e40af] dark:hover:border-blue-500 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95">
                                                Inspect
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($logs->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>

    <!-- Alpine.js Inspection Modal -->
    <div x-show="selectedLog !== null" 
         class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center" 
         style="display: none;"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity" 
             @click="selectedLog = null"></div>

        <!-- Modal Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-lg mx-auto z-10 border border-slate-100 dark:border-slate-800 max-h-[90vh] flex flex-col"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 dark:from-slate-800 dark:to-slate-950 px-6 py-4 flex items-center justify-between text-white shrink-0 border-b border-transparent dark:border-slate-800">
                <div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-200 dark:text-blue-300">Log Inspector</span>
                    <h3 class="text-sm font-extrabold uppercase tracking-wide font-display mt-0.5 text-white">Audit Entry Details</h3>
                </div>
                <button @click="selectedLog = null" class="text-white/80 hover:text-white transition active:scale-95 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-4 overflow-y-auto flex-1 text-xs">
                
                <!-- General Info Panel -->
                <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-950/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block font-display">Event Action</span>
                        <span class="font-extrabold text-[#1e40af] dark:text-blue-450 text-[11px] uppercase tracking-wide" x-text="selectedLog ? selectedLog.action.replace('_', ' ') : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block font-display">Timestamp</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300 text-[10px]" x-text="selectedLog ? selectedLog.date : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block font-display">Actor / Performed By</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]" x-text="selectedLog ? selectedLog.actor : ''"></span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 block font-mono" x-text="selectedLog && selectedLog.actor_email ? selectedLog.actor_email : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block font-display">IP Address & Subject</span>
                        <span class="font-mono text-slate-600 dark:text-slate-400 block text-[10px]" x-text="selectedLog ? 'IP: ' + selectedLog.ip : ''"></span>
                        <span class="font-mono text-[#1e40af] dark:text-blue-400 text-[10px] block mt-0.5" x-text="selectedLog ? 'Subject: ' + selectedLog.subject : ''"></span>
                    </div>
                </div>

                <!-- Payload Inspection Block -->
                <div class="space-y-2">
                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block font-display">Payload Data & Changes</span>
                    
                    <div class="bg-slate-900 text-slate-100 rounded-2xl p-4 font-mono text-[10px] overflow-x-auto shadow-inner leading-relaxed">
                        <template x-if="selectedLog && selectedLog.payload && Object.keys(selectedLog.payload).length > 0">
                            <div>
                                <template x-if="selectedLog.payload.changes">
                                    <div class="space-y-2">
                                        <div class="text-[#38bdf8] font-bold">// Changed Attributes:</div>
                                        <template x-for="(val, key) in selectedLog.payload.changes" :key="key">
                                            <div class="pl-2 border-l border-slate-700">
                                                <span class="text-amber-400" x-text="key"></span>: 
                                                <span class="text-rose-400" x-text="JSON.stringify(val.from)"></span> 
                                                <span class="text-slate-400">&rarr;</span> 
                                                <span class="text-emerald-400" x-text="JSON.stringify(val.to)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                
                                <div class="mt-3">
                                    <div class="text-[#38bdf8] font-bold">// Metadata:</div>
                                    <pre class="pl-2 text-slate-300" x-text="JSON.stringify(
                                        Object.keys(selectedLog.payload).reduce((acc, key) => {
                                            if (key !== 'changes') acc[key] = selectedLog.payload[key];
                                            return acc;
                                        }, {}), 
                                        null, 
                                        2
                                    )"></pre>
                                </div>
                            </div>
                        </template>
                        <template x-if="!selectedLog || !selectedLog.payload || Object.keys(selectedLog.payload).length === 0">
                            <span class="text-slate-500 italic">// No extra details in payload.</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 dark:bg-slate-950 px-6 py-4 flex items-center justify-end border-t border-slate-100 dark:border-slate-800 shrink-0">
                <button @click="selectedLog = null" class="px-4 py-2 bg-[#1e40af] hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold rounded-xl text-[10px] uppercase tracking-wider transition active:scale-95">
                    Done
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
