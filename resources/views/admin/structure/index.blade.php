@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-black text-white uppercase tracking-wider font-display">Portal Structure Settings</h1>
        <p class="text-xs text-slate-400">Manage your system's committees, program initiatives, and custom registration forms.</p>
    </div>

    <livewire:admin.structure-manager />
</div>
@endsection
