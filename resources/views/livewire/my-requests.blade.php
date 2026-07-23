<div class="space-y-6">
    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Total Submitted</span>
                <span class="block text-2xl font-black text-slate-850 dark:text-white leading-tight font-mono">{{ $total }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">📋</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Pending Review</span>
                <span class="block text-2xl font-black text-amber-600 leading-tight font-mono">{{ $pending }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-955/20 text-amber-600 flex items-center justify-center font-bold text-sm">⏳</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Approved</span>
                <span class="block text-2xl font-black text-emerald-600 leading-tight font-mono">{{ $approved }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center font-bold text-sm">✓</div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Declined</span>
                <span class="block text-2xl font-black text-rose-600 leading-tight font-mono">{{ $declined }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-955/20 text-rose-600 flex items-center justify-center font-bold text-sm">✗</div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="relative flex-1">
            <input type="text" wire:model.live="search" placeholder="Search by description or reference..." class="w-full rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs pl-10 h-11">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
        </div>
        <select wire:model.live="statusFilter" class="rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs min-w-[150px] h-11">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="declined">Declined</option>
        </select>
    </div>

    <!-- Request Status Card Feed -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($requestsList as $req)
            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:border-slate-200 dark:hover:border-slate-700 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest text-[#1e40af] bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-md">
                            {{ $req['type_label'] }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400 font-mono">
                            {{ $req['reference_number'] }}
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-white leading-relaxed truncate">{{ $req['detail'] }}</h4>
                    <p class="text-[10px] text-slate-400">{{ $req['created_at'] }}</p>
                </div>
                
                <!-- Status Badges -->
                <div class="shrink-0">
                    @if(in_array(strtolower($req['status']), ['approved', 'confirmed', 'completed', 'active']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-100 dark:border-emerald-900/30">
                            Approved
                        </span>
                    @elseif(in_array(strtolower($req['status']), ['declined', 'rejected', 'cancelled']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-955/20 text-rose-600 border border-rose-100 dark:border-rose-900/30">
                            Declined
                        </span>
                    @elseif(in_array(strtolower($req['status']), ['review', 'under_review']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-955/20 text-amber-600 border border-amber-100 dark:border-amber-900/30">
                            Under Review
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-850 text-slate-600 border border-slate-200/50 dark:border-slate-800/40">
                            Pending
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800">
                <span class="text-3xl">📭</span>
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-400 mt-2">No Requests Found</h3>
                <p class="text-xs text-slate-400 mt-1">Try adjusting your filters or search terms.</p>
            </div>
        @endforelse
    </div>
</div>
