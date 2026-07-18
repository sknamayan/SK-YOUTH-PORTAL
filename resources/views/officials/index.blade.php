@extends('layouts.app')

@section('content')
<div class="flex-1 bg-slate-50 dark:bg-slate-950 font-sans pb-16">

    <!-- Hero Header Panel -->
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-10 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-6 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">SK Officials</span>
            </nav>
            
            <div class="max-w-2xl space-y-3">
                <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest">
                    Youth Governance
                </span>
                <h1 class="text-3xl sm:text-4xl font-black font-display uppercase tracking-tight leading-tight">
                    Sangguniang Kabataan Council
                </h1>
                <p class="text-xs sm:text-sm text-slate-305 leading-relaxed max-w-xl">
                    Meet the elected youth leaders of Barangay Namayan. Driven by service, leadership, and community development.
                </p>
            </div>
        </div>
    </section>

    <!-- Council Members Grid -->
    <section class="max-w-7xl mx-auto w-full px-4 sm:px-8 py-10 md:py-14">
        @if($officials->isEmpty())
            <div class="flex flex-col items-center text-center py-20 px-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm animate-fade-in">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200">No Officials Registered</h2>
                <p class="text-xs text-slate-400 mt-2 max-w-xs leading-relaxed">The youth council roster is currently being updated. Please check back soon.</p>
                <a href="{{ route('landing') }}" class="mt-6 inline-flex items-center min-h-10 px-5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl active:scale-95 transition-all shadow-sm">Back to Home</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                @foreach($officials as $official)
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition duration-200">
                        <!-- Top Image Area -->
                        <div class="relative w-full aspect-square bg-slate-50 dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800/60">
                            @if($official->photoUrl())
                                <img src="{{ $official->photoUrl() }}" alt="{{ $official->name }}" class="w-full h-full object-cover object-top">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-950 dark:to-slate-900">
                                    <span class="w-16 h-16 rounded-2xl bg-blue-500/10 dark:bg-blue-400/10 text-blue-600 dark:text-blue-400 text-2xl font-black font-display flex items-center justify-center border border-blue-500/20">
                                        {{ $official->initials() }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Details -->
                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="inline-flex px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[9px] font-extrabold uppercase tracking-wide">
                                    {{ $official->position }}
                                </span>
                                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 font-display uppercase tracking-tight">
                                    {{ $official->name }}
                                </h3>
                                @if($official->term)
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">
                                        Term: {{ $official->term }}
                                    </span>
                                @endif
                            </div>

                            @if($official->bio)
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3">
                                    {{ strip_tags($official->bio) }}
                                </p>
                            @endif

                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/60 space-y-2">
                                <div class="flex items-center gap-2 text-xs text-slate-650 dark:text-slate-350 min-w-0">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="truncate font-mono text-[10px]" title="{{ $official->email }}">
                                        {{ $official->email ?? 'no-email@namayan.gov' }}
                                    </span>
                                </div>
                                @if($official->contact_number)
                                    <div class="flex items-center gap-2 text-xs text-slate-655 dark:text-slate-350">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span class="font-mono text-[10px]">{{ $official->contact_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
