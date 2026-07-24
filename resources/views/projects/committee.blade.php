@extends('layouts.app')

@section('content')
<div x-data="{ selectedReport: null, activeSection: 'programs', activeInitiativeId: 'all', showAll: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

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
                        
                        <!-- Initiatives list (Projects Node under active committee) -->
                        @if($isCommActive)
                            <div class="pl-3 border-l-2 border-slate-100 space-y-1 ml-3.5 mt-1">
                                @foreach($comm->initiatives as $init)
                                    <a href="{{ route('projects.explorer', ['project_slug' => $project->slug, 'committee_slug' => $comm->slug, 'initiative_id' => $init->id]) }}"
                                       class="block px-3 py-1.5 rounded-lg text-[11px] font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-50/50 transition">
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
        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto max-w-5xl w-full mx-auto">
            
            <!-- Committee Hero Header -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm relative overflow-hidden space-y-6">
                <!-- Row 1: Title Section -->
                <div class="space-y-2 relative z-10">
                    <span class="inline-flex bg-blue-50 text-[#1e40af] border border-blue-100 text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                        10 Centers of Youth Participation Portal
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-800 font-display uppercase tracking-tight">{{ $activeCommittee->name }}</h1>
                </div>

                <!-- Row 2: Content Grid (2 Columns) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10 items-start">
                    <!-- Left Column: Description (Wider) -->
                    <div class="md:col-span-2">
                        <p class="text-xs md:text-sm text-slate-500 leading-relaxed font-medium">
                            Explore transparency reports, check public progress steps, or file requests for this committee's primary initiatives below.
                        </p>
                    </div>

                    <!-- Right Column: Committee Head Card -->
                    <div>
                        @if($chairperson)
                            <div class="flex items-center gap-4 bg-slate-50/50 border border-slate-100 p-4 rounded-2xl w-full">
                                @if($chairperson->photo_path)
                                    <img src="{{ $chairperson->photoUrl() }}" alt="{{ $chairperson->name }}" class="w-14 h-14 rounded-xl object-cover shrink-0">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-blue-50 text-[#1e40af] flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $chairperson->initials() }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <span class="text-[8px] font-black text-[#1e40af] uppercase tracking-wider block">Committee Head</span>
                                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight truncate mt-0.5">{{ $chairperson->name }}</h4>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-wider mt-0.5 truncate">{{ $chairperson->position }}</p>
                                    @if($chairperson->email)
                                        <p class="text-[9px] text-slate-400 mt-1 truncate font-mono">{{ $chairperson->email }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Committee Leadership & Members -->
            @if($members && $members->isNotEmpty())
                <div class="space-y-4 animate-fade-in-up">
                    <div class="px-1">
                        <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Committee Leadership & Team</span>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide font-display mt-0.5">Committee Members</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($members as $official)
                            <div class="bg-white border border-slate-100 rounded-3xl p-5 flex items-center space-x-4 shadow-sm hover:shadow-md transition duration-200">
                                @if($official->photo_path)
                                    <img src="{{ $official->photoUrl() }}" alt="{{ $official->name }}" class="w-12 h-12 rounded-2xl object-cover shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1e40af] flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $official->initials() }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight truncate">{{ $official->name }}</h4>
                                    <p class="text-[10px] text-[#1e40af] uppercase font-bold tracking-wider mt-0.5 truncate">{{ $official->position }}</p>
                                    @if($official->email)
                                        <p class="text-[9px] text-slate-400 mt-1 truncate">{{ $official->email }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Redesigned Dual Filter Tab System (Matching reference image) -->
            <div class="space-y-6">
                <!-- Top Tabs Bar (Styled exactly like reference image) -->
                <div class="border-b border-slate-200 flex space-x-6 text-xs font-bold uppercase tracking-wider mb-6 relative">
                    <button @click="activeSection = 'programs'; activeInitiativeId = 'all'"
                            :class="activeSection === 'programs' ? 'text-[#1e40af] border-b-2 border-[#1e40af] -mb-px pb-3' : 'text-slate-400 hover:text-[#1e40af] pb-3'"
                            class="transition duration-150 outline-none font-display font-black tracking-widest cursor-pointer">
                        Active Programs & Subtopics
                    </button>
                    <button @click="activeSection = 'accomplishments'; activeInitiativeId = 'all'"
                            :class="activeSection === 'accomplishments' ? 'text-[#1e40af] border-b-2 border-[#1e40af] -mb-px pb-3' : 'text-slate-400 hover:text-[#1e40af] pb-3'"
                            class="transition duration-150 outline-none font-display font-black tracking-widest cursor-pointer">
                        Past Accomplishments & Reports
                    </button>
                </div>

                <!-- Bottom Filter Pills (Styled exactly like reference image) -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 no-scrollbar">
                    <button @click="activeInitiativeId = 'all'"
                            :class="activeInitiativeId === 'all' ? 'bg-[#1e40af] text-white font-bold shadow-sm shadow-blue-500/10 border border-[#1e40af]' : 'bg-transparent border border-[#1e40af] text-[#1e40af] hover:bg-blue-50/50'"
                            class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition duration-150 shrink-0 active:scale-95 whitespace-nowrap cursor-pointer"
                    >
                        All
                    </button>
                    @foreach($activeCommittee->initiatives as $init)
                        <button @click="activeInitiativeId = {{ $init->id }}"
                                :class="activeInitiativeId === {{ $init->id }} ? 'bg-[#1e40af] text-white font-bold shadow-sm shadow-blue-500/10 border border-[#1e40af]' : 'bg-transparent border border-[#1e40af] text-[#1e40af] hover:bg-blue-50/50'"
                                class="px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition duration-150 shrink-0 active:scale-95 whitespace-nowrap font-display cursor-pointer"
                        >
                            {{ $init->title }}
                        </button>
                    @endforeach
                </div>

                <!-- Dynamic Content Sections -->
                
                <!-- Section 1: Active Programs Grid -->
                <div x-show="activeSection === 'programs'" 
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="space-y-4">
                     
                    <div class="px-1 mb-2">
                        <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Active Subtopics</span>
                        <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide font-display mt-0.5">Subtopics & Programs</h2>
                        <p class="text-xs text-slate-400">Select an initiative below to view request routing workflows, submit requests, or track progress.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($activeCommittee->initiatives as $init)
                            <div x-show="activeInitiativeId === 'all' || activeInitiativeId === {{ $init->id }}"
                                 x-transition
                                 class="group relative bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 rounded-3xl overflow-hidden shadow-md hover:shadow-2xl hover:border-blue-500/40 dark:hover:border-blue-500/40 transition-all duration-300 flex flex-col justify-between h-full transform hover:-translate-y-1">
                                
                                <!-- Top Image Header Container -->
                                @if($init->picture_path)
                                    <div class="relative w-full h-52 overflow-hidden bg-slate-900 shrink-0">
                                        <img src="{{ asset('storage/' . $init->picture_path) }}" 
                                             alt="{{ $init->title }}" 
                                             class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-108">
                                        <!-- Soft Image Gradient Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                                        
                                        <!-- Top Badge Overlay on Image -->
                                        <div class="absolute top-4 left-4 z-10">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-950/70 backdrop-blur-md border border-white/20 text-blue-300 text-[9px] font-black uppercase tracking-widest font-mono shadow-lg">
                                                {{ $activeCommittee->name }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <!-- Fallback Decorative Header Pattern when no photo is uploaded -->
                                    <div class="relative w-full h-24 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 p-6 flex items-end shrink-0">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-blue-200 text-[9px] font-black uppercase tracking-widest font-mono">
                                            {{ $activeCommittee->name }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Body Content Container -->
                                <div class="p-6 sm:p-7 space-y-3 flex-1">
                                    @if($init->picture_path)
                                        <!-- Category tag if photo header is present (mobile text variant) -->
                                        <div class="hidden">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 font-mono">{{ $activeCommittee->name }}</span>
                                        </div>
                                    @endif

                                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-tight font-display leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $init->title }}
                                    </h3>
                                    
                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium line-clamp-3">
                                        {{ $init->description }}
                                    </p>
                                </div>
                                
                                <!-- Card Footer & Interactive Action Button Bar -->
                                <div class="p-6 pt-4 bg-slate-50/50 dark:bg-slate-950/40 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mt-auto">
                                    <a href="{{ route('projects.explorer', ['project_slug' => $project->slug, 'committee_slug' => $activeCommittee->slug, 'initiative_id' => $init->id]) }}" 
                                       class="text-xs font-black text-blue-700 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 uppercase tracking-wider transition-colors inline-flex items-center gap-1.5 py-1">
                                        <span>View Stepper</span>
                                        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    </a>

                                    <div class="grid grid-cols-2 gap-2.5 w-full sm:w-auto">
                                        <a href="{{ route('track.index') }}" 
                                           class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-750 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-extrabold rounded-xl text-[10px] uppercase tracking-wider text-center transition-all shadow-sm active:scale-95">
                                            Track Progress
                                        </a>
                                        @if($init->is_coming_soon)
                                            <span class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800/60 text-slate-400 font-extrabold rounded-xl text-[10px] uppercase tracking-wider text-center select-none cursor-not-allowed">
                                                Coming Soon
                                            </span>
                                        @else
                                            <a href="{{ route('forms.custom.create', $init->id) }}" 
                                               class="px-4 py-2.5 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-600 hover:to-indigo-600 text-white font-extrabold rounded-xl text-[10px] uppercase tracking-wider text-center transition-all shadow-md shadow-blue-700/20 active:scale-95">
                                                Apply Now
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full card p-12 text-center text-slate-400 space-y-2">
                                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">No Programs Registered</h4>
                                <p class="text-[11px] text-slate-400">Programs and subtopics are currently being planned by this committee council.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Section 2: Accomplishment Reports Grid -->
                <div x-show="activeSection === 'accomplishments'" 
                     x-transition:enter="transition ease-out duration-250"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="space-y-4"
                     x-cloak>
                     
                    <div class="px-1 mb-2">
                        <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Transparency Archives</span>
                        <h2 class="text-base font-bold text-slate-800 uppercase tracking-wide font-display mt-0.5">Past Accomplishment Reports</h2>
                        <p class="text-xs text-slate-400">Official quarterly reports, auditable accomplishments, and summaries published by the youth council.</p>
                    </div>

                    @if($accomplishmentReports->isEmpty())
                        <div class="card p-0 bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                            <div class="text-center py-12 px-4 space-y-3 bg-white">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">No Accomplishments Uploaded</h3>
                                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Transparency logs and past accomplishment reports for this committee will be posted here soon.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($accomplishmentReports as $index => $report)
                                @php
                                    $extension = pathinfo($report->file_path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'webp', 'svg']);
                                    $extensionEmoji = match(strtolower($extension)) {
                                        'pdf' => '📕',
                                        'doc', 'docx' => '📘',
                                        'xls', 'xlsx' => '📊',
                                        'png', 'jpg', 'jpeg' => '🖼️',
                                        default => '📝'
                                    };
                                @endphp
                                <div x-data="{ imageError: false }"
                                     x-show="(activeInitiativeId === 'all' && (showAll || {{ $index }} < 3)) || (activeInitiativeId !== 'all' && activeInitiativeId === {{ $report->initiative_id ?? 'null' }})"
                                     x-transition:enter="transition ease-out duration-350"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     class="card bg-white border border-slate-100 hover:border-blue-150 hover:shadow-md transition duration-300 p-5 rounded-3xl flex flex-col justify-between"
                                     x-cloak>
                                    
                                    <div>
                                        <!-- White Picture / Stylized File Preview -->
                                        <div class="w-full h-32 bg-slate-50 border border-slate-100/60 rounded-2xl mb-4 flex items-center justify-center relative overflow-hidden select-none">
                                            @if($isImage)
                                                <img src="{{ asset('storage/' . $report->file_path) }}" 
                                                     x-show="!imageError" 
                                                     x-on:error="imageError = true"
                                                     class="w-full h-full object-cover rounded-2xl" 
                                                     alt="{{ $report->report_title }}">
                                            @endif

                                            <div x-show="!{{ $isImage ? 'true' : 'false' }} || imageError"
                                                 class="absolute inset-0 flex items-center justify-center w-full h-full">
                                                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(circle, #000 10%, transparent 11%); background-size: 10px 10px;"></div>
                                                
                                                <div class="relative w-14 h-18 bg-white border border-slate-200/60 rounded-lg shadow-sm flex flex-col justify-between p-2 transform rotate-1 hover:rotate-0 transition duration-300">
                                                    <div class="space-y-1">
                                                        <div class="h-0.5 bg-slate-200 rounded w-8"></div>
                                                        <div class="h-0.5 bg-slate-100 rounded w-6"></div>
                                                    </div>
                                                    
                                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                        <span class="text-lg">{{ $extensionEmoji }}</span>
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-between mt-auto">
                                                        <div class="h-0.5 bg-[#1e40af]/30 rounded w-3"></div>
                                                        <span class="text-[5px] font-black uppercase text-slate-400 font-mono">{{ $extension ?: 'DOC' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <h3 class="text-xs font-bold text-slate-800 line-clamp-1 uppercase tracking-wide font-display" title="{{ $report->report_title }}">
                                            {{ $report->report_title }}
                                        </h3>
                                        <span class="text-[10px] font-bold text-slate-400 block mt-0.5">
                                            {{ $report->initiative ? $report->initiative->title : 'General Report' }}
                                        </span>
                                        
                                        <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 mt-2">
                                            {{ $report->initiative ? Str::limit($report->initiative->description, 80) : 'Official accomplishment report.' }}
                                        </p>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between mt-4 text-[10px] font-black uppercase tracking-wider text-slate-400 font-mono">
                                        <span>{{ $report->reporting_period->format('M Y') }}</span>
                                        <button @click='selectedReport = {
                                                    title: "{{ addslashes($report->report_title) }}",
                                                    initiative: "{{ $report->initiative ? addslashes($report->initiative->title) : "General Report" }}",
                                                    period: "{{ $report->reporting_period->format("M d, Y") }}",
                                                    file: "{{ asset("storage/" . $report->file_path) }}",
                                                    is_image: {{ $isImage ? "true" : "false" }},
                                                    extension: "{{ strtoupper($extension) }}",
                                                    description: "{{ $report->initiative ? addslashes($report->initiative->description) : "Official quarterly accomplishment reports and updates." }}"
                                                }' 
                                                class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 transition active:scale-95 font-sans font-bold uppercase">
                                            <span>See More</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- See More Button (only visible when ActiveInitiativeId is 'all') -->
                        <div x-show="activeInitiativeId === 'all' && {{ $accomplishmentReports->count() }} > 3" class="text-center pt-4">
                            <button @click="showAll = !showAll" 
                                    class="inline-flex items-center space-x-1.5 px-4 py-2 border border-slate-200 text-slate-600 hover:text-[#1e40af] hover:border-[#1e40af] font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-95">
                                <span x-text="showAll ? 'Show Less' : 'See More'">See More</span>
                                <svg class="w-3.5 h-3.5 transition transform" :class="showAll ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

    <!-- Accomplishment Report Detail Modal -->
    <div x-show="selectedReport !== null" 
         class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center" 
         style="display: none;"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
             @click="selectedReport = null"></div>

        <!-- Modal Card -->
        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-lg mx-auto z-10 border border-slate-100 max-h-[90vh] flex flex-col"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
             
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-6 py-4 flex items-center justify-between text-white shrink-0">
                <div class="pr-4">
                    <span class="text-[9px] font-black uppercase tracking-widest text-blue-200">Transparency Log</span>
                    <h3 class="text-sm font-extrabold uppercase tracking-wide font-display mt-0.5" x-text="selectedReport ? selectedReport.title : ''"></h3>
                </div>
                <button @click="selectedReport = null" class="text-white/80 hover:text-white transition active:scale-95 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-5 overflow-y-auto flex-1 text-xs">
                <!-- If the report file is an image, display it -->
                <div x-show="selectedReport && selectedReport.is_image" class="w-full rounded-2xl overflow-hidden border border-slate-100 bg-slate-50 flex justify-center items-center p-2">
                    <img :src="selectedReport ? selectedReport.file : ''" class="max-w-full max-h-64 object-contain rounded-xl shadow-sm" alt="Report image">
                </div>

                <!-- If the report is a document, show preview icon box -->
                <div x-show="selectedReport && !selectedReport.is_image" class="w-full h-32 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center relative select-none">
                    <div class="text-center space-y-1.5">
                        <span class="text-3xl block" x-text="selectedReport ? (selectedReport.extension === 'PDF' ? '📕' : (selectedReport.extension === 'XLS' || selectedReport.extension === 'XLSX' ? '📊' : '📘')) : '📝'"></span>
                        <span class="text-[10px] font-black uppercase text-slate-400 font-mono tracking-wider" x-text="selectedReport ? selectedReport.extension : ''"></span>
                    </div>
                </div>

                <!-- Report Details -->
                <div class="space-y-4 pt-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block font-display">Assigned Program</span>
                            <span class="font-bold text-slate-800 text-xs" x-text="selectedReport ? selectedReport.initiative : ''"></span>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block font-display">Reporting Date</span>
                            <span class="font-bold text-slate-800 text-xs" x-text="selectedReport ? selectedReport.period : ''"></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1 font-display">Description</span>
                        <p class="text-[11px] text-slate-600 leading-relaxed font-medium" x-text="selectedReport ? selectedReport.description : ''"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-4 flex items-center justify-between border-t border-slate-100 shrink-0">
                <button @click="selectedReport = null" class="px-4 py-2 border border-slate-200 text-slate-600 hover:text-slate-900 font-bold rounded-xl text-[10px] uppercase tracking-wider transition active:scale-95">
                    Close
                </button>
                <a :href="selectedReport ? selectedReport.file : '#'" 
                   target="_blank" 
                   class="inline-flex items-center space-x-1.5 px-4 py-2 bg-[#1e40af] text-white hover:bg-blue-700 font-bold rounded-xl text-[10px] uppercase tracking-wider transition active:scale-95">
                    <span>Open / Download File</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
