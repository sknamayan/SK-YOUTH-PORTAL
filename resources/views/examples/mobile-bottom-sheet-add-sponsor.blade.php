@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-950 px-4 py-6 text-slate-100">
        <div class="mx-auto max-w-md space-y-4">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4">
                <p class="text-[10px] font-black uppercase tracking-[0.24em] text-blue-300">SYSTEM STANDARD</p>
                <h1 class="mt-1 text-xl font-black uppercase tracking-tight text-white">Mobile Bottom Sheet Blueprint</h1>
                <p class="mt-2 text-sm text-slate-300">This example shows how the global mobile bottom sheet should host any primary action form.</p>
            </div>

            <button
                type="button"
                class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wide text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-700"
            >
                Open Add Sponsor Bottom Sheet
            </button>
        </div>

        <x-mobile-bottom-sheet closeLabel="Close add sponsor partner modal">
            <x-slot:title>ADD SPONSOR PARTNER</x-slot:title>
            <x-slot:subtitle>NEW SPONSOR</x-slot:subtitle>

            <div class="space-y-3">
                <div class="space-y-1.5">
                    <label for="sponsor-name" class="block text-[10px] font-black uppercase tracking-[0.24em] text-slate-300">
                        Sponsor / Partner Name
                    </label>
                    <input
                        id="sponsor-name"
                        type="text"
                        placeholder="Enter sponsor or partner name"
                        class="w-full rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2.5 text-sm text-white placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                    >
                </div>

                <div class="space-y-1.5">
                    <label for="status-label" class="block text-[10px] font-black uppercase tracking-[0.24em] text-slate-300">
                        Status Label
                    </label>
                    <select
                        id="status-label"
                        class="w-full rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2.5 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                    >
                        <option value="">Select status</option>
                        <option>Active</option>
                        <option>Pending</option>
                        <option>Archived</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="website-url" class="block text-[10px] font-black uppercase tracking-[0.24em] text-slate-300">
                        Website URL (Optional)
                    </label>
                    <input
                        id="website-url"
                        type="url"
                        placeholder="https://example.com"
                        class="w-full rounded-xl border border-slate-700 bg-slate-800/80 px-3.5 py-2.5 text-sm text-white placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                    >
                </div>

                <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-800/50 p-4 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-700/80 text-slate-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 015.5 5h6.086a1 1 0 01.707.293l1.414 1.414A1 1 0 0015.414 7H18.5A2.5 2.5 0 0121 9.5v8A2.5 2.5 0 0118.5 20h-13A2.5 2.5 0 013 17.5v-10z" />
                        </svg>
                    </div>
                    <p class="mt-3 text-xs font-semibold text-slate-300">Drag and drop logo here</p>
                    <p class="mt-1 text-[11px] text-slate-400">PNG, JPG, or WEBP</p>
                    <button
                        type="button"
                        class="mt-3 inline-flex items-center justify-center rounded-lg border border-slate-600 bg-slate-700 px-4 py-2 text-[11px] font-black uppercase tracking-wide text-slate-100 transition hover:bg-slate-600"
                    >
                        Browse Files
                    </button>
                </div>

                <div class="space-y-2 pt-1">
                    <button
                        type="button"
                        class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-blue-700"
                    >
                        Add Sponsor
                    </button>
                    <button
                        type="button"
                        class="w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-sm font-bold uppercase tracking-wide text-slate-200 transition hover:bg-slate-700"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </x-mobile-bottom-sheet>
    </div>
@endsection
