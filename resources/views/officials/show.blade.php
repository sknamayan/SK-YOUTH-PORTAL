@extends('layouts.app')

@section('content')
<div x-data="governanceMobile()" class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-950 font-sans pb-[max(5rem,env(safe-area-inset-bottom))] md:pb-10">

    {{-- Sticky mobile toolbar --}}
    <div class="sticky top-16 z-20 md:hidden bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center justify-between px-3 h-12">
            <a href="{{ route('officials.index') }}" class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-600 dark:text-slate-300 active:scale-95 transition-all" aria-label="Back to all officials">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate px-2">Official Profile</span>
            @if($official->email || $official->contact_number)
                <button type="button" @click="openSheet()" class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl bg-[#1e40af] text-white active:scale-95 transition-all shadow-sm" aria-label="Contact {{ $official->name }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </button>
            @else
                <div class="w-11"></div>
            @endif
        </div>
    </div>

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-[#1e40af] via-[#1e3a8a] to-[#172554] text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-8 py-8 md:py-12">
            <nav aria-label="Breadcrumb" class="hidden md:flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-blue-200 mb-6 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white shrink-0">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('officials.index') }}" class="hover:text-white shrink-0">Officials</a>
                <span aria-hidden="true">/</span>
                <span class="truncate" aria-current="page">{{ $official->name }}</span>
            </nav>

            <div class="flex flex-col items-center md:items-start md:flex-row gap-6">
                <div class="shrink-0 w-28 h-28 md:w-36 md:h-36 rounded-3xl overflow-hidden border-4 border-white/25 shadow-2xl ring-4 ring-white/10">
                    @if($official->photoUrl())
                        <img src="{{ $official->photoUrl() }}" alt="{{ $official->name }}" class="w-full h-full object-cover object-top">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-white/10">
                            <span class="text-3xl md:text-4xl font-black font-display">{{ $official->initials() }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-center md:text-left space-y-2 min-w-0 flex-1">
                    <span class="inline-flex px-3 py-1 rounded-full bg-white/10 border border-white/20 text-[10px] font-black uppercase tracking-widest backdrop-blur-sm">{{ $official->position }}</span>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-black font-display uppercase tracking-tight leading-tight">{{ $official->name }}</h1>
                    @if($official->term)
                        <p class="text-sm text-blue-200 font-semibold">Term of Office: {{ $official->term }}</p>
                    @endif
                    <div class="hidden md:flex flex-wrap gap-3 pt-2">
                        @if($official->email)
                            <a href="mailto:{{ $official->email }}" class="inline-flex items-center gap-2 min-h-11 px-4 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold active:scale-95 transition-all">Email</a>
                        @endif
                        @if($official->contact_number)
                            <a href="tel:{{ $official->contact_number }}" class="inline-flex items-center gap-2 min-h-11 px-4 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold active:scale-95 transition-all">Call</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-4xl mx-auto w-full px-4 sm:px-8 py-6 md:py-12 space-y-5 md:space-y-8">
        @if($official->bio)
            <article class="rounded-2xl md:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 md:p-6 space-y-3">
                <h2 class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest font-display">About</h2>
                <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $official->bio }}</div>
            </article>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Position</span>
                <p class="text-sm font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $official->position }}</p>
            </div>
            @if($official->term)
                <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 md:p-5 shadow-sm">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Term</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 mt-1">{{ $official->term }}</p>
                </div>
            @endif
        </div>

        @if($otherOfficials->isNotEmpty())
            <div class="space-y-3 pt-2">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest font-display px-1">Other Officials</h2>
                <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory overscroll-x-contain pb-2 -mx-1 px-1 md:grid md:grid-cols-4 md:overflow-visible md:snap-none">
                    @foreach($otherOfficials as $other)
                        <a href="{{ route('officials.show', $other->slug) }}"
                           class="snap-start shrink-0 w-[7.5rem] md:w-auto text-center p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm active:scale-95 transition-all duration-200">
                            <div class="w-14 h-14 mx-auto rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 mb-2">
                                @if($other->photoUrl())
                                    <img src="{{ $other->photoUrl() }}" alt="" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-[#1e40af] text-white text-xs font-black">{{ $other->initials() }}</div>
                                @endif
                            </div>
                            <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 uppercase leading-tight line-clamp-2">{{ $other->name }}</p>
                            <p class="text-[9px] text-slate-400 mt-0.5 truncate">{{ $other->position }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="hidden md:flex gap-3">
            <a href="{{ route('officials.index') }}" class="btn-outline text-xs min-h-11 inline-flex items-center justify-center active:scale-95">← All Officials</a>
            <a href="{{ route('transparency.index') }}" class="btn-primary text-xs min-h-11 inline-flex items-center justify-center active:scale-95">Transparency Board</a>
        </div>
    </section>

    {{-- Mobile contact bottom sheet --}}
    @if($official->email || $official->contact_number)
        <template x-teleport="body">
            <div x-show="sheetOpen" x-cloak class="fixed inset-0 z-50 md:hidden flex items-end" role="dialog" aria-modal="true" aria-label="Contact options">
                <div x-show="sheetOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" data-overscroll-lock="true" @click="closeSheet()"></div>
                <div x-show="sheetOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="translate-y-0"
                     x-transition:leave-end="translate-y-full"
                     class="relative z-10 w-full rounded-t-3xl bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shadow-2xl p-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] space-y-4">
                    <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700 mx-auto" aria-hidden="true"></div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider text-center">Contact {{ $official->name }}</h3>
                    <div class="space-y-2">
                        @if($official->email)
                            <a href="mailto:{{ $official->email }}" @click="closeSheet()" class="flex items-center gap-3 min-h-[3.25rem] px-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 active:scale-[0.98] transition-all">
                                <svg class="w-5 h-5 text-[#1e40af] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $official->email }}</span>
                            </a>
                        @endif
                        @if($official->contact_number)
                            <a href="tel:{{ $official->contact_number }}" @click="closeSheet()" class="flex items-center gap-3 min-h-[3.25rem] px-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900 active:scale-[0.98] transition-all">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $official->contact_number }}</span>
                            </a>
                        @endif
                    </div>
                    <button type="button" @click="closeSheet()" class="w-full min-h-11 rounded-2xl border border-slate-200 dark:border-slate-700 text-xs font-bold uppercase text-slate-600 dark:text-slate-300 active:scale-95 transition-all">Cancel</button>
                </div>
            </div>
        </template>
    @endif
</div>

@include('partials.governance-mobile-js')
@endsection
