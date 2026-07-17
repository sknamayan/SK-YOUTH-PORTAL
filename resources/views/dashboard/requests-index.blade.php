@extends('layouts.app')

@section('content')
<div x-data="{
    activeTab: '{{ $activeTab }}',
    stats: {
        all: {{ json_encode($allStats) }},
        archive: {{ json_encode($archivedStats) }},
        @foreach($initiatives as $init)
            init_{{ $init->id }}: {{ json_encode($init->stats) }},
        @endforeach
    },
    setActiveTab(tab) {
        this.activeTab = tab;
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
        window.location.search = '?tab=' + tab;
    }
}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar Navigation -->
    @include('layouts.dashboard-sidebar')

    <!-- Overlay back shadow on mobile -->

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Mobile Sidebar Trigger Header -->

        <!-- Page Main Wrapper -->
        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto font-sans">
            
            <!-- Breadcrumbs / Overview Top Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <span class="text-slate-400">Dashboard</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-700">Requests</span>
                </div>
            </div>

            <!-- Page Title / Headers -->
            <div>
                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Citizen Submissions</span>
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight font-display mt-0.5">Service Requests Evaluation</h1>
                <p class="text-slate-450 text-xs mt-1">Search requestor profiles, review detailed logs, or download data files directly.</p>
            </div>

            <!-- Dashboard Analytics Quick Widgets -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Total Submissions Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition border-l-4 border-l-blue-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="logs" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Total Submissions</span>
                    </div>
                    <div class="mt-3 font-sans">
                        <span class="block text-xl font-black font-display text-slate-700" x-text="stats[activeTab].total"></span>
                        <span class="text-[9px] font-bold text-slate-450 uppercase tracking-wider">All Records</span>
                    </div>
                </div>

                <!-- Action Required Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition border-l-4 border-l-amber-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="logs" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Pending Review</span>
                    </div>
                    <div class="mt-3 font-sans">
                        <span class="block text-xl font-black font-display text-amber-600" x-text="stats[activeTab].pending"></span>
                        <span class="text-[9px] font-bold text-slate-455 uppercase tracking-wider">Action Required</span>
                    </div>
                </div>

                <!-- Approved / Completed Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition border-l-4 border-l-emerald-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="logs" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Approved / Active</span>
                    </div>
                    <div class="mt-3 font-sans">
                        <span class="block text-xl font-black font-display text-emerald-600" x-text="stats[activeTab].approved"></span>
                        <span class="text-[9px] font-bold text-slate-455 uppercase tracking-wider">Completed / Scheduled</span>
                    </div>
                </div>

                <!-- Declined Card -->
                <div class="card p-5 bg-white border border-slate-100 rounded-3xl shadow-sm flex flex-col justify-between hover:shadow-md transition border-l-4 border-l-rose-500">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <x-category-icon name="logs" class="w-4.5 h-4.5" />
                        </div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Declined</span>
                    </div>
                    <div class="mt-3 font-sans">
                        <span class="block text-xl font-black font-display text-rose-600" x-text="stats[activeTab].declined"></span>
                        <span class="text-[9px] font-bold text-slate-455 uppercase tracking-wider">Rejected Requests</span>
                    </div>
                </div>
            </div>

            <!-- Tabbed Requests Database Grid -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200">
                    <!-- Tab buttons -->
                    <div class="flex overflow-x-auto whitespace-nowrap scrollbar-hide text-xs font-bold uppercase tracking-wider min-w-0">
                        <button @click="setActiveTab('all')"
                                :class="activeTab === 'all' ? 'border-[#1e40af] text-[#1e40af]' : 'border-transparent text-slate-400 hover:text-slate-650'"
                                class="py-3 px-5 border-b-2 transition select-none flex items-center space-x-1.5 cursor-pointer shrink-0 min-w-0 max-w-full">
                            <x-category-icon name="logs" class="w-4 h-4" />
                            <span>All Submissions ({{ $allStats['total'] }})</span>
                            @if($allStats['pending'] > 0)
                                <span class="w-2 h-2 bg-rose-600 rounded-full inline-block shadow-sm animate-pulse ml-1 shrink-0"></span>
                            @endif
                        </button>
                         @foreach($initiatives as $init)
                             @if(in_array($init->form_route, ['forms.health.create','forms.mental-health.create','forms.medicine.create','forms.silid.create','forms.sports.create']))
                                 @continue
                             @endif
                            @php
                                $icon = match($init->committee_id) {
                                    1 => 'education',
                                    2 => 'health',
                                    3 => 'medicine',
                                    4 => 'sports',
                                    default => 'logs'
                                };
                            @endphp
                            <button @click="setActiveTab('init_{{ $init->id }}')"
                                    :class="activeTab === 'init_{{ $init->id }}' ? 'border-[#1e40af] text-[#1e40af]' : 'border-transparent text-slate-400 hover:text-slate-650'"
                                    class="py-3 px-5 border-b-2 transition select-none flex items-center space-x-1.5 cursor-pointer shrink-0 min-w-0 max-w-full">
                                <x-category-icon name="{{ $icon }}" class="w-4 h-4" />
                                <span>{{ $init->title }} ({{ $init->stats['total'] }})</span>
                                @if($init->stats['pending'] > 0)
                                    <span class="w-2 h-2 bg-rose-600 rounded-full inline-block shadow-sm animate-pulse ml-1 shrink-0"></span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    </div>
                </div>

                <!-- Table Search & Status Filter Bar -->
                <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm">
                    <form id="filterForm" method="GET" action="{{ route('dashboard.requests.index') }}" class="space-y-4">
                        <input type="hidden" name="tab" :value="activeTab">
                        <input type="hidden" name="division" id="divisionFilterInput" value="{{ $divisionFilter ?? '' }}">
                        
                        <!-- Row 1: Search, Status, Year -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-6 relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search by requestor name or email..." class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans placeholder-slate-400">
                            </div>

                            <!-- Status Dropdown -->
                            <div class="md:col-span-3 relative">
                                <select name="status" onchange="this.form.submit()" class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="declined" {{ $status == 'declined' ? 'selected' : '' }}>Declined</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Year Dropdown -->
                            <div class="md:col-span-3 relative">
                                <select name="year" onchange="this.form.submit()" class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none">
                                    <option value="">All Submission Years</option>
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>


                        <!-- Row 2: Limit & Clear Filters -->
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-t border-slate-100 pt-4 mt-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <!-- Page Size Limit select -->
                                <div class="relative w-32 shrink-0">
                                    <select name="limit" onchange="this.form.submit()" class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold">
                                        <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 rows</option>
                                        <option value="15" {{ $limit == 15 ? 'selected' : '' }}>15 rows</option>
                                        <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25 rows</option>
                                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 rows</option>
                                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 rows</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>

                                <!-- Reset Filter Link -->
                                @if($search || $status || $yearFilter || $limit != 10)
                                    <a href="{{ route('dashboard.requests.index', ['tab' => $activeTab]) }}" class="inline-flex items-center text-[11px] font-bold text-slate-450 hover:text-slate-600 transition space-x-1 select-none cursor-pointer pl-2 py-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                        <span>Reset Filter</span>
                                    </a>
                                @endif
                            </div>

                            <!-- Right Export CSV trigger -->
                            <div class="flex items-center space-x-3">
                                <button type="button" @click="setActiveTab(activeTab === 'archive' ? 'all' : 'archive')"
                                        :class="activeTab === 'archive' ? 'bg-[#1e40af] text-white shadow-sm border-transparent' : 'bg-slate-100 hover:bg-slate-200/80 text-slate-650 border border-slate-250'"
                                        class="px-4 py-2 rounded-2xl text-[11px] font-black transition flex items-center space-x-1.5 cursor-pointer uppercase tracking-wider h-[38px] select-none">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <span>Archive ({{ $archivedStats['total'] }})</span>
                                    @if($archivedStats['pending'] > 0)
                                        <span class="w-2 h-2 bg-rose-500 rounded-full inline-block shadow-sm animate-pulse shrink-0"></span>
                                    @endif
                                </button>

                                <button type="submit" class="hidden"></button>
                                <a :href="`{{ url('/dashboard/export') }}/${activeTab}`" class="btn-primary text-[11px] font-black uppercase py-2 px-5 flex items-center space-x-1.5 cursor-pointer bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition shadow-sm border border-transparent rounded-2xl h-[38px]">
                                    <span>Export CSV</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tab Panels Contents -->
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                    
                    @if($paginatedRequests->isEmpty())
                        <div class="text-center py-12 text-slate-400 text-xs">No records match the search filter.</div>
                    @else
                        @if($activeTab === 'all' || $activeTab === 'archive')
                            <!-- Unified Merged Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-4 px-6">Submitted</th>
                                            <th class="py-4 px-6">Name</th>
                                            <th class="py-4 px-6">Form / Service</th>
                                            <th class="py-4 px-6">Email Address</th>
                                            <th class="py-4 px-6 text-center">Status</th>
                                            <th class="py-4 px-6">Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-600">
                                        @foreach($paginatedRequests as $req)
                                            <tr class="hover:bg-slate-50/50 transition duration-150">
                                                <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                    <a href="{{ route('dashboard.requests.show', [$req->type, $req->id]) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                                </td>
                                                <td class="py-4 px-6 font-semibold">
                                                    {{ $req->type_name }}
                                                    @if($req->type === 'sports' && $req->division)
                                                        <span class="block text-[10px] text-[#1e40af] font-black uppercase tracking-wide mt-0.5">{{ $req->division }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-4 px-6 font-mono">{{ $req->email }}</td>
                                                <td class="py-4 px-6 text-center">
                                                    <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                                </td>
                                                <td class="py-4 px-6 font-semibold text-slate-700">
                                                    @if(in_array($req->status, ['approved', 'declined']))
                                                        {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                                    @else
                                                        <span class="text-slate-400 font-medium">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($activeInitiative)
                            @if($activeInitiative->form_route === 'forms.health.create' || $activeInitiative->form_route === 'forms.mental-health.create')
                                <!-- Health Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                                <th class="py-4 px-6">Submitted</th>
                                                <th class="py-4 px-6">Name</th>
                                                <th class="py-4 px-6">Age/Gender</th>
                                                <th class="py-4 px-6">Email & Contact</th>
                                                <th class="py-4 px-6 text-center">Schedule</th>
                                                <th class="py-4 px-6 text-center">Status</th>
                                                <th class="py-4 px-6">Processed By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            @foreach($paginatedRequests as $req)
                                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                                    <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                    <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                        <a href="{{ route('dashboard.requests.show', ['health', $req->id]) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold">{{ $req->age }} / <span class="capitalize">{{ $req->gender }}</span></td>
                                                    <td class="py-4 px-6 font-mono">{{ $req->email }}<br><span class="text-slate-400 text-[10px] font-semibold">{{ $req->contact_number }}</span></td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="block font-bold text-slate-800">{{ $req->preferred_date->format('M d, Y') }}</span>
                                                        <span class="block text-[10px] text-[#1e40af] font-semibold mt-0.5">{{ $req->preferred_time }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold text-slate-700">
                                                        @if(in_array($req->status, ['approved', 'declined']))
                                                            {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                                        @else
                                                            <span class="text-slate-400 font-medium">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($activeInitiative->form_route === 'forms.medicine.create')
                                <!-- Medicine Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                                <th class="py-4 px-6">Submitted</th>
                                                <th class="py-4 px-6">Requestor Name</th>
                                                <th class="py-4 px-6">Age/Gender</th>
                                                <th class="py-4 px-6">Contact & Email</th>
                                                <th class="py-4 px-6">Complete Delivery Address</th>
                                                <th class="py-4 px-6 text-center">Status</th>
                                                <th class="py-4 px-6">Processed By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            @foreach($paginatedRequests as $req)
                                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                                    <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                    <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                        <a href="{{ route('dashboard.requests.show', ['medicine', $req->id]) }}">{{ $req->requestor_last_name }}, {{ $req->requestor_first_name }}</a>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold">{{ $req->requestor_age }} / <span class="capitalize">{{ $req->requestor_gender }}</span></td>
                                                    <td class="py-4 px-6 font-mono">{{ $req->email }}<br><span class="text-slate-400 text-[10px] font-semibold">{{ $req->contact_number }}</span></td>
                                                    <td class="py-4 px-6 font-medium text-slate-650 max-w-[200px] truncate" title="{{ $req->complete_address }}">{{ $req->complete_address }}</td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold text-slate-700">
                                                        @if(in_array($req->status, ['approved', 'declined']))
                                                            {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                                        @else
                                                            <span class="text-slate-400 font-medium">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($activeInitiative->form_route === 'forms.silid.create')
                                <!-- Silid Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                                <th class="py-4 px-6">Submitted</th>
                                                <th class="py-4 px-6">Requestor Name</th>
                                                <th class="py-4 px-6">Age</th>
                                                <th class="py-4 px-6">Contact & Email</th>
                                                <th class="py-4 px-6 text-center">Booking Details</th>
                                                <th class="py-4 px-6 text-center">Status</th>
                                                <th class="py-4 px-6">Processed By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            @foreach($paginatedRequests as $req)
                                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                                    <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                    <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                        <a href="{{ route('dashboard.requests.show', ['silid', $req->id]) }}">{{ $req->requestor_last_name }}, {{ $req->requestor_first_name }}</a>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold">{{ $req->requestor_age }} yrs</td>
                                                    <td class="py-4 px-6 font-mono">{{ $req->email }}<br><span class="text-slate-400 text-[10px] font-semibold">{{ $req->contact_number }}</span></td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="block font-bold text-slate-800">{{ $req->preferred_date->format('M d, Y') }}</span>
                                                        <span class="block text-[10px] text-[#1e40af] font-semibold mt-0.5">{{ $req->preferred_time }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold text-slate-700">
                                                        @if(in_array($req->status, ['approved', 'declined']))
                                                            {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                                        @else
                                                            <span class="text-slate-400 font-medium">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($activeInitiative->form_route === 'forms.sports.create')
                                <!-- Sports Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                                <th class="py-4 px-6">Submitted</th>
                                                <th class="py-4 px-6">Participant Name</th>
                                                <th class="py-4 px-6">Age/Gender</th>
                                                <th class="py-4 px-6">Tournament Details</th>
                                                <th class="py-4 px-6 text-center">Schedule Date</th>
                                                <th class="py-4 px-6 text-center">Status</th>
                                                <th class="py-4 px-6">Processed By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            @foreach($paginatedRequests as $req)
                                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                                    <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                    <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                        <a href="{{ route('dashboard.requests.show', ['sports', $req->id]) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold">{{ $req->age }} / <span class="capitalize">{{ $req->gender }}</span></td>
                                                    <td class="py-4 px-6">
                                                        <span class="font-bold text-slate-800">{{ $req->sport }}</span>
                                                        @if($req->division)
                                                            <span class="block text-[10px] text-[#1e40af] font-black mt-0.5 uppercase tracking-wide">{{ $req->division }}</span>
                                                        @endif
                                                        <span class="block text-[10px] text-slate-400 font-semibold mt-0.5">Team: {{ $req->team_name ?? 'None' }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 text-center font-bold text-slate-800">{{ $req->event_date->format('M d, Y') }}</td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold text-slate-700">
                                                        @if(in_array($req->status, ['approved', 'declined']))
                                                            {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                                        @else
                                                            <span class="text-slate-400 font-medium">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <!-- Custom Requests Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-xs">
                                        <thead>
                                            <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                                <th class="py-4 px-6">Submitted</th>
                                                <th class="py-4 px-6">Name</th>
                                                <th class="py-4 px-6">Email Address</th>
                                                <th class="py-4 px-6 text-center">Status</th>
                                                <th class="py-4 px-6">Processed By</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            @foreach($paginatedRequests as $req)
                                                <tr class="hover:bg-slate-50/50 transition duration-150">
                                                    <td class="py-4 px-6 text-[10px] text-slate-400 font-semibold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                                    <td class="py-4 px-6 font-bold text-slate-800 hover:text-[#1e40af] transition">
                                                        <a href="{{ route('dashboard.requests.show', ['custom', $req->id]) }}">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                                    </td>
                                                    <td class="py-4 px-6 font-mono">{{ $req->email }}</td>
                                                    <td class="py-4 px-6 text-center">
                                                        <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                                    </td>
                                                    <td class="py-4 px-6 font-semibold text-slate-700">
                                                        @if(in_array($req->status, ['approved', 'declined']))
                                                            {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                                        @else
                                                            <span class="text-slate-400 font-medium">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        @endif

                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $paginatedRequests->links() }}
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

</div>
@endsection
