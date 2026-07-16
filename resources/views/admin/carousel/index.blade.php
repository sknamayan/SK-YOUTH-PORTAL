@extends('layouts.app')

@section('content')
<div
    x-data="carouselAdmin({
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
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">Hero Slides</li>
                </ol>
            </nav>

            {{-- Page header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1 min-w-0">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Landing Customization</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Hero Carousel Slides</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Manage visual image slides shown at the top of the citizen landing page.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <button
                        type="button"
                        @click="openAddSlideModal = true"
                        class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add Carousel Slide</span>
                    </button>
                @endif
            </div>

            @if(!Auth::user()->isAdmin())
                <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 text-amber-800 dark:text-amber-200 p-4 rounded-2xl text-xs shadow-sm flex items-start gap-3" role="status">
                    <span class="text-base shrink-0" aria-hidden="true">🔒</span>
                    <p><strong>View Only Mode:</strong> Creation and deletion of landing page hero slides are restricted to Administrator accounts.</p>
                </div>
            @endif

            {{-- Slides container --}}
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">

                @if($slides->isEmpty())
                    <div class="text-center py-12 md:py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">No Custom Slides Uploaded</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">The landing page will display the default system slides (Study Hub, SIKLAB, Youth Projects) as a fallback.</p>
                        </div>
                    </div>
                @else

                    {{-- Mobile: native-style card list --}}
                    <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800" role="list" aria-label="Carousel slides">
                        @foreach($slides as $slide)
                            <li 
                                @if(Auth::user()->isAdmin())
                                    draggable="true"
                                    data-id="{{ $slide->id }}"
                                    @dragstart="dragStart($event)"
                                    @dragover.prevent="dragOver($event)"
                                    @drop="drop($event)"
                                    @dragend="dragEnd($event)"
                                    @touchstart="touchStart($event)"
                                    @touchmove="touchMove($event)"
                                    @touchend="touchEnd($event)"
                                    class="p-4 space-y-3 cursor-grab active:cursor-grabbing transition duration-150"
                                @else
                                    class="p-4 space-y-3"
                                @endif
                            >
                                {{-- Preview + title row --}}
                                <div class="flex gap-3 min-w-0">
                                    <div class="w-28 h-[4.5rem] shrink-0 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                        <img
                                            src="{{ asset('storage/' . $slide->image_path) }}"
                                            class="w-full h-full object-cover"
                                            alt="Preview of {{ $slide->title }}"
                                            loading="lazy"
                                        >
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug line-clamp-2">{{ $slide->title }}</h3>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">{{ $slide->description }}</p>
                                    </div>
                                </div>

                                {{-- CTA badge --}}
                                <div class="flex flex-wrap gap-2 min-w-0">
                                    <span class="inline-flex items-center max-w-full px-2.5 py-1 bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900 text-[#1e40af] dark:text-blue-300 rounded-lg text-[10px] font-bold font-mono truncate">
                                        <span class="truncate">{{ $slide->cta_text }}</span>
                                        <span class="text-slate-400 dark:text-slate-500 mx-1 shrink-0">·</span>
                                        <span class="truncate text-slate-500 dark:text-slate-400 font-normal">{{ $slide->cta_url }}</span>
                                    </span>
                                </div>

                                {{-- Expand details toggle --}}
                                <button
                                    type="button"
                                    @click="toggleSlide({{ $slide->id }})"
                                    class="w-full flex items-center justify-between min-h-11 px-3 -mx-1 rounded-xl text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition"
                                    :aria-expanded="expandedSlideId === {{ $slide->id }}"
                                    aria-controls="slide-details-{{ $slide->id }}"
                                >
                                    <span x-text="expandedSlideId === {{ $slide->id }} ? 'Hide Details' : 'View Full Details'"></span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': expandedSlideId === {{ $slide->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div
                                    id="slide-details-{{ $slide->id }}"
                                    x-show="expandedSlideId === {{ $slide->id }}"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    x-cloak
                                    class="space-y-2"
                                >
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed break-words">{{ $slide->description }}</p>
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <div class="pt-1 border-t border-slate-100 dark:border-slate-800 flex gap-2">
                                        <button
                                            type="button"
                                            @click="editSlide({ id: {{ $slide->id }}, title: '{{ addslashes($slide->title) }}', description: '{{ addslashes($slide->description) }}', cta_text: '{{ addslashes($slide->cta_text) }}', cta_url: '{{ addslashes($slide->cta_url) }}', image_url: '{{ asset('storage/' . $slide->image_path) }}' })"
                                            class="flex-1 inline-flex items-center justify-center min-h-11 px-4 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-xl transition text-[10px] uppercase tracking-wider active:scale-[0.98] border border-blue-100 dark:border-blue-900/60"
                                        >
                                            Edit Slide
                                        </button>
                                        <x-alert-dialog>
                                            <x-slot:trigger>
                                                <button
                                                    type="button"
                                                    class="w-full inline-flex items-center justify-center min-h-11 px-4 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-950/70 font-bold rounded-xl transition text-[10px] uppercase tracking-wider active:scale-[0.98] border border-rose-100 dark:border-rose-900/60"
                                                >
                                                    Delete Slide
                                                </button>
                                            </x-slot:trigger>

                                            <x-slot:icon>
                                                <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            </x-slot:icon>

                                            <x-slot:title>Delete Carousel Slide</x-slot:title>

                                            <x-slot:description>
                                                Are you sure you want to permanently delete "{{ $slide->title }}"? This will remove it from the public homepage. This action cannot be undone.
                                            </x-slot:description>

                                            <x-slot:footer>
                                                <button type="button" @click="open = false" class="btn-outline text-xs py-2.5 px-4 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                    Cancel
                                                </button>
                                                <form method="POST" action="{{ route('admin.carousel.destroy', $slide->id) }}" class="w-full sm:w-auto">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm min-h-11">
                                                        Confirm Delete
                                                    </button>
                                                </form>
                                            </x-slot:footer>
                                        </x-alert-dialog>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    {{-- Desktop: data table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Preview</th>
                                    <th class="p-4">Header Description</th>
                                    <th class="p-4">Sub Description</th>
                                    <th class="p-4">CTA Action</th>
                                    @if(Auth::user()->isAdmin())
                                        <th class="p-4 pr-6 text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                @foreach($slides as $slide)
                                    <tr 
                                        @if(Auth::user()->isAdmin())
                                            draggable="true"
                                            data-id="{{ $slide->id }}"
                                            @dragstart="dragStart($event)"
                                            @dragover.prevent="dragOver($event)"
                                            @drop="drop($event)"
                                            @dragend="dragEnd($event)"
                                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150 cursor-grab active:cursor-grabbing"
                                        @else
                                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150"
                                        @endif
                                    >
                                        <td class="p-4 pl-6">
                                            <div class="w-24 h-14 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                                                <img src="{{ asset('storage/' . $slide->image_path) }}" class="w-full h-full object-cover" alt="Slide preview">
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800 dark:text-slate-100 max-w-xs">
                                            {{ $slide->title }}
                                        </td>
                                        <td class="p-4 text-slate-500 dark:text-slate-400 max-w-sm leading-relaxed">
                                            <span class="line-clamp-2">{{ Str::limit($slide->description, 120) }}</span>
                                        </td>
                                        <td class="p-4 text-slate-500 dark:text-slate-400 max-w-xs">
                                            <span class="inline-flex max-w-full px-2 py-0.5 bg-blue-50 dark:bg-blue-950/50 border border-blue-100 dark:border-blue-900 text-[#1e40af] dark:text-blue-300 rounded text-[10px] font-bold font-mono truncate">
                                                {{ $slide->cta_text }} ({{ $slide->cta_url }})
                                            </span>
                                        </td>
                                        @if(Auth::user()->isAdmin())
                                            <td class="p-4 pr-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        @click="editSlide({ id: {{ $slide->id }}, title: '{{ addslashes($slide->title) }}', description: '{{ addslashes($slide->description) }}', cta_text: '{{ addslashes($slide->cta_text) }}', cta_url: '{{ addslashes($slide->cta_url) }}', image_url: '{{ asset('storage/' . $slide->image_path) }}' })"
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

                                                    <x-slot:title>Delete Carousel Slide</x-slot:title>

                                                    <x-slot:description>
                                                        Are you sure you want to permanently delete "{{ $slide->title }}"? This will remove it from the public homepage. This action cannot be undone.
                                                    </x-slot:description>

                                                    <x-slot:footer>
                                                        <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                            Cancel
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.carousel.destroy', $slide->id) }}" class="inline">
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
                @endif
            </div>
        </div>

        {{-- Mobile FAB: Add slide --}}
        @if(Auth::user()->isAdmin())
            <div class="fixed bottom-0 inset-x-0 z-20 md:hidden pointer-events-none px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button
                    type="button"
                    @click="openAddSlideModal = true"
                    class="pointer-events-auto w-full inline-flex items-center justify-center gap-2 min-h-[3.25rem] btn-primary text-xs font-bold uppercase tracking-wider shadow-lg shadow-blue-900/20 rounded-2xl"
                    aria-label="Add new carousel slide"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add Carousel Slide</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Add Slide: bottom sheet (mobile) / centered modal (desktop) --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openAddSlideModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="add-slide-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openAddSlideModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openAddSlideModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Sheet / modal panel --}}
                <div
                    x-show="openAddSlideModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-2xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openAddSlideModal = false"
                >
                    {{-- Drag handle (mobile) --}}
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">New Slide</span>
                            <h2 id="add-slide-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Add Carousel Slide</h2>
                        </div>
                        <button
                            type="button"
                            @click="openAddSlideModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close add slide dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable form body --}}
                    <form
                        method="POST"
                        action="{{ route('admin.carousel.store') }}"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf

                        <div class="space-y-1">
                            <label for="slide-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Header Description (Title)</label>
                            <input
                                id="slide-title"
                                type="text"
                                name="title"
                                required
                                placeholder="Enter slide main title text..."
                                value="{{ old('title') }}"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                            >
                            @error('title')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="slide-description" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Sub Description (Paragraph)</label>
                            <textarea
                                id="slide-description"
                                name="description"
                                required
                                rows="3"
                                placeholder="Enter supporting sub-description copy shown below the header..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[5.5rem]"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="slide-cta-text" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">CTA Button Text (Optional)</label>
                                <input
                                    id="slide-cta-text"
                                    type="text"
                                    name="cta_text"
                                    placeholder="Apply Now"
                                    value="{{ old('cta_text') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="slide-cta-url" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">CTA Action Link (Optional)</label>
                                <input
                                    id="slide-cta-url"
                                    type="text"
                                    name="cta_url"
                                    placeholder="/forms/silid-karunungan or #"
                                    value="{{ old('cta_url') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>
                        </div>

                        {{-- Drag and drop uploader at the bottom, 96% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2">
                            <label for="slide-image" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Slide Background Image</label>
                            <x-file-upload name="image" id="slide-image" required="true" accept="image/jpeg,image/png,image/webp,image/gif" placeholder="Drag your background slide image here or click to browse." />
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-relaxed">Recommended ratio: <strong class="text-blue-600 dark:text-blue-400 font-bold">16:9</strong> (e.g., 1920x1080px). Max size: 4MB. Supported: JPG, PNG, WEBP, GIF.</span>
                            @error('image')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Sticky footer actions inside sheet --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openAddSlideModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Upload Slide
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Edit Slide Modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openEditSlideModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-slide-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openEditSlideModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openEditSlideModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Sheet / modal panel --}}
                <div
                    x-show="openEditSlideModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-2xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openEditSlideModal = false"
                >
                    {{-- Drag handle (mobile) --}}
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Modify Slide</span>
                            <h2 id="edit-slide-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Edit Carousel Slide</h2>
                        </div>
                        <button
                            type="button"
                            @click="openEditSlideModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close edit slide dialog"
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

                        <div class="space-y-1">
                            <label for="edit-slide-title-input" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Header Description (Title)</label>
                            <input
                                id="edit-slide-title-input"
                                type="text"
                                name="title"
                                required
                                x-model="editSlideData.title"
                                placeholder="Enter slide main title text..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                            >
                        </div>

                        <div class="space-y-1">
                            <label for="edit-slide-description" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Sub Description (Paragraph)</label>
                            <textarea
                                id="edit-slide-description"
                                name="description"
                                required
                                rows="3"
                                x-model="editSlideData.description"
                                placeholder="Enter supporting sub-description copy shown below the header..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[5.5rem]"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="edit-slide-cta-text" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">CTA Button Text (Optional)</label>
                                <input
                                    id="edit-slide-cta-text"
                                    type="text"
                                    name="cta_text"
                                    x-model="editSlideData.cta_text"
                                    placeholder="Apply Now"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-slide-cta-url" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">CTA Action Link (Optional)</label>
                                <input
                                    id="edit-slide-cta-url"
                                    type="text"
                                    name="cta_url"
                                    x-model="editSlideData.cta_url"
                                    placeholder="/forms/silid-karunungan or #"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>
                        </div>

                        {{-- Drag and drop uploader at the bottom, 96% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2">
                            <label for="edit-slide-image" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Change Background Image (Optional)</label>
                            <x-file-upload name="image" id="edit-slide-image" required="false" accept="image/jpeg,image/png,image/webp,image/gif" placeholder="Drag a new image here or click to browse." />
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-relaxed">Recommended ratio: <strong class="text-blue-600 dark:text-blue-400 font-bold">16:9</strong> (e.g., 1920x1080px). Max size: 4MB. Supported: JPG, PNG, WEBP, GIF.</span>
                            <div class="mt-2 flex items-center gap-3">
                                <div class="w-16 h-10 rounded overflow-hidden border border-slate-200 dark:border-slate-800 shrink-0 bg-slate-100 dark:bg-slate-950">
                                    <img :src="editSlideData.image_url" class="w-full h-full object-cover" alt="Current slide image">
                                </div>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight">Currently active image. Leave empty to keep it.</span>
                            </div>
                        </div>

                        {{-- Sticky footer actions inside sheet --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openEditSlideModal = false"
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
        Alpine.data('carouselAdmin', (config = {}) => ({
            openAddSlideModal: config.openOnLoad ?? false,
            openEditSlideModal: false,
            expandedSlideId: null,
            _touchStartY: null,
            editSlideData: { id: null, title: '', description: '', cta_text: '', cta_url: '', image_url: '' },
            editFormAction: '',

            init() {
                // Re-open modal after validation errors
                if (this.openAddSlideModal) {
                    this.lockBodyScroll(true);
                }

                this.$watch('openAddSlideModal', (open) => {
                    this.lockBodyScroll(open);
                });

                this.$watch('openEditSlideModal', (open) => {
                    this.lockBodyScroll(open);
                });
            },

            editSlide(slide) {
                this.editSlideData = {
                    id: slide.id,
                    title: slide.title,
                    description: slide.description,
                    cta_text: slide.cta_text,
                    cta_url: slide.cta_url,
                    image_url: slide.image_url
                };
                this.editFormAction = `/admin/carousel/${slide.id}`;
                this.openEditSlideModal = true;
            },

            // Drag and Drop (Desktop)
            draggedElement: null,

            dragStart(event) {
                this.draggedElement = event.currentTarget;
                event.dataTransfer.effectAllowed = 'move';
                event.currentTarget.classList.add('opacity-40', 'bg-blue-50/20');
            },

            dragOver(event) {
                const target = event.currentTarget;
                if (target && target !== this.draggedElement) {
                    const rect = target.getBoundingClientRect();
                    const next = (event.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                    target.parentNode.insertBefore(this.draggedElement, next ? target.nextSibling : target);
                }
            },

            drop(event) {
                event.preventDefault();
            },

            dragEnd(event) {
                event.currentTarget.classList.remove('opacity-40', 'bg-blue-50/20');
                this.saveOrder();
            },

            // Touch drag-and-drop (Mobile)
            _touchStartY: null,
            _activeTouchItem: null,

            touchStart(event) {
                if (event.target.closest('button, a, form')) return;
                this._touchStartY = event.touches[0].clientY;
                this._activeTouchItem = event.currentTarget;
                this._activeTouchItem.classList.add('bg-blue-50/20');
            },

            touchMove(event) {
                if (!this._activeTouchItem) return;
                const clientY = event.touches[0].clientY;
                const target = document.elementFromPoint(event.touches[0].clientX, clientY);
                const item = target ? target.closest('li[data-id]') : null;
                
                if (item && item !== this._activeTouchItem) {
                    event.preventDefault(); // Stop native scrolling
                    const rect = item.getBoundingClientRect();
                    const next = (clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                    item.parentNode.insertBefore(this._activeTouchItem, next ? item.nextSibling : item);
                }
            },

            touchEnd(event) {
                if (this._activeTouchItem) {
                    this._activeTouchItem.classList.remove('bg-blue-50/20');
                    this._activeTouchItem = null;
                    this.saveOrder();
                }
            },

            saveOrder() {
                // Determine whether to scan mobile LI or desktop TR list
                const selector = window.innerWidth < 768 ? 'ul[aria-label="Carousel slides"] li[data-id]' : 'tbody tr[data-id]';
                const rows = Array.from(document.querySelectorAll(selector));
                const ids = rows.map(row => row.getAttribute('data-id'));
                
                fetch('{{ route('admin.carousel.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    // Success silently re-indexes in database
                });
            },

            toggleSlide(id) {
                this.expandedSlideId = this.expandedSlideId === id ? null : id;
            },

            lockBodyScroll(locked) {
                document.documentElement.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('touch-none', locked);
            },
        }));
    });

    // Reduce iOS rubber-band overscroll on fixed UI while allowing inner scroll areas
    (function () {
        var lastY = 0;

        document.addEventListener('touchstart', function (e) {
            if (e.touches.length !== 1) return;
            lastY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchmove', function (e) {
            var target = e.target.closest('[data-overscroll-lock], .overflow-y-auto, .overscroll-y-contain');
            if (!target) return;

            if (target.dataset.overscrollLock === 'true') {
                e.preventDefault();
                return;
            }

            var scrollable = target;
            var atTop = scrollable.scrollTop <= 0;
            var atBottom = scrollable.scrollTop + scrollable.clientHeight >= scrollable.scrollHeight - 1;
            var currentY = e.touches[0].clientY;
            var pullingDown = currentY > lastY;
            var pullingUp = currentY < lastY;

            if ((atTop && pullingDown) || (atBottom && pullingUp)) {
                e.preventDefault();
            }
        }, { passive: false });
    })();
</script>
@endsection
