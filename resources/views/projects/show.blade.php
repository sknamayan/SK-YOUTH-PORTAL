@extends('layouts.app')

@section('content')
<div x-data="{}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar (Explorer Navigation Tree) -->
    <aside class="hidden md:flex flex-col w-80 bg-white border-r border-slate-100 shrink-0">
        
        <div class="p-6 space-y-6 flex-1 flex flex-col min-h-0 overflow-y-auto">
            <!-- Explorer Branding Header -->
            <div class="pb-5 border-b border-slate-100">
                <span class="text-[9px] font-black tracking-widest text-[#1e40af] uppercase block">Interactive Explorer</span>
                <h2 class="text-sm font-extrabold text-slate-800 font-display uppercase tracking-tight mt-1">{{ $project->title }}</h2>
                <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">Select an active committee program to view accomplishments and file requests.</p>
            </div>

            <!-- Nested Hierarchy Navigation Tree -->
            <nav class="space-y-4">
                @foreach($project->committees as $comm)
                    @php
                        $commIcon = match($comm->slug) {
                            'education' => 'education',
                            'health' => 'health',
                            'governance' => 'governance',
                            'active-citizenship' => 'active-citizenship',
                            'social-inclusion' => 'social-inclusion',
                            'peace-building' => 'peace-building',
                            'environment' => 'environment',
                            'youth-employment' => 'youth-employment',
                            'agriculture' => 'agriculture',
                            'global-mobility' => 'global-mobility',
                            default => 'dashboard'
                        };
                        $isCommActive = $comm->id === $activeCommittee->id;
                    @endphp
                    <div class="space-y-1.5">
                        <!-- Committee Header (Subtopic Node) -->
                        <a href="{{ route('projects.committee', ['project_slug' => $project->slug, 'committee_slug' => $comm->slug]) }}"
                           class="flex items-center space-x-2 px-3 py-2 border rounded-xl transition duration-150 {{ $isCommActive ? 'bg-blue-50 border-blue-200 text-[#1e40af] font-bold shadow-sm/5' : 'bg-slate-50/50 border-slate-100/50 text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                            <x-category-icon name="{{ $commIcon }}" class="w-4 h-4 text-blue-600 shrink-0" />
                            <span class="text-[10px] font-black tracking-wider uppercase font-display truncate">{{ $comm->name }}</span>
                        </a>
                        
                        <!-- Initiatives list (Projects Node) -->
                        @if($isCommActive)
                            <div class="pl-3 border-l-2 border-slate-100 space-y-1 ml-3.5 mt-1">
                                @foreach($comm->initiatives as $init)
                                    @php
                                        $isActive = $init->id === $activeInitiative->id;
                                    @endphp
                                    <a href="{{ route('projects.explorer', ['project_slug' => $project->slug, 'committee_slug' => $comm->slug, 'initiative_id' => $init->id]) }}"
                                       class="block px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $isActive ? 'bg-blue-50 text-[#1e40af] border-l-2 border-blue-600 pl-2 font-black shadow-sm/5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50/50' }}">
                                        {{ $init->title }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>

        <!-- Sidebar Back Link -->
        <div class="p-6 bg-slate-50 border-t border-slate-100">
            <a href="/" class="text-[10px] font-black text-slate-500 hover:text-[#1e40af] uppercase tracking-widest flex items-center space-x-1.5 transition">
                <svg class="w-3.5 h-3.5 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                <span>Back to Home</span>
            </a>
        </div>
    </aside>

    <!-- Right Pane (Main Workspace Panel) -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f8fafc]">

        <!-- Dynamic Page Workspace -->
        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto max-w-5xl w-full mx-auto">
            
            <!-- Initiative Hero Header -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm flex flex-col sm:flex-row justify-between items-start gap-6 relative overflow-hidden">
                <div class="space-y-3 relative z-10 max-w-2xl">
                    <div class="flex items-center space-x-2.5">
                        <span class="bg-blue-50 text-[#1e40af] border border-blue-100 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                            {{ $activeCommittee->name }}
                        </span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 font-display uppercase tracking-tight">{{ $activeInitiative->title }}</h1>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">{{ $activeInitiative->description }}</p>
                </div>
                
                @if($activeInitiative->is_coming_soon)
                    <span class="bg-slate-100 text-slate-400 border border-slate-200 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full self-start sm:self-center select-none">
                        Coming Soon
                    </span>
                @else
                    <a href="{{ route('forms.custom.create', $activeInitiative->id) }}" class="btn-primary text-xs shrink-0 flex items-center space-x-1.5 shadow-md hover:shadow-blue-500/20 active:scale-95 transition relative z-10 self-start sm:self-center">
                        <span>➕ Submit Request</span>
                    </a>
                @endif
            </div>

            <!-- Stepper Progress Tracker -->
            <div x-data="{ currentStep: 1 }" class="card bg-white p-6 border border-slate-100 shadow-sm rounded-3xl space-y-6">
                <div>
                    <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Interactive Workflow</span>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide mt-1">Request Progress & Tracking Stepper</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Explore the validation, verification, and fulfillment stages of request submissions.</p>
                </div>

                <!-- Stepper Progress Line and Buttons -->
                <div class="relative flex items-center justify-between w-full px-2">
                    <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-1 bg-slate-100 z-0"></div>
                    <div class="absolute left-6 top-1/2 -translate-y-1/2 h-1 bg-[#1e40af] transition-all duration-500 z-0" 
                         :style="`width: ${((currentStep - 1) / 3) * 90}%`"></div>

                    <!-- Steps Buttons -->
                    @php
                        $steps = [
                            1 => ['title' => 'Submitted', 'desc' => 'Request Submission & Verification'],
                            2 => ['title' => 'Processing', 'desc' => 'DPO Privacy Masking & Review'],
                            3 => ['title' => 'Allocated', 'desc' => 'Schedules & Resource Scheduling'],
                            4 => ['title' => 'Completed', 'desc' => 'Resolution & Service Release'],
                        ];
                    @endphp
                    @foreach($steps as $num => $stepData)
                        <button @click="currentStep = {{ $num }}"
                                class="relative z-10 w-10 h-10 sm:w-12 sm:h-12 rounded-full border-4 flex flex-col items-center justify-center font-bold text-xs sm:text-sm font-display transition duration-300 active:scale-95 shadow-sm"
                                :class="currentStep >= {{ $num }} 
                                    ? 'bg-[#1e40af] border-blue-400 text-white hover:bg-blue-700' 
                                    : 'bg-white border-slate-200 text-slate-400 hover:border-slate-400 hover:text-slate-700'">
                            <span>{{ $num }}</span>
                            <span class="absolute top-12 sm:top-14 text-[9px] font-black uppercase tracking-wider text-slate-400 hidden sm:block whitespace-nowrap"
                                  :class="currentStep === {{ $num }} ? 'text-[#1e40af]' : ''">
                                {{ $stepData['title'] }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Stepper Stage Detail Card -->
                <div class="bg-slate-50 border border-slate-100/50 rounded-2xl p-5 mt-4">
                    <template x-if="currentStep === 1">
                        <div class="space-y-1.5">
                            <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest font-display block">Stage 1: Submitted</span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Request Submission & Validation</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Submit your request details via our digital forms. The system validates fields, registers user IPs, and issues a tracking ID code instantly. Citizens can then look up status timelines in real time.</p>
                        </div>
                    </template>
                    <template x-if="currentStep === 2">
                        <div class="space-y-1.5">
                            <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest font-display block">Stage 2: Processing</span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">DPO Verification & PII Privacy Masking</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Our Data Protection Officer evaluates the request. Private information (e.g. contact numbers, precise addresses) is strictly masked inside dashboard records using secure character masking algorithms to comply with data privacy policies.</p>
                        </div>
                    </template>
                    <template x-if="currentStep === 3">
                        <div class="space-y-1.5">
                            <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest font-display block">Stage 3: Allocated</span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Master Calendar Scheduling & Allocation</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Staff updates the request timeline on our public master calendar. Reserved studying desks, medical consultations, team tournament match assignments, or medicine courier delivery routes are allocated.</p>
                        </div>
                    </template>
                    <template x-if="currentStep === 4">
                        <div class="space-y-1.5">
                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest font-display block">Stage 4: Completed</span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Fulfillment & Final Resolution</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">The application reaches its resolution. Studied slots are officially confirmed, athletic team registration rosters are compiled, or medicines are delivered. Citizens are notified and summaries are filed to transparency records.</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Accomplishment Reports Desk Section -->
            <div class="space-y-4">
                <div class="flex justify-between items-center px-1">
                    <div>
                        <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide font-display">Accomplishment Reports</h2>
                        <p class="text-xs text-slate-400">View transparency documentations, attendance logs, and result statistics published for this initiative.</p>
                    </div>
                </div>

                <div class="card p-0 bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                    @if($activeInitiative->accomplishmentReports->isEmpty())
                        <div class="text-center py-12 px-4 space-y-3 bg-white">
                            <div>
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">No Reports Available</h3>
                                <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Our committee will soon upload quarterly accomplishment reports and review audits for this program.</p>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-display">
                                        <th class="p-4 pl-6">Report Title</th>
                                        <th class="p-4">Reporting Period</th>
                                        <th class="p-4">Format</th>
                                        <th class="p-4 pr-6 text-right font-black">Link</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach($activeInitiative->accomplishmentReports as $report)
                                        @php
                                            $extension = pathinfo($report->file_path, PATHINFO_EXTENSION);
                                            $badgeClass = match(strtolower($extension)) {
                                                'pdf' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                'doc', 'docx' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'xls', 'xlsx' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'png', 'jpg', 'jpeg' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                default => 'bg-slate-50 text-slate-700 border-slate-100'
                                            };
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <!-- Title -->
                                            <td class="p-4 pl-6 font-bold text-slate-800">
                                                <div class="flex items-center space-x-2.5">
                                                    <span class="text-lg">📄</span>
                                                    <span>{{ $report->report_title }}</span>
                                                </div>
                                            </td>
                                            <!-- Period -->
                                            <td class="p-4 text-slate-500 font-mono font-medium">
                                                {{ $report->reporting_period->format('M d, Y') }}
                                            </td>
                                            <!-- Format Badge -->
                                            <td class="p-4">
                                                <span class="inline-block px-2 py-0.5 border text-[9px] font-black uppercase tracking-wider rounded {{ $badgeClass }}">
                                                    {{ $extension ?: 'DOC' }}
                                                </span>
                                            </td>
                                            <!-- Download Link -->
                                            <td class="p-4 pr-6 text-right font-bold">
                                                <a href="{{ asset('storage/' . $report->file_path) }}" 
                                                   target="_blank" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-[#1e40af] hover:bg-[#1e40af] hover:text-white rounded-xl text-[10px] uppercase tracking-wider transition active:scale-95">
                                                    Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bottom Actions Dashboard Card -->
            <div class="bg-gradient-to-r from-blue-800 to-blue-900 rounded-3xl p-6 md:p-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 shadow-md shadow-blue-900/10">
                <div class="space-y-1.5 text-center md:text-left">
                    <h3 class="text-base font-bold uppercase tracking-wider font-display">Need to apply or check submissions?</h3>
                    <p class="text-xs text-blue-200 max-w-md">File request records, register slots, or lookup real-time tracking status in Barangay Namayan.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3 shrink-0">
                    <a href="{{ route('track.index') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-95">
                        🔍 Track Request
                    </a>
                    @if($activeInitiative->is_coming_soon)
                        <span class="px-5 py-2.5 bg-white/10 text-slate-300 border border-white/10 font-bold rounded-xl text-xs uppercase tracking-wider select-none">
                            Coming Soon
                        </span>
                    @else
                        <a href="{{ route('forms.custom.create', $activeInitiative->id) }}" class="px-5 py-2.5 bg-white text-[#1e40af] hover:bg-blue-50 font-bold rounded-xl text-xs uppercase tracking-wider transition shadow-sm active:scale-95">
                            ➕ Apply Now
                        </a>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
