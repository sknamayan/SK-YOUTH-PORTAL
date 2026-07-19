@extends('layouts.app')

@section('content')
<div x-data="{}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <!-- Main Pane -->
    <div class="flex-1 flex flex-col min-w-0">

        <div class="px-4 py-6 sm:p-8 space-y-8 flex-1 overflow-y-auto">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Recycle Bin</span>
                </div>
            </div>

            <!-- Page Title -->
            <div class="space-y-1">
                <h1 class="text-2xl font-black text-slate-800 font-display uppercase tracking-tight">Master Recycle Bin</h1>
                <p class="text-xs text-slate-400">View soft-deleted records across all modules and restore or permanently delete them.</p>
            </div>

            <!-- Soft-deleted items list -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                @if($items->isEmpty())
                    <div class="text-center py-16 text-slate-400 text-xs font-semibold uppercase tracking-wider space-y-3">
                        <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <div>No archived items in the Recycle Bin.</div>
                    </div>
                @else
                    <div class="overflow-x-auto font-sans">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">Module / Type</th>
                                    <th class="py-4 px-6">Details / Description</th>
                                    <th class="py-4 px-6">Deleted Date</th>
                                    <th class="py-4 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                @foreach($items as $item)
                                    <tr class="hover:bg-slate-50/60 transition duration-150">
                                        <td class="py-4 px-6 shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600">
                                                {{ $item['module'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-semibold break-all text-slate-800">
                                            {{ $item['title'] }}
                                        </td>
                                        <td class="py-4 px-6 text-slate-400">
                                            {{ $item['deleted_at']->timezone(config('app.timezone', 'Asia/Manila'))->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center justify-center gap-3">
                                                <!-- Restore Button -->
                                                <form action="{{ route('admin.recycle-bin.restore', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="py-1.5 px-3.5 bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-600 hover:text-white rounded-lg font-bold text-[10px] sm:text-xs uppercase transition tracking-wider active:scale-95" title="Restore Record">
                                                        Restore
                                                    </button>
                                                </form>

                                                <!-- Force Delete Button (Uses Alert Dialog) -->
                                                <x-alert-dialog>
                                                    <x-slot name="trigger">
                                                        <button class="py-1.5 px-3.5 bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-600 hover:text-white rounded-lg font-bold text-[10px] sm:text-xs uppercase transition tracking-wider active:scale-95" title="Delete permanently">
                                                            Delete
                                                        </button>
                                                    </x-slot>
                                                    <x-slot name="icon">
                                                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </x-slot>
                                                    <x-slot name="title">Delete Permanently</x-slot>
                                                    <x-slot name="description">
                                                        Are you sure you want to permanently delete this item? This action cannot be undone. All data and associated logs will be lost.
                                                    </x-slot>
                                                    <x-slot name="footer">
                                                        <button @click="open = false" type="button" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition">
                                                            Cancel
                                                        </button>
                                                        <form action="{{ route('admin.recycle-bin.force-delete', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition">
                                                                Confirm Delete
                                                            </button>
                                                        </form>
                                                    </x-slot>
                                                </x-alert-dialog>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
