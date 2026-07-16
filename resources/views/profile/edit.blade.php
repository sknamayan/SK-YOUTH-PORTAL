@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'account');
@endphp

<div x-data="{ activeTab: '{{ $activeTab }}' }" class="flex-1 flex flex-col min-h-screen">
    @if(Auth::user()->canAccessDashboard())
        <div x-data="{ mobileSidebar: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950">

            <!-- Left Master Sidebar -->
            @include('layouts.dashboard-sidebar')

            <div x-show="mobileSidebar" @click="mobileSidebar = false" class="fixed inset-0 bg-slate-900/40 z-20 md:hidden" x-cloak></div>

            <!-- Main Content Pane -->
            <div class="flex-1 flex flex-col min-w-0">

                <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">
                    <div class="max-w-4xl space-y-6">
                        <div>
                            <span class="text-[10px] font-black tracking-widest text-[#1e40af] dark:text-blue-400 uppercase font-display block">{{ __('Settings Portal') }}</span>
                            <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase mt-1">{{ __('Settings & Preferences') }}</h1>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ __('Manage your account settings, dark mode preferences, security sessions, and data privacy.') }}</p>
                        </div>

                        <!-- Horizontal Settings Tab Navigation -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-3 border-b border-slate-150 dark:border-slate-800 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none shrink-0">
                            <button @click="activeTab = 'account'" 
                                    :class="activeTab === 'account' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ __('Personal Info') }}</span>
                            </button>
                            <button @click="activeTab = 'display'" 
                                    :class="activeTab === 'display' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ __('Display & Theme') }}</span>
                            </button>
                            <button @click="activeTab = 'security'" 
                                    :class="activeTab === 'security' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>{{ __('Security Settings') }}</span>
                            </button>
                            <button @click="activeTab = 'notifications'" 
                                    :class="activeTab === 'notifications' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span>{{ __('Notifications') }}</span>
                            </button>
                            <button @click="activeTab = 'privacy'" 
                                    :class="activeTab === 'privacy' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>{{ __('Privacy & Data') }}</span>
                            </button>
                        </div>

                        <!-- Forms Content Area -->
                        <div class="space-y-6">
                            @include('profile.partials.edit-forms')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Citizen Base Dashboard -->
        <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                    <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                    <span aria-hidden="true" class="shrink-0">/</span>
                    <span class="text-white truncate" aria-current="page">{{ __('Settings & Preferences') }}</span>
                </nav>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="max-w-2xl space-y-2.5">
                        <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest font-display">Settings Portal</span>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">{{ __('Settings & Preferences') }}</h1>
                        <p class="text-sm text-slate-300 leading-relaxed">{{ __('Manage your account settings, dark mode preferences, security sessions, and data privacy.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10 flex-1 font-sans space-y-6">
            @include('profile.partials.citizen-nav')

            <!-- Horizontal Settings Tab Navigation (Citizen View) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 border-b border-slate-150 dark:border-slate-800 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none shrink-0">
                <button @click="activeTab = 'account'" 
                        :class="activeTab === 'account' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ __('Personal Info') }}</span>
                </button>
                <button @click="activeTab = 'display'" 
                        :class="activeTab === 'display' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ __('Display & Theme') }}</span>
                </button>
                <button @click="activeTab = 'security'" 
                        :class="activeTab === 'security' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>{{ __('Security Settings') }}</span>
                </button>
                <button @click="activeTab = 'notifications'" 
                        :class="activeTab === 'notifications' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>{{ __('Notifications') }}</span>
                </button>
                <button @click="activeTab = 'privacy'" 
                        :class="activeTab === 'privacy' ? 'bg-[#1e40af] text-white shadow-sm' : 'bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white'" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>{{ __('Privacy & Data') }}</span>
                </button>
            </div>

            <!-- Forms Content Area -->
            <div class="space-y-6">
                @include('profile.partials.edit-forms')
            </div>
        </div>
    @endif
</div>
@endsection
