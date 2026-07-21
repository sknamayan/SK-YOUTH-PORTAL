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
                title="Edit Accomplishment Report"
                subtitle="Update information for this accomplishment report. Leave the file field empty to keep the existing document."
                action="{{ route('admin.reports.update', $report->id) }}"
                enctype="multipart/form-data"
            >
                @method('PUT')

                <div class="pt-4 flex items-center justify-between gap-4">
                    <a href="{{ route('admin.reports.index') }}" class="btn-secondary text-center flex-1 sm:flex-initial py-2.5">
                        Cancel
                    </a>
                    <button type="submit" class="btn-primary flex-1 sm:flex-initial py-2.5">
                        Save Changes
                    </button>
                </div>
                <x-mobile-bottom-action type="submit">
                    Save Changes
                </x-mobile-bottom-action>
            </x-form-card>

        </div>

    </div>

</div>
@endsection
