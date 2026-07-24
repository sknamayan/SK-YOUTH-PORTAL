@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    {{-- Breadcrumbs --}}
    <nav aria-label="Breadcrumb" class="pb-3 border-b border-slate-800">
        <ol class="flex items-center gap-2 text-[10px] md:text-xs font-semibold uppercase tracking-wider min-w-0 font-display">
            <li class="shrink-0">
                <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-blue-400 transition duration-150">Dashboard</a>
            </li>
            <li class="text-slate-600 shrink-0" aria-hidden="true">/</li>
            <li class="text-slate-200 truncate" aria-current="page">Portal Structure</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="space-y-1 pb-4">
        <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest block font-display">System Configuration</span>
        <h1 class="text-xl md:text-2xl font-black tracking-tight text-white font-display uppercase">Portal Structure Settings</h1>
        <p class="text-xs text-slate-400 leading-relaxed">Manage your system's committees, program initiatives, and custom registration forms.</p>
    </div>

    <livewire:admin.structure-manager />
</div>
@endsection
