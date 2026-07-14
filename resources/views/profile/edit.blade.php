@extends('layouts.app')

@section('content')
@if(Auth::user()->canAccessDashboard())
    <div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

        <!-- Left Sidebar -->
        @include('layouts.dashboard-sidebar')

        <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

        <!-- Main Content Pane -->
        <div class="flex-1 flex flex-col min-w-0">
            
            <header class="bg-white border-b border-slate-100 h-16 px-4 flex items-center justify-between md:hidden shrink-0">
                <button @click="mobileSidebar = true" class="p-2 text-slate-500 hover:text-slate-800 active:scale-95 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain rounded-full bg-white p-0.5 border" alt="SK Logo">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-800 font-display">SK Namayan</span>
                </div>
                <div class="w-10"></div>
            </header>

            <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
                <div class="max-w-xl space-y-8">
                    <div>
                        <span class="text-[10px] font-black tracking-widest text-[#1e40af] uppercase font-display block">Settings Portal</span>
                        <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Edit Account Profile</h1>
                        <p class="text-xs text-slate-400 mt-1">Manage your basic contact info, change security credentials, or delete your account records.</p>
                    </div>

                    @include('profile.partials.edit-forms')
                </div>
            </div>
        </div>
    </div>@else
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">Account Settings</span>
            </nav>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="max-w-2xl space-y-2.5">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest font-display">Settings Portal</span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">Edit Account Profile</h1>
                    <p class="text-sm text-slate-300 leading-relaxed">Manage your basic contact info, change security credentials, or delete your account records.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10 flex-1 font-sans space-y-6">
        @include('profile.partials.citizen-nav')

        @include('profile.partials.edit-forms')
    </div>
@endif
@endsection
