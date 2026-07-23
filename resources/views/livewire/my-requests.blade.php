<div class="space-y-6">
    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Submitted Card -->
        <div class="bg-slate-800 border border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Total Submitted</span>
                <span class="block text-2xl font-black text-white leading-tight font-mono">{{ $total }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center font-bold text-sm">📋</div>
        </div>

        <!-- Pending Review Card -->
        <div class="bg-slate-800 border border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Pending Review</span>
                <span class="block text-2xl font-black text-amber-400 leading-tight font-mono">{{ $pending }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center font-bold text-sm">⏳</div>
        </div>

        <!-- Approved Card -->
        <div class="bg-slate-800 border border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Approved</span>
                <span class="block text-2xl font-black text-emerald-400 leading-tight font-mono">{{ $approved }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-sm">✓</div>
        </div>

        <!-- Declined Card -->
        <div class="bg-slate-800 border border-slate-700 p-5 rounded-3xl shadow-sm flex items-center justify-between shrink-0">
            <div class="space-y-0.5">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider font-display">Declined</span>
                <span class="block text-2xl font-black text-rose-400 leading-tight font-mono">{{ $declined }}</span>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center font-bold text-sm">✗</div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-slate-800 p-4 rounded-3xl border border-slate-700 shadow-sm">
        <div class="relative flex-1">
            <input type="text" wire:model.live="search" placeholder="Search by description or reference..." class="w-full rounded-2xl border-slate-700 bg-slate-900 text-white placeholder-slate-550 text-xs pl-10 h-11 focus:ring-blue-500/30 focus:border-blue-500">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
        </div>
        <select wire:model.live="statusFilter" class="rounded-2xl border-slate-700 bg-slate-900 text-white text-xs min-w-[150px] h-11 focus:ring-blue-500/30 focus:border-blue-500">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="declined">Declined</option>
        </select>
    </div>

    <!-- Request Status Card Feed -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($requestsList as $req)
            <div class="bg-slate-800 border border-slate-700 rounded-3xl p-5 shadow-sm hover:border-slate-650 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest text-blue-400 bg-blue-950/60 px-2 py-0.5 rounded-md border border-blue-900/50">
                            {{ $req['type_label'] }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-500 font-mono">
                            {{ $req['reference_number'] }}
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-white leading-relaxed truncate">{{ $req['detail'] }}</h4>
                    <p class="text-[10px] text-slate-400">{{ $req['created_at'] }}</p>
                </div>
                
                <!-- Status Badges -->
                <div class="shrink-0">
                    @if(in_array(strtolower($req['status']), ['approved', 'confirmed', 'completed', 'active']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-950/60 text-emerald-400 border border-emerald-900/50">
                            Approved
                        </span>
                    @elseif(in_array(strtolower($req['status']), ['declined', 'rejected', 'cancelled']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-rose-955/40 text-rose-400 border border-rose-900/30">
                            Declined
                        </span>
                    @elseif(in_array(strtolower($req['status']), ['review', 'under_review']))
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-955/35 text-amber-400 border border-amber-900/30">
                            Under Review
                        </span>
                    @else
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-900 text-slate-350 border border-slate-700">
                            Pending
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-slate-800 rounded-3xl border border-slate-700">
                <span class="text-3xl">📭</span>
                <h3 class="text-sm font-bold text-slate-400 mt-2">No Requests Found</h3>
                <p class="text-xs text-slate-500 mt-1">Try adjusting your filters or search terms.</p>
            </div>
        @endforelse
    </div>
</div>
