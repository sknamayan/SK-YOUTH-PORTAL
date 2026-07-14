@extends('layouts.app')

@section('content')
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
            
            <div class="mb-4">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-[#1e40af] uppercase tracking-wider transition">
                    &larr; Back to Reports
                </a>
            </div>

            <x-form-card 
                title="Edit Accomplishment Report" 
                subtitle="Update information for this accomplishment report. Leave the file field empty to keep the existing document." 
                action="{{ route('admin.reports.update', $report->id) }}"
                enctype="multipart/form-data"
            >
                @method('PUT')

                <div class="space-y-4">
                    <x-form-input label="Report Title" name="report_title" required="true" :value="$report->report_title" placeholder="e.g. Q1 Silid Karunungan Booking Attendance Report" />
                    
                    <x-form-select label="Target Initiative" name="initiative_id" required="true" :options="$initiatives" :selected="$report->initiative_id" />
                    
                    <x-form-input type="date" label="Reporting Period" name="reporting_period" required="true" :value="$report->reporting_period ? $report->reporting_period->format('Y-m-d') : ''" />
                    
                    <!-- File Upload Input -->
                    <div class="space-y-1.5">
                        <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            Replace Report Document File <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <x-file-upload name="file" placeholder="Drag your report file here or click to browse." existing-url="{{ $report->file_path ? asset('storage/' . $report->file_path) : null }}" />
                        <span class="text-[10px] text-slate-400 mt-1 block">Supports PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, JPEG. Max file size: 2MB. Leave empty to keep current file.</span>
                        @error('file')
                            <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between gap-4">
                    <a href="{{ route('admin.reports.index') }}" class="btn-secondary text-center flex-1 sm:flex-initial py-2.5">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary flex-1 sm:flex-initial py-2.5">
                        Save Changes
                    </button>
                </div>
            </x-form-card>

        </div>

    </div>

</div>
@endsection
