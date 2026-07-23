@extends('layouts.app')

@section('content')
<div class="flex-1 bg-slate-50 dark:bg-slate-950 font-sans min-h-screen pt-8 pb-36 md:py-10 md:pb-40">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">Profiling</span>
            </nav>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="max-w-2xl space-y-2.5">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[9px] font-black uppercase tracking-widest font-display">Youth Portal</span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">Katipunan ng Kabataan Self Profiling</h1>
                    <p class="text-sm text-slate-300 leading-relaxed">Please complete all steps of the KK Profiling registry form to verify your residency and citizen status.</p>
                </div>
                <a href="{{ route('profile.my-requests') }}" class="inline-flex items-center justify-center min-h-11 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all text-white shrink-0 self-start sm:self-center">
                    Return to Portal
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto w-full overflow-x-hidden px-4 pb-32 sm:px-8 sm:pb-36 lg:pb-40 py-8 md:py-10 space-y-6 animate-fade-in-up">
        
        <!-- Horizontal Citizen Sub-navigation -->
        @include('profile.partials.citizen-nav')

        <!-- Livewire KK Profiling Form Wizard -->
        <div class="bg-white border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
            <livewire:kk-profiling />
        </div>
    </div>
</div>
@endsection
