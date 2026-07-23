<div class="space-y-6">
    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Submitted Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider font-display">Total Submitted</span>
                <span class="block text-2xl font-black text-slate-900 dark:text-white leading-tight font-mono">{{ $total }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-350 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        <!-- Pending Review Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider font-display">Pending Review</span>
                <span class="block text-2xl font-black text-slate-900 dark:text-white leading-tight font-mono">{{ $pending }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-350 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Approved Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider font-display">Approved</span>
                <span class="block text-2xl font-black text-slate-900 dark:text-white leading-tight font-mono">{{ $approved }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-350 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Declined Card -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider font-display">Declined</span>
                <span class="block text-2xl font-black text-slate-900 dark:text-white leading-tight font-mono">{{ $declined }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-350 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="relative flex-1">
            <input type="text" wire:model.live="search" placeholder="Search by description or reference..." class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white placeholder-slate-400 text-xs pl-10 h-11 focus:ring-[#1e40af] dark:focus:ring-blue-500/30">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
        </div>
        <select wire:model.live="statusFilter" class="rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white text-xs min-w-[150px] h-11 focus:ring-[#1e40af] dark:focus:ring-blue-500/30">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="declined">Declined</option>
        </select>
    </div>

    <!-- Request Status Card Feed -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($requestsList as $req)
            <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-5 shadow-sm hover:border-slate-200 dark:hover:border-slate-600 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest text-[#1e40af] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-md border border-blue-100 dark:border-blue-900/50">
                            {{ $req['type_label'] }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 font-mono">
                            {{ $req['reference_number'] }}
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white leading-relaxed truncate">{{ $req['detail'] }}</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-450">{{ $req['created_at'] }}</p>
                </div>
                
                <!-- Status Badges -->
                <div class="shrink-0">
                    @if(in_array(strtolower($req['status']), ['approved', 'confirmed', 'completed', 'active']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-955/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">
                            Approved
                        </span>
                    @elseif(in_array(strtolower($req['status']), ['declined', 'rejected', 'cancelled']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-955/20 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">
                            Declined
                        </span>
                    @elseif(in_array(strtolower($req['status']), ['review', 'under_review']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-955/20 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">
                            Under Review
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-350 border border-slate-200/50 dark:border-slate-700">
                            Pending
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-3xl border border-slate-105 dark:border-slate-700">
                <span class="text-3xl block mb-2">📭</span>
                <h3 class="text-sm font-bold text-slate-750 dark:text-slate-400">No Requests Found</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Try adjusting your filters or search terms.</p>
            </div>
        @endforelse
    </div>
</div>
