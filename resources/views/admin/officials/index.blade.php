@extends('layouts.app')

@section('content')
<div
    x-data="officialsAdmin({
        openOnLoad: {{ $errors->any() ? 'true' : 'false' }}
    })"
    x-init="init()"
    class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 min-h-0"
>

    @include('layouts.dashboard-sidebar')

    {{-- Mobile sidebar backdrop --}}
    <div
        x-show="mobileSidebar"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileSidebar = false"
        class="fixed inset-0 bg-slate-900/50 dark:bg-black/60 z-20 md:hidden"
        aria-hidden="true"
        x-cloak
    ></div>

    {{-- Main content shell --}}
    <div class="flex-1 flex flex-col min-w-0 min-h-0 md:min-h-[calc(100dvh-4rem)]">

        {{-- Sticky mobile app bar --}}

        {{-- Scrollable page body --}}
        <div class="flex-1 overflow-y-auto overscroll-y-contain p-4 md:p-8 space-y-4 md:space-y-6 pb-24 md:pb-8">

            {{-- Breadcrumbs --}}
            <nav aria-label="Breadcrumb" class="flex items-center pb-3 md:pb-4 border-b border-slate-100 dark:border-slate-800">
                <ol class="flex items-center gap-2 text-[10px] md:text-xs font-semibold uppercase tracking-wider min-w-0">
                    <li class="shrink-0">
                        <a href="{{ route('dashboard.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-[#1e40af] dark:hover:text-blue-400 transition duration-150">Dashboard</a>
                    </li>
                    <li class="text-slate-300 dark:text-slate-600 shrink-0" aria-hidden="true">/</li>
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">SK Officials</li>
                </ol>
            </nav>

            {{-- Page header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1 min-w-0">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Governance</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Manage SK Officials</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Add and update elected officer profiles shown on the public officials page.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <button
                        type="button"
                        @click="openAddOfficialModal = true"
                        class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add Official Profile</span>
                    </button>
                @endif
            </div>

            {{-- Officials Container --}}
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">

                @if($officials->isEmpty())
                    <div class="text-center py-12 md:py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">No Officials Added</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">Create the first SK official profile for the public page.</p>
                        </div>
                    </div>
                @else

                    {{-- Mobile view --}}
                    <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800" role="list" aria-label="Officials">
                        @foreach($officials as $official)
                            <li class="p-4 flex gap-3 min-w-0">
                                <div class="w-14 h-14 shrink-0 rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center">
                                    @if($official->photoUrl())
                                        <img src="{{ $official->photoUrl() }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#1e40af] text-white text-xs font-black">{{ $official->initials() }}</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 space-y-2">
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">{{ $official->name }}</p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-450 truncate">{{ $official->position }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 items-center">
                                        @if($official->is_active)
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-350 rounded">Active</span>
                                        @else
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-slate-150 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded">Hidden</span>
                                        @endif
                                        <span class="text-[9px] text-slate-400 dark:text-slate-500">Order: {{ $official->sort_order }}</span>
                                    </div>
                                    @if(Auth::user()->isAdmin())
                                        <div class="flex gap-2">
                                            <button
                                                type="button"
                                                @click="editOfficial({ id: {{ $official->id }}, name: '{{ addslashes($official->name) }}', position: '{{ addslashes($official->position) }}', bio: '{{ addslashes(str_replace(["\r", "\n"], ' ', $official->bio ?? '')) }}', email: '{{ addslashes($official->email ?? '') }}', contact_number: '{{ addslashes($official->contact_number ?? '') }}', term: '{{ addslashes($official->term ?? '') }}', sort_order: {{ $official->sort_order ?? 0 }}, is_active: {{ $official->is_active ? 1 : 0 }}, photo_url: '{{ $official->photoUrl() ?? '' }}' })"
                                                class="text-[10px] font-bold uppercase text-[#1e40af] dark:text-blue-300 px-3 py-2 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-950/70 rounded-lg min-h-9 inline-flex items-center transition"
                                            >
                                                Edit
                                            </button>
                                            <x-alert-dialog>
                                                <x-slot:trigger>
                                                    <button type="button" class="text-[10px] font-bold uppercase text-rose-700 dark:text-rose-300 px-3 py-2 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-955 rounded-lg min-h-9">Delete</button>
                                                </x-slot:trigger>
                                                <x-slot:icon>
                                                    <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </x-slot:icon>
                                                <x-slot:title>Delete Official</x-slot:title>
                                                <x-slot:description>
                                                    Are you sure you want to permanently delete this official's profile? This action cannot be undone.
                                                </x-slot:description>
                                                <x-slot:footer>
                                                    <button @click="open = false" type="button" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.officials.destroy', $official->id) }}" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </x-slot:footer>
                                            </x-alert-dialog>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Desktop view --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Photo</th>
                                    <th class="p-4">Name</th>
                                    <th class="p-4">Position</th>
                                    <th class="p-4">Term</th>
                                    <th class="p-4">Order</th>
                                    <th class="p-4">Status</th>
                                    @if(Auth::user()->isAdmin())
                                        <th class="p-4 pr-6 text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-650 dark:text-slate-350">
                                @foreach($officials as $official)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="p-4 pl-6">
                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center shrink-0">
                                                @if($official->photoUrl())
                                                    <img src="{{ $official->photoUrl() }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-[#1e40af] text-white text-[10px] font-black">{{ $official->initials() }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800 dark:text-slate-100">{{ $official->name }}</td>
                                        <td class="p-4 text-slate-600 dark:text-slate-400">{{ $official->position }}</td>
                                        <td class="p-4 text-slate-500 dark:text-slate-500">{{ $official->term ?? '—' }}</td>
                                        <td class="p-4 text-slate-500 dark:text-slate-500">{{ $official->sort_order }}</td>
                                        <td class="p-4">
                                            @if($official->is_active)
                                                <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-350 rounded text-[9px] font-extrabold uppercase tracking-wide font-display">Active</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-slate-150 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded text-[9px] font-extrabold uppercase tracking-wide font-display">Hidden</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->isAdmin())
                                            <td class="p-4 pr-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('officials.show', $official->slug) }}" target="_blank" class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-350 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">View</a>
                                                    <button
                                                        type="button"
                                                        @click="editOfficial({ id: {{ $official->id }}, name: '{{ addslashes($official->name) }}', position: '{{ addslashes($official->position) }}', bio: '{{ addslashes(str_replace(["\r", "\n"], ' ', $official->bio ?? '')) }}', email: '{{ addslashes($official->email ?? '') }}', contact_number: '{{ addslashes($official->contact_number ?? '') }}', term: '{{ addslashes($official->term ?? '') }}', sort_order: {{ $official->sort_order ?? 0 }}, is_active: {{ $official->is_active ? 1 : 0 }}, photo_url: '{{ $official->photoUrl() ?? '' }}' })"
                                                        class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                                    >
                                                        Edit
                                                    </button>
                                                    <x-alert-dialog>
                                                        <x-slot:trigger>
                                                            <button type="button" class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-955 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                                Delete
                                                            </button>
                                                        </x-slot:trigger>
                                                        <x-slot:icon>
                                                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                            </svg>
                                                        </x-slot:icon>
                                                        <x-slot:title>Delete Official</x-slot:title>
                                                        <x-slot:description>
                                                            Are you sure you want to permanently delete this official's profile? This action cannot be undone.
                                                        </x-slot:description>
                                                        <x-slot:footer>
                                                            <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                Cancel
                                                            </button>
                                                            <form method="POST" action="{{ route('admin.officials.destroy', $official->id) }}" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm border border-transparent">
                                                                    Confirm Delete
                                                                </button>
                                                            </form>
                                                        </x-slot:footer>
                                                    </x-alert-dialog>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($officials->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            {{ $officials->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Mobile FAB: Add Official --}}
        @if(Auth::user()->isAdmin())
            <div class="fixed bottom-0 inset-x-0 z-20 md:hidden pointer-events-none px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button
                    type="button"
                    @click="openAddOfficialModal = true"
                    class="pointer-events-auto w-full inline-flex items-center justify-center gap-2 min-h-[3.25rem] btn-primary text-xs font-bold uppercase tracking-wider shadow-lg shadow-blue-900/20 rounded-2xl"
                    aria-label="Add new official"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add Official Profile</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Add Official Modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openAddOfficialModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="add-official-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openAddOfficialModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openAddOfficialModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="openAddOfficialModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-3xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openAddOfficialModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">New Official</span>
                            <h2 id="add-official-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Add SK Official Profile</h2>
                        </div>
                        <button
                            type="button"
                            @click="openAddOfficialModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.officials.store') }}"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="official-name" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Full Name</label>
                                <input
                                    id="official-name"
                                    type="text"
                                    name="name"
                                    required
                                    placeholder="e.g. Hon. Juan dela Cruz"
                                    value="{{ old('name') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('name')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="official-position" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Position Role</label>
                                <input
                                    id="official-position"
                                    type="text"
                                    name="position"
                                    required
                                    placeholder="e.g. SK Chairperson"
                                    value="{{ old('position') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('position')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="official-email" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Email Address (Optional)</label>
                                <input
                                    id="official-email"
                                    type="email"
                                    name="email"
                                    placeholder="e.g. juan@sknamayan.gov.ph"
                                    value="{{ old('email') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('email')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="official-contact" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Contact Number (Optional)</label>
                                <input
                                    id="official-contact"
                                    type="text"
                                    name="contact_number"
                                    placeholder="e.g. +63 917 123 4567"
                                    value="{{ old('contact_number') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('contact_number')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1 md:col-span-2">
                                <label for="official-term" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Office Term Years (Optional)</label>
                                <input
                                    id="official-term"
                                    type="text"
                                    name="term"
                                    placeholder="e.g. 2023 - 2026"
                                    value="{{ old('term', '2023 - 2026') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('term')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="official-order" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Sort Order Ranking</label>
                                <input
                                    id="official-order"
                                    type="number"
                                    name="sort_order"
                                    placeholder="e.g. 1"
                                    value="{{ old('sort_order', 1) }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('sort_order')
                                    <span class="text-rose-650 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="official-bio" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Official Biography & Advocacy Summary</label>
                            <textarea
                                id="official-bio"
                                name="bio"
                                rows="3"
                                placeholder="Write a short background bio copy..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[5rem]"
                            >{{ old('bio') }}</textarea>
                            @error('bio')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-start gap-2.5 py-1">
                            <input
                                type="checkbox"
                                id="official-active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                            >
                            <div>
                                <label for="official-active" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Publish on Public Officials Roster</label>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Allows citizens to view contact and bio information on public directory.</span>
                            </div>
                        </div>

                        {{-- Drag and drop photo uploader at the bottom, 96% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2 border-t border-slate-150 dark:border-slate-800">
                            <label for="official-photo" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Official Profile Photo</label>
                            <x-file-upload name="photo" id="official-photo" required="true" accept="image/*" placeholder="Drag photo image here or click to browse." />
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-normal">Supports JPEG, PNG, WEBP. Max size: 4MB. Recommended: 1:1 square ratio.</span>
                            @error('photo')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Sticky footer actions --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openAddOfficialModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Save Official
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Edit Official Modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openEditOfficialModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-official-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openEditOfficialModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openEditOfficialModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="openEditOfficialModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-3xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openEditOfficialModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Modify Official</span>
                            <h2 id="edit-official-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Edit SK Official Profile</h2>
                        </div>
                        <button
                            type="button"
                            @click="openEditOfficialModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form
                        method="POST"
                        :action="editFormAction"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="edit-official-name" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Full Name</label>
                                <input
                                    id="edit-official-name"
                                    type="text"
                                    name="name"
                                    required
                                    x-model="editOfficialData.name"
                                    placeholder="e.g. Hon. Juan dela Cruz"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-official-position" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Position Role</label>
                                <input
                                    id="edit-official-position"
                                    type="text"
                                    name="position"
                                    required
                                    x-model="editOfficialData.position"
                                    placeholder="e.g. SK Chairperson"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="edit-official-email" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Email Address (Optional)</label>
                                <input
                                    id="edit-official-email"
                                    type="email"
                                    name="email"
                                    x-model="editOfficialData.email"
                                    placeholder="e.g. juan@sknamayan.gov.ph"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-official-contact" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Contact Number (Optional)</label>
                                <input
                                    id="edit-official-contact"
                                    type="text"
                                    name="contact_number"
                                    x-model="editOfficialData.contact_number"
                                    placeholder="e.g. +63 917 123 4567"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1 md:col-span-2">
                                <label for="edit-official-term" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Office Term Years (Optional)</label>
                                <input
                                    id="edit-official-term"
                                    type="text"
                                    name="term"
                                    x-model="editOfficialData.term"
                                    placeholder="e.g. 2023 - 2026"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-official-order" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Sort Order Ranking</label>
                                <input
                                    id="edit-official-order"
                                    type="number"
                                    name="sort_order"
                                    x-model="editOfficialData.sort_order"
                                    placeholder="e.g. 1"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-official-bio" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Official Biography & Advocacy Summary</label>
                            <textarea
                                id="edit-official-bio"
                                name="bio"
                                rows="3"
                                x-model="editOfficialData.bio"
                                placeholder="Write a short background bio copy..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[5rem]"
                            ></textarea>
                        </div>

                        <div class="flex items-start gap-2.5 py-1">
                            <input
                                type="checkbox"
                                id="edit-official-active"
                                name="is_active"
                                value="1"
                                x-model="editOfficialData.is_active"
                                class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                            >
                            <div>
                                <label for="edit-official-active" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Publish on Public Officials Roster</label>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Allows citizens to view contact and bio information on public directory.</span>
                            </div>
                        </div>

                        {{-- Drag and drop photo uploader at the bottom, 96% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2 border-t border-slate-150 dark:border-slate-800">
                            <label for="edit-official-photo" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Official Profile Photo (Optional)</label>
                            <x-file-upload name="photo" id="edit-official-photo" required="false" accept="image/*" placeholder="Drag a new photo here or click to browse." />
                            <div class="mt-2 flex items-center gap-3">
                                <div class="w-12 h-12 rounded overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-center">
                                    <template x-if="editOfficialData.photo_url">
                                        <img :src="editOfficialData.photo_url" class="w-full h-full object-cover" alt="Current Photo">
                                    </template>
                                    <template x-if="!editOfficialData.photo_url">
                                        <span class="text-slate-300 text-xs">None</span>
                                    </template>
                                </div>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight">Currently active photo. Leave empty to keep it.</span>
                            </div>
                        </div>

                        {{-- Sticky footer actions --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openEditOfficialModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

</div>

{{-- Mobile interaction helpers --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('officialsAdmin', (config = {}) => ({
            openAddOfficialModal: config.openOnLoad ?? false,
            openEditOfficialModal: false,
            editOfficialData: { id: null, name: '', position: '', bio: '', email: '', contact_number: '', term: '', sort_order: 1, is_active: true, photo_url: '' },
            editFormAction: '',

            init() {
                if (this.openAddOfficialModal) {
                    this.lockBodyScroll(true);
                }

                this.$watch('openAddOfficialModal', (open) => {
                    this.lockBodyScroll(open);
                });

                this.$watch('openEditOfficialModal', (open) => {
                    this.lockBodyScroll(open);
                });
            },

            editOfficial(official) {
                this.editOfficialData = {
                    id: official.id,
                    name: official.name,
                    position: official.position,
                    bio: official.bio,
                    email: official.email,
                    contact_number: official.contact_number,
                    term: official.term,
                    sort_order: official.sort_order,
                    is_active: !!official.is_active,
                    photo_url: official.photo_url
                };
                this.editFormAction = `/admin/officials/${official.id}`;
                this.openEditOfficialModal = true;
            },

            lockBodyScroll(locked) {
                document.documentElement.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('touch-none', locked);
            },
        }));
    });
</script>
@endsection
