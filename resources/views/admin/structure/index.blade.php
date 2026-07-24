@extends('layouts.app')

@section('content')
<div
    x-data="{ mobileSidebar: false }"
    class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-955 min-h-0"
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

        {{-- Scrollable page body --}}
        <div class="flex-1 overflow-y-auto overscroll-y-contain p-4 md:p-8 space-y-6 pb-24 md:pb-8">

            {{-- Breadcrumbs --}}
            <nav aria-label="Breadcrumb" class="pb-3 border-b border-slate-100 dark:border-slate-800">
                <ol class="flex items-center gap-2 text-[10px] md:text-xs font-semibold uppercase tracking-wider min-w-0 font-display">
                    <li class="shrink-0">
                        <a href="{{ route('dashboard.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-blue-400 transition duration-150">Dashboard</a>
                    </li>
                    <li class="text-slate-300 dark:text-slate-700 shrink-0" aria-hidden="true">/</li>
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">Portal Structure</li>
                </ol>
            </nav>

            {{-- Page Header --}}
            <div class="space-y-1 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest block font-display">System Configuration</span>
                <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Portal Structure Settings</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Manage your system's committees, program initiatives, and custom registration forms.</p>
            </div>

            <livewire:admin.structure-manager />

        </div>

    </div>

</div>
@endsection
