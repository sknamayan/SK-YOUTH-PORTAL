@extends('layouts.app')

@section('content')
<div
    x-data="partnersAdmin({
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
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">Sponsor Partnerships</li>
                </ol>
            </nav>

            {{-- Page header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1 min-w-0">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Branding Manager</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Sponsor Partnerships</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Add and manage active sponsor logos shown on the landing page footer slider.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <button
                        type="button"
                        @click="openAddSponsorModal = true"
                        class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add Sponsor Partner</span>
                    </button>
                @endif
            </div>

            {{-- Sponsors Container --}}
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">

                @if($partners->isEmpty())
                    <div class="text-center py-12 md:py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">No Sponsors Added</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">Upload partner/sponsor logos to showcase them dynamically on the public landing page.</p>
                        </div>
                    </div>
                @else

                    {{-- Mobile Roster --}}
                    <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800" role="list" aria-label="Sponsors">
                        @foreach($partners as $partner)
                            <li class="p-4 space-y-3">
                                <div class="flex gap-3 min-w-0">
                                    <div class="w-20 h-12 shrink-0 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden flex items-center justify-center p-1">
                                        <img
                                            src="{{ asset('storage/' . $partner->logo_path) }}"
                                            class="max-w-full max-h-full object-contain"
                                            alt="{{ $partner->name }} logo"
                                            loading="lazy"
                                        >
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug truncate">{{ $partner->name }}</h3>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate">
                                            @if($partner->website_url)
                                                <a href="{{ $partner->website_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                    {{ $partner->website_url }}
                                                </a>
                                            @else
                                                <span class="italic text-slate-300 dark:text-slate-700">No Link</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-2 min-w-0">
                                    @if($partner->is_active)
                                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 rounded text-[9px] font-extrabold uppercase tracking-wide">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-150 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded text-[9px] font-extrabold uppercase tracking-wide">Inactive</span>
                                    @endif

                                    @if(Auth::user()->isAdmin())
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="editSponsor({ id: {{ $partner->id }}, name: '{{ addslashes($partner->name) }}', website_url: '{{ addslashes($partner->website_url ?? '') }}', is_active: {{ $partner->is_active ? 1 : 0 }}, logo_url: '{{ asset('storage/' . $partner->logo_path) }}' })"
                                                class="inline-flex items-center min-h-9 px-3 py-1 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                            >
                                                Edit
                                            </button>
                                            <x-alert-dialog>
                                                <x-slot:trigger>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center min-h-9 px-3 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                                    >
                                                        Delete
                                                    </button>
                                                </x-slot:trigger>

                                                <x-slot:icon>
                                                    <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </x-slot:icon>

                                                <x-slot:title>Delete Sponsor</x-slot:title>

                                                <x-slot:description>
                                                    Are you sure you want to permanently delete "{{ $partner->name }}"? This will remove it from the public homepage. This action cannot be undone.
                                                </x-slot:description>

                                                <x-slot:footer>
                                                    <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.partners.destroy', $partner->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm border border-transparent">
                                                            Confirm Delete
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

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Sponsor Logo</th>
                                    <th class="p-4">Name</th>
                                    <th class="p-4">Website Link</th>
                                    <th class="p-4">Status</th>
                                    @if(Auth::user()->isAdmin())
                                        <th class="p-4 pr-6 text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                @foreach($partners as $partner)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="p-4 pl-6">
                                            <div class="w-16 h-10 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700 p-1 flex items-center justify-center overflow-hidden shrink-0">
                                                <img src="{{ asset('storage/' . $partner->logo_path) }}" class="max-w-full max-h-full object-contain" alt="Logo preview">
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800 dark:text-slate-100">
                                            {{ $partner->name }}
                                        </td>
                                        <td class="p-4 font-mono text-slate-400 dark:text-slate-500 select-all max-w-xs truncate">
                                            @if($partner->website_url)
                                                <a href="{{ $partner->website_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                                    <span>{{ $partner->website_url }}</span>
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-700 italic">None</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($partner->is_active)
                                                <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 rounded text-[9px] font-extrabold uppercase tracking-wide font-display">Active</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-slate-150 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded text-[9px] font-extrabold uppercase tracking-wide font-display">Inactive</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->isAdmin())
                                            <td class="p-4 pr-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        @click="editSponsor({ id: {{ $partner->id }}, name: '{{ addslashes($partner->name) }}', website_url: '{{ addslashes($partner->website_url ?? '') }}', is_active: {{ $partner->is_active ? 1 : 0 }}, logo_url: '{{ asset('storage/' . $partner->logo_path) }}' })"
                                                        class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                                    >
                                                        Edit
                                                    </button>
                                                    <x-alert-dialog>
                                                        <x-slot:trigger>
                                                            <button type="button" class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                                Delete
                                                            </button>
                                                        </x-slot:trigger>

                                                        <x-slot:icon>
                                                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                            </svg>
                                                        </x-slot:icon>

                                                        <x-slot:title>Delete Sponsor</x-slot:title>

                                                        <x-slot:description>
                                                            Are you sure you want to permanently delete "{{ $partner->name }}"? This will remove it from the public homepage. This action cannot be undone.
                                                        </x-slot:description>

                                                        <x-slot:footer>
                                                            <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                Cancel
                                                            </button>
                                                            <form method="POST" action="{{ route('admin.partners.destroy', $partner->id) }}" class="inline">
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
                    @if($partners->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            {{ $partners->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Mobile FAB: Add sponsor --}}
        @if(Auth::user()->isAdmin())
            <x-mobile-bottom-action @click="openAddSponsorModal = true">
                Add Sponsor Partner
            </x-mobile-bottom-action>
        @endif
    </div>

    {{-- Add Sponsor: bottom sheet (mobile) / centered modal (desktop) --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openAddSponsorModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="add-sponsor-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openAddSponsorModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openAddSponsorModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Sheet / modal panel --}}
                <div
                    x-show="openAddSponsorModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-2xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openAddSponsorModal = false"
                >
                    {{-- Drag handle (mobile) --}}
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">New Sponsor</span>
                            <h2 id="add-sponsor-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Add Sponsor Partner</h2>
                        </div>
                        <button
                            type="button"
                            @click="openAddSponsorModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable form body --}}
                    <form
                        method="POST"
                        action="{{ route('admin.partners.store') }}"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1 md:col-span-2">
                                <label for="sponsor-name" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Sponsor / Partner Name</label>
                                <input
                                    id="sponsor-name"
                                    type="text"
                                    name="name"
                                    required
                                    placeholder="e.g. Lenovo Corporation"
                                    value="{{ old('name') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('name')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="sponsor-status" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Status Label</label>
                                <select id="sponsor-status" name="is_active" class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="sponsor-url" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Website URL (Optional)</label>
                            <input
                                id="sponsor-url"
                                type="url"
                                name="website_url"
                                placeholder="e.g. https://lenovo.com"
                                value="{{ old('website_url') }}"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                            >
                            @error('website_url')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Drag and drop logo uploader at the bottom, 95% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2">
                            <label for="sponsor-logo" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Logo Image</label>
                            <x-file-upload name="logo" id="sponsor-logo" required="true" accept="image/*" placeholder="Drag the logo image here or click to browse." />
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-relaxed">Supports PNG, JPG, JPEG, SVG or WebP. Max size: 2MB.</span>
                            @error('logo')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Sticky footer actions inside sheet --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openAddSponsorModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Add Sponsor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Edit Sponsor: bottom sheet (mobile) / centered modal (desktop) --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openEditSponsorModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-sponsor-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openEditSponsorModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openEditSponsorModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Sheet / modal panel --}}
                <div
                    x-show="openEditSponsorModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-2xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openEditSponsorModal = false"
                >
                    {{-- Drag handle (mobile) --}}
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Modify Sponsor</span>
                            <h2 id="edit-sponsor-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Edit Sponsor Partner</h2>
                        </div>
                        <button
                            type="button"
                            @click="openEditSponsorModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable form body --}}
                    <form
                        method="POST"
                        :action="editFormAction"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1 md:col-span-2">
                                <label for="edit-sponsor-name" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Sponsor / Partner Name</label>
                                <input
                                    id="edit-sponsor-name"
                                    type="text"
                                    name="name"
                                    required
                                    x-model="editSponsorData.name"
                                    placeholder="e.g. Lenovo Corporation"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-sponsor-status" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Status Label</label>
                                <select id="edit-sponsor-status" name="is_active" x-model="editSponsorData.is_active" class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-sponsor-url" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Website URL (Optional)</label>
                            <input
                                id="edit-sponsor-url"
                                type="url"
                                name="website_url"
                                x-model="editSponsorData.website_url"
                                placeholder="e.g. https://lenovo.com"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                            >
                        </div>

                        {{-- Drag and drop logo uploader at the bottom, 95% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2">
                            <label for="edit-sponsor-logo" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Logo Image (Optional)</label>
                            <x-file-upload name="logo" id="edit-sponsor-logo" required="false" accept="image/*" placeholder="Drag a new logo here or click to browse." />
                            <div class="mt-2 flex items-center gap-3">
                                <div class="w-16 h-10 rounded overflow-hidden border border-slate-200 dark:border-slate-800 shrink-0 bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-1">
                                    <img :src="editSponsorData.logo_url" class="max-w-full max-h-full object-contain" alt="Current sponsor logo">
                                </div>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight">Currently active logo. Leave empty to keep it.</span>
                            </div>
                        </div>

                        {{-- Sticky footer actions inside sheet --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openEditSponsorModal = false"
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
        Alpine.data('partnersAdmin', (config = {}) => ({
            openAddSponsorModal: config.openOnLoad ?? false,
            openEditSponsorModal: false,
            editSponsorData: { id: null, name: '', website_url: '', is_active: '1', logo_url: '' },
            editFormAction: '',

            init() {
                if (this.openAddSponsorModal) {
                    this.lockBodyScroll(true);
                }

                this.$watch('openAddSponsorModal', (open) => {
                    this.lockBodyScroll(open);
                });

                this.$watch('openEditSponsorModal', (open) => {
                    this.lockBodyScroll(open);
                });
            },

            editSponsor(partner) {
                this.editSponsorData = {
                    id: partner.id,
                    name: partner.name,
                    website_url: partner.website_url,
                    is_active: String(partner.is_active),
                    logo_url: partner.logo_url
                };
                this.editFormAction = `/admin/partners/${partner.id}`;
                this.openEditSponsorModal = true;
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
