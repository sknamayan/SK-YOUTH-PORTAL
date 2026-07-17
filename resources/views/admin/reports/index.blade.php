@extends('layouts.app')

@section('content')
<div x-data="{}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <!-- Main Content Pane -->
    <div class="flex-1 flex flex-col min-w-0">

        <div class="p-6 md:p-8 pb-24 md:pb-8 space-y-6 flex-1 overflow-y-auto">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Project Accountability</span>
                    <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display uppercase mt-1">Accomplishment Reports</h1>
                    <p class="text-xs text-slate-500 mt-1">Manage, view, and upload accomplishment reports linked to initiatives.</p>
                </div>
                <a href="{{ route('admin.reports.create') }}" class="btn-primary text-xs shrink-0 flex items-center space-x-1">
                    <span>➕ Upload New Report</span>
                </a>
            </div>


            <!-- Reports Grid/Table -->
            <div class="card p-0 overflow-hidden bg-white border border-slate-100">
                @if($reports->isEmpty())
                    <div class="text-center py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">No Reports Uploaded</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Upload accomplishment reports to link them with project initiatives and show them on the public portal.</p>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('admin.reports.create') }}" class="btn-primary text-xs">Upload Your First Report</a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Report Title</th>
                                    <th class="p-4">Initiative</th>
                                    <th class="p-4">Reporting Period</th>
                                    <th class="p-4">File Path</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach($reports as $rep)
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <!-- Title -->
                                        <td class="p-4 pl-6 font-bold text-slate-800">
                                            {{ $rep->report_title }}
                                        </td>
                                        <!-- Initiative -->
                                        <td class="p-4 text-slate-600">
                                            @if($rep->initiative)
                                                <span class="font-semibold">{{ $rep->initiative->title }}</span>
                                                @if($rep->initiative->committee)
                                                    <span class="text-[10px] text-slate-400 block">{{ $rep->initiative->committee->name }}</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 italic">None</span>
                                            @endif
                                        </td>
                                        <!-- Reporting Period -->
                                        <td class="p-4 text-slate-500 font-mono">
                                            {{ $rep->reporting_period ? $rep->reporting_period->format('Y-m-d') : 'N/A' }}
                                        </td>
                                        <!-- File -->
                                        <td class="p-4 text-slate-500 truncate max-w-xs">
                                            @if($rep->file_path)
                                                <a href="{{ asset('storage/' . $rep->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center space-x-1">
                                                    <span>{{ basename($rep->file_path) }}</span>
                                                    <svg class="w-3.5 h-3.5 inline-block text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </a>
                                            @else
                                                <span class="text-slate-300 italic">No File</span>
                                            @endif
                                        </td>
                                        <!-- Actions -->
                                        <td class="p-4 pr-6 text-right space-x-1.5 whitespace-nowrap font-medium">
                                            <a href="{{ route('admin.reports.edit', $rep->id) }}" class="inline-flex items-center px-2.5 py-1 border border-slate-200 text-slate-600 hover:text-[#1e40af] hover:border-[#1e40af] font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95">
                                                Edit
                                            </a>
                                                <x-alert-dialog>
                                                    <x-slot:trigger>
                                                        <button type="button" class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                            Delete
                                                        </button>
                                                    </x-slot:trigger>
                                                    
                                                     <x-slot:icon>
                                                         <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                         </svg>
                                                     </x-slot:icon>
                                                    
                                                    <x-slot:title>
                                                        Delete Accomplishment Report
                                                    </x-slot:title>
                                                    
                                                    <x-slot:description>
                                                        Are you sure you want to delete the accomplishment report "{{ $rep->report_title }}"? This action is permanent and cannot be undone.
                                                    </x-slot:description>
                                                    
                                                    <x-slot:footer>
                                                        <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">
                                                            Cancel
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.reports.destroy', $rep->id) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm hover:shadow-md border border-transparent">
                                                                Confirm Delete
                                                            </button>
                                                        </form>
                                                    </x-slot:footer>
                                                </x-alert-dialog>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($reports->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $reports->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>

    </div>

</div>

<x-mobile-bottom-action href="{{ route('admin.reports.create') }}">
    Upload New Report
</x-mobile-bottom-action>
@endsection
