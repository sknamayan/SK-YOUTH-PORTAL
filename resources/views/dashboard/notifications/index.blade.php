@extends('layouts.app')

@section('content')
@if(Auth::user()->canAccessDashboard())
    <div x-data="{}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-955 min-h-0">
        <!-- Left Sidebar -->
        @include('layouts.dashboard-sidebar')

        {{-- Mobile sidebar backdrop --}}

        <!-- Main Content Pane -->
        <div class="flex-1 flex flex-col min-w-0 min-h-0 md:min-h-[calc(100dvh-4rem)]">
            {{-- Sticky mobile app bar --}}

            <div class="p-4 md:p-8 space-y-6 flex-1 overflow-y-auto">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Inbox</span>
                        <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase mt-1">Notifications</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Keep track of your submitted forms, request updates, and portal announcements.</p>
                    </div>
                    @php
                        $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                    @endphp
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="btn-outline w-full sm:w-auto text-xs py-2.5 px-4 min-h-11 rounded-xl">
                                Mark All as Read
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Notifications list card -->
                @include('dashboard.notifications.partials.list-card')
            </div>
        </div>
    </div>
@else
    <div class="flex-1 bg-slate-50 dark:bg-slate-955 font-sans min-h-screen">
        <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                    <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                    <span aria-hidden="true" class="shrink-0">/</span>
                    <span class="text-white truncate" aria-current="page">Notifications</span>
                </nav>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="max-w-2xl space-y-2.5">
                        <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest font-display">Inbox</span>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">Notifications</h1>
                        <p class="text-sm text-slate-300 leading-relaxed">Keep track of your submitted forms, request updates, and portal announcements.</p>
                    </div>
                    @php
                        $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                    @endphp
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}" class="shrink-0 self-start sm:self-center">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center min-h-11 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all text-white cursor-pointer">
                                Mark All as Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10 space-y-6">
            <!-- Horizontal Citizen Sub-navigation -->
            @include('profile.partials.citizen-nav')

            <!-- Notifications list card -->
            @include('dashboard.notifications.partials.list-card')
        </div>
    </div>
@endif
@endsection
