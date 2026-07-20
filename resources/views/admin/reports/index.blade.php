@extends('layouts.app')

@section('content')
<div x-data="{
    showModal: false,
    editMode: false,
    formAction: '{{ route('admin.reports.store') }}',
    reportTitle: '',
    initiativeId: '',
    reportingPeriod: '',
    existingFileUrl: null,
    
    openCreate() {
        this.editMode = false;
        this.formAction = '{{ route('admin.reports.store') }}';
        this.reportTitle = '';
        this.initiativeId = '';
        this.reportingPeriod = '';
        this.existingFileUrl = null;
        this.showModal = true;
        this.$nextTick(() => {
            window.dispatchEvent(new CustomEvent('report-opened', { detail: { existingUrl: null } }));
        });
    },
    
    openEdit(report) {
        this.editMode = true;
        this.formAction = '/admin/reports/' + report.id;
        this.reportTitle = report.report_title;
        this.initiativeId = report.initiative_id;
        this.reportingPeriod = report.reporting_period;
        this.existingFileUrl = report.file_path ? '/storage/' + report.file_path : null;
        this.showModal = true;
        this.$nextTick(() => {
            window.dispatchEvent(new CustomEvent('report-opened', { detail: { existingUrl: this.existingFileUrl } }));
        });
    }
}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

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
                <button type="button" @click="openCreate()" class="hidden md:flex btn-primary text-xs shrink-0 items-center space-x-1">
                    <span>➕ Upload New Report</span>
                </button>
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
                            <button type="button" @click="openCreate()" class="btn-primary text-xs">Upload Your First Report</button>
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
                                            <button type="button" @click="openEdit({ id: {{ $rep->id }}, report_title: '{{ addslashes($rep->report_title) }}', initiative_id: '{{ $rep->initiative_id }}', reporting_period: '{{ $rep->reporting_period ? $rep->reporting_period->format('Y-m-d') : '' }}', file_path: '{{ $rep->file_path ? addslashes($rep->file_path) : '' }}' })" class="inline-flex items-center px-2.5 py-1 border border-slate-200 text-slate-650 hover:text-[#1e40af] hover:border-[#1e40af] font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95">
                                                Edit
                                            </button>
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

            <!-- Create/Edit Modal -->
            <div x-show="showModal" class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col border border-slate-100 dark:border-slate-800 overflow-hidden animate-fade-in-up" @click.away="showModal = false">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-sky-400 uppercase tracking-widest block font-display">Report Console</span>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-105 font-display uppercase tracking-tight" x-text="editMode ? 'Edit Accomplishment Report' : 'Upload Accomplishment Report'"></h3>
                        </div>
                        <button @click="showModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/50 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Body (Form) -->
                    <form :action="formAction" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-4">
                        @csrf
                        <template x-if="editMode">
                            @method('PUT')
                        </template>

                        <div class="space-y-4">
                            <!-- Report Title -->
                            <div class="space-y-1.5">
                                <label for="report_title" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Report Title <span class="text-rose-500 font-extrabold">*</span>
                                </label>
                                <input type="text" name="report_title" id="report_title" required x-model="reportTitle" placeholder="e.g. Q1 Booking Attendance Report"
                                       class="block w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-semibold text-slate-700 dark:text-slate-300">
                            </div>

                            <!-- Target Initiative -->
                            <div class="space-y-1.5">
                                <label for="initiative_id" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Target Initiative <span class="text-rose-500 font-extrabold">*</span>
                                </label>
                                <select name="initiative_id" id="initiative_id" required x-model="initiativeId"
                                        class="block w-full py-2.5 pl-4 pr-10 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-700 dark:text-slate-300 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold">
                                    <option value="">Select Initiative...</option>
                                    @foreach($initiatives as $id => $title)
                                        <option value="{{ $id }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Reporting Period -->
                            <div class="space-y-1.5">
                                <label for="reporting_period" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Reporting Period <span class="text-rose-500 font-extrabold">*</span>
                                </label>
                                <input type="date" name="reporting_period" id="reporting_period" required x-model="reportingPeriod"
                                       class="block w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-semibold text-slate-700 dark:text-slate-300">
                            </div>

                            <!-- File Upload Input -->
                            <div class="space-y-1.5">
                                <label for="file" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Report Document File <span class="text-rose-500 font-extrabold" x-show="!editMode">*</span>
                                </label>
                                <x-file-upload name="file" placeholder="Drag your report file here or click to browse." />
                                <span class="text-[10px] text-slate-400 mt-1 block">Supports PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, JPEG. Max file size: 2MB.</span>
                                
                                <template x-if="editMode && existingFileUrl">
                                    <div class="mt-2 text-xs">
                                        <span class="text-slate-500">Current file:</span>
                                        <a :href="existingFileUrl" target="_blank" class="text-blue-600 hover:underline font-semibold" x-text="existingFileUrl.split('/').pop()"></a>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Modal Actions -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-655 dark:text-slate-300 font-bold rounded-xl transition text-xs uppercase tracking-wider">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white font-bold rounded-xl transition text-xs uppercase tracking-wider shadow-sm" x-text="editMode ? 'Save Changes' : 'Upload Report'"></button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <x-mobile-bottom-action x-show="!showModal" @click="openCreate()">
        Upload New Report
    </x-mobile-bottom-action>
</div>
@endsection
