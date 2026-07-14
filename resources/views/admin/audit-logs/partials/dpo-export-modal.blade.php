@if(auth()->user()?->hasPiiClearance())
<div x-data="{ exportOpen: false }" class="mb-4">
    <button type="button" @click="exportOpen = true"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export DPO Audit CSV
    </button>

    <div x-show="exportOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="exportOpen = false" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl p-6 space-y-5" @click.stop>
            <div>
                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Data Privacy</span>
                <h3 class="text-lg font-black text-slate-800 dark:text-white font-display uppercase mt-1">Export Audit Logs</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Filter activity logs by date range and download as CSV for DPO review.</p>
            </div>

            <form method="GET" action="{{ route('admin.logs.export') }}" class="space-y-4">
                <input type="hidden" name="type" value="{{ $type ?? 'system' }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 mb-1.5 block">Date From</label>
                        <x-date-picker name="date_from" value="{{ now()->subMonth()->toDateString() }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-white" />
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 mb-1.5 block">Date To</label>
                        <x-date-picker name="date_to" value="{{ now()->toDateString() }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-white" />
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 mb-1.5 block">Action Type (Optional)</label>
                    <select name="action_type" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2.5 text-sm dark:text-white">
                        <option value="">All actions</option>
                        @foreach($uniqueActions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="exportOpen = false" class="px-4 py-2 rounded-xl text-[11px] font-bold uppercase text-slate-500 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black uppercase tracking-wider transition">Download CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
