@extends('layouts.app')

@section('content')
<div x-data="{}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <!-- Main Content Pane -->
    <div class="flex-1 flex flex-col min-w-0">

        <div class="p-6 md:p-8 pb-24 md:pb-8 space-y-6 flex-1 overflow-y-auto">
            
            <div class="mb-4">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-[#1e40af] uppercase tracking-wider transition">
                    &larr; Back to Reports
                </a>
            </div>

            <x-form-card 
                title="Upload Accomplishment Report" 
                subtitle="Upload accomplishment report documents linked to project initiatives. Supports PDF, Word, Excel, and images under 2MB." 
                action="{{ route('admin.reports.store') }}"
                enctype="multipart/form-data"
            >
                <div class="space-y-4">
                    <x-form-input label="Report Title" name="report_title" required="true" placeholder="e.g. Q1 Silid Karunungan Booking Attendance Report" />
                    
                    <x-form-select label="Target Initiative" name="initiative_id" required="true" :options="$initiatives" />
                    
                    <x-form-input type="date" label="Reporting Period" name="reporting_period" required="true" />
                    
                    <!-- File Upload Input -->
                    <div class="space-y-1.5">
                        <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            Report Document File <span class="text-rose-500 font-extrabold">*</span>
                        </label>
                        <x-file-upload name="file" required="true" placeholder="Drag your report file here or click to browse." />
                        <span class="text-[10px] text-slate-400 mt-1 block">Supports PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, JPEG. Max file size: 2MB.</span>
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
                        Upload Report
                    </button>
                </div>
                <x-mobile-bottom-action type="submit">
                    Upload Report
                </x-mobile-bottom-action>
            </x-form-card>

        </div>

    </div>

</div>
@endsection
