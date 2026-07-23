@extends('layouts.app')

@section('content')
<div class="flex-1 bg-slate-950 font-sans min-h-screen">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">My Requests</span>
            </nav>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="max-w-2xl space-y-2.5">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest font-display">Overview</span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">My Submitted Requests</h1>
                    <p class="text-sm text-slate-300 leading-relaxed">Review and monitor the status of requests submitted under your email address ({{ auth()->user()->email }}).</p>
                </div>
                <a href="/" class="inline-flex items-center justify-center min-h-11 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all text-white shrink-0 self-start sm:self-center">
                    New Request
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8 md:py-10 space-y-6 animate-fade-in-up">

        <!-- Horizontal Citizen Sub-navigation -->
        @include('profile.partials.citizen-nav')

        <!-- Livewire Component -->
        <livewire:my-requests />

    </div>
</div>
@endsection
