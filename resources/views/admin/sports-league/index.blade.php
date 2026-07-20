@extends('layouts.app')

@section('content')
<div x-data="{
    showDeleteModal: false,
    deleteActionUrl: '',
    showViewModal: false,
    selectedReq: null,
    showRegisterModal: {{ session('failed_modal') === 'register' ? 'true' : 'false' }},
    adminRegStep: {{ session('failed_modal') === 'register' ? '5' : '1' }},
    adminSport: '{{ old('sport', 'Basketball') }}',
    adminDivision: '{{ old('division', 'Midget') }}',
    adminAge: {{ old('age', 18) }},
    showEditModal: {{ session('failed_modal') === 'edit' ? 'true' : 'false' }},
    editReq: {{ session('failed_modal') === 'edit' ? json_encode([
        'id' => session('failed_id'),
        'first_name' => old('first_name'),
        'last_name' => old('last_name'),
        'middle_name' => old('middle_name'),
        'age' => old('age'),
        'gender' => old('gender'),
        'email' => old('email'),
        'contact_number' => old('contact_number'),
        'address' => old('address'),
        'sport' => old('sport'),
        'division' => old('division'),
        'team_name' => old('team_name'),
        'position' => old('position'),
        'remarks' => old('remarks'),
        'kk_profiling_status' => old('kk_profiling_status'),
        'consent_waiver' => old('consent_waiver') ? true : false,
        'health_declaration' => old('health_declaration'),
        'guardian_first_name' => old('guardian_first_name'),
        'guardian_middle_name' => old('guardian_middle_name'),
        'guardian_last_name' => old('guardian_last_name'),
        'guardian_age' => old('guardian_age'),
        'guardian_relation' => old('guardian_relation'),
        'guardian_contact_number' => old('guardian_contact_number'),
        'guardian_address' => old('guardian_address'),
    ]) : '{}' }},
    validateAdminRegStep(s) {
        const fields = document.querySelectorAll(`#admin-reg-step-${s} [required]`);
        let valid = true;
        let firstInvalid = null;
        fields.forEach(field => {
            const rect = field.getBoundingClientRect();
            if (rect.width === 0 && rect.height === 0) {
                return;
            }
            if (!field.value || !field.value.trim() || !field.checkValidity()) {
                valid = false;
                if (!firstInvalid) firstInvalid = field;
            }
        });
        if (!valid && firstInvalid) {
            firstInvalid.reportValidity();
            firstInvalid.focus();
        }
        return valid;
    },
    nextAdminRegStep() {
        if (this.validateAdminRegStep(this.adminRegStep)) {
            this.adminRegStep++;
        }
    },
    prevAdminRegStep() {
        this.adminRegStep--;
    },
    confirmDelete(url) {
        this.deleteActionUrl = url;
        this.showDeleteModal = true;
    }
}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <!-- Main Pane -->
    <div class="flex-1 flex flex-col min-w-0">

        <div class="px-4 py-6 sm:p-8 pb-24 md:pb-8 space-y-6 flex-1 overflow-y-auto">

            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">SK Sports Committee</span>
                    <h1 class="text-xl font-black text-slate-800 font-display uppercase tracking-tight">SIKLAB Console</h1>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-250 rounded-2xl flex items-start gap-3 shadow-sm">
                    <span class="text-emerald-500 font-bold text-base">✓</span>
                    <div>
                        <h4 class="text-xs font-bold text-emerald-800 uppercase tracking-wide">Operation Successful</h4>
                        <p class="text-xs text-emerald-600 mt-0.5 font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-250 rounded-2xl flex items-start gap-3 shadow-sm">
                    <span class="text-rose-500 font-bold text-base">⚠</span>
                    <div>
                        <h4 class="text-xs font-bold text-rose-800 uppercase tracking-wide">Operation Failed</h4>
                        <p class="text-xs text-rose-600 mt-0.5 font-semibold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Top Navigation Tabs (Alpine.js Powered) -->
            <div x-data="{ activeTab: '{{ $status ?: 'approved' }}' }" class="flex border-b border-slate-200 dark:border-slate-800 overflow-x-auto no-scrollbar">
                <!-- Active Registry Tab -->
                <button @click="activeTab = 'approved'; window.location.href = '{{ route('admin.sports-league.index', ['status' => 'approved', 'search' => $search, 'year' => $yearFilter, 'division' => $divisionFilter, 'limit' => $limit]) }}'"
                        class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-150 flex items-center gap-2 border-b-2 shrink-0 select-none cursor-pointer"
                        :class="activeTab === 'approved' ? 'border-blue-600 text-blue-600 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'">
                    <span>Active Registry</span>
                    <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $approvedCount }}</span>
                </button>

                <!-- Pending Tab -->
                <button @click="activeTab = 'pending'; window.location.href = '{{ route('admin.sports-league.index', ['status' => 'pending', 'search' => $search, 'year' => $yearFilter, 'division' => $divisionFilter, 'limit' => $limit]) }}'"
                        class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-150 flex items-center gap-2 border-b-2 shrink-0 select-none cursor-pointer"
                        :class="activeTab === 'pending' ? 'border-blue-600 text-blue-600 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'">
                    <span>Pending</span>
                    <span class="bg-slate-100 text-slate-750 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                </button>

                <!-- Under Review Tab -->
                <button @click="activeTab = 'review'; window.location.href = '{{ route('admin.sports-league.index', ['status' => 'review', 'search' => $search, 'year' => $yearFilter, 'division' => $divisionFilter, 'limit' => $limit]) }}'"
                        class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-150 flex items-center gap-2 border-b-2 shrink-0 select-none cursor-pointer"
                        :class="activeTab === 'review' ? 'border-blue-600 text-blue-600 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'">
                    <span>Under Review</span>
                    <span class="bg-amber-100 text-amber-700 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $underReviewCount }}</span>
                </button>

                <!-- Rejected Tab -->
                <button @click="activeTab = 'rejected'; window.location.href = '{{ route('admin.sports-league.index', ['status' => 'rejected', 'search' => $search, 'year' => $yearFilter, 'division' => $divisionFilter, 'limit' => $limit]) }}'"
                        class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-150 flex items-center gap-2 border-b-2 shrink-0 select-none cursor-pointer"
                        :class="activeTab === 'rejected' ? 'border-blue-600 text-blue-600 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'">
                    <span>Rejected</span>
                    <span class="bg-red-100 text-red-700 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $rejectedCount }}</span>
                </button>

                <!-- Completed Tab -->
                <button @click="activeTab = 'completed'; window.location.href = '{{ route('admin.sports-league.index', ['status' => 'completed', 'search' => $search, 'year' => $yearFilter, 'division' => $divisionFilter, 'limit' => $limit]) }}'"
                        class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider transition-all duration-150 flex items-center gap-2 border-b-2 shrink-0 select-none cursor-pointer"
                        :class="activeTab === 'completed' ? 'border-blue-600 text-blue-600 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'">
                    <span>Completed</span>
                    <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black px-2 py-0.5 rounded-full">{{ $completedCount }}</span>
                </button>
            </div>

            <!-- Filter Console (Dark Rounded Container) -->
            <div class="bg-slate-800 rounded-xl p-4 text-white">
                <form id="filterForm" method="GET" action="{{ route('admin.sports-league.index') }}" class="space-y-4">
                    <input type="hidden" name="status" value="{{ $status }}">

                    <!-- Top Row: Wide search bar left, dropdown filters right -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- Search Bar on the Left (Wide) -->
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search by participant name or email..."
                                   class="pl-9 pr-4 py-2 w-full bg-slate-700 border border-slate-600 rounded-lg text-xs outline-none focus:bg-slate-750 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-white placeholder-slate-400 font-sans">
                        </div>

                        <!-- Dropdown Filters on the Right -->
                        <div class="flex flex-wrap items-center gap-3 shrink-0">
                            <!-- Category / Division Dropdown -->
                            <div class="relative min-w-[180px]">
                                <select name="division" onchange="this.form.submit()"
                                        class="block w-full py-2 pl-3 pr-8 bg-slate-700 border border-slate-600 rounded-lg text-xs text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition cursor-pointer appearance-none font-semibold">
                                    <option value="">All Divisions</option>
                                    @foreach($leagues as $lg)
                                        @foreach($lg->registrationForms as $form)
                                            <option value="{{ $form->division_name }}" {{ $divisionFilter === $form->division_name ? 'selected' : '' }}>
                                                {{ $form->division_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Year Dropdown -->
                            <div class="relative min-w-[120px]">
                                <select name="year" onchange="this.form.submit()"
                                        class="block w-full py-2 pl-3 pr-8 bg-slate-700 border border-slate-600 rounded-lg text-xs text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition cursor-pointer appearance-none font-semibold">
                                    <option value="">All Years</option>
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Clear filters helper -->
                            @if($search || $divisionFilter || $yearFilter || $limit != 15)
                                <a href="{{ route('admin.sports-league.index', ['status' => $status]) }}"
                                   class="inline-flex items-center text-xs font-bold text-slate-350 hover:text-white transition gap-1 px-3 py-2 border border-slate-600 rounded-lg bg-slate-700/40 select-none cursor-pointer">
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Row: Limit left, Actions right -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-700">
                        <!-- Limit select -->
                        <div class="relative w-32 shrink-0">
                            <select name="limit" onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-700 border border-slate-600 rounded-lg text-xs text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition cursor-pointer appearance-none font-semibold">
                                <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 rows</option>
                                <option value="15" {{ $limit == 15 ? 'selected' : '' }}>15 rows</option>
                                <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25 rows</option>
                                <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 rows</option>
                                <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 rows</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <!-- Action Buttons Group (Right Aligned) -->
                        <div class="flex flex-wrap items-center gap-3 sm:ml-auto">
                            <!-- Tertiary: Archive -->
                            <button type="button"
                                    onclick="document.getElementById('bulkArchiveForm').submit()"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-slate-700/60 hover:bg-slate-700 border border-slate-600 hover:border-slate-500 text-slate-350 hover:text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition active:scale-95 select-none cursor-pointer h-9">
                                Archive
                            </button>

                            <!-- Secondary: Export CSV -->
                            <a href="{{ route('admin.sports-league.export', ['status' => $status, 'search' => $search, 'year' => $yearFilter, 'division' => $divisionFilter]) }}"
                               class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition active:scale-95 select-none cursor-pointer gap-1.5 shadow-sm h-9">
                                <span>Export CSV</span>
                            </a>

                            <!-- Primary: Register Citizen -->
                            <a href="{{ route('admin.sports-league.create') }}"
                               @click.prevent="showRegisterModal = true; adminRegStep = 1; adminSport = 'Basketball'; adminDivision = 'Midget'; adminAge = 18"
                               class="inline-flex items-center justify-center px-4 py-2 bg-[#1e40af] hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition active:scale-95 select-none cursor-pointer gap-1.5 shadow-sm h-9">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                <span>Register Citizen</span>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Hidden Bulk Archive Form -->
                <form id="bulkArchiveForm" action="{{ route('admin.sports-league.bulk-archive') }}" method="POST" class="hidden"
                      onsubmit="return confirm('Are you sure you want to archive all approved, rejected, and completed registrations? This action is soft-delete based.')">
                    @csrf
                </form>
            </div>

            <!-- Data Table Container -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                @if($paginatedRequests->isEmpty())
                    <div class="text-center py-12 text-slate-400 text-xs font-semibold">No registrations match the selected filters.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold tracking-wider">
                                    <th class="py-4 px-6 text-[10px] font-black uppercase">Submitted</th>
                                    <th class="py-4 px-6 text-[10px] font-black uppercase">Participant Name</th>
                                    <th class="py-4 px-6 text-[10px] font-black uppercase">Age/Gender</th>
                                    <th class="py-4 px-6 text-[10px] font-black uppercase">Tournament Details</th>
                                    <th class="py-4 px-6 text-center text-[10px] font-black uppercase">Schedule Date</th>
                                    <th class="py-4 px-6 text-center text-[10px] font-black uppercase">Status</th>
                                    <th class="py-4 px-6 text-[10px] font-black uppercase">Processed By</th>
                                    <th class="py-4 px-6 text-right text-[10px] font-black uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach($paginatedRequests as $req)
                                    @php
                                        $escapedRemarks = addslashes(str_replace(["\r", "\n"], ["", " "], $req->remarks ?? ''));
                                        $escapedHealth = addslashes(str_replace(["\r", "\n"], ["", " "], $req->health_declaration ?? ''));
                                        $escapedAddress = addslashes(str_replace(["\r", "\n"], ["", " "], $req->address ?? ''));
                                        $escapedGuardianAddress = addslashes(str_replace(["\r", "\n"], ["", " "], $req->guardian_address ?? ''));
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition duration-150 border-b border-slate-200">
                                        <td class="py-4 px-6 text-[10px] text-slate-405 font-bold uppercase">{{ $req->created_at->format('M d, Y') }}</td>
                                        <td class="py-4 px-6 font-bold text-slate-800 hover:text-blue-600 transition">
                                            <a href="{{ route('admin.sports-league.show', $req->id) }}"
                                               @click.prevent="selectedReq = {
                                                   id: '{{ $req->id }}',
                                                   first_name: '{{ addslashes($req->first_name) }}',
                                                   last_name: '{{ addslashes($req->last_name) }}',
                                                   middle_name: '{{ addslashes($req->middle_name) }}',
                                                   age: {{ $req->age }},
                                                   gender: '{{ $req->gender }}',
                                                   email: '{{ $req->email }}',
                                                   contact_number: '{{ $req->contact_number }}',
                                                   address: '{{ $escapedAddress }}',
                                                   sport: '{{ $req->sport }}',
                                                   division: '{{ $req->division }}',
                                                   team_name: '{{ addslashes($req->team_name) }}',
                                                   position: '{{ addslashes($req->position) }}',
                                                   remarks: '{{ $escapedRemarks }}',
                                                   kk_profiling_status: '{{ $req->kk_profiling_status }}',
                                                   consent_waiver: {{ $req->consent_waiver ? 'true' : 'false' }},
                                                   health_declaration: '{{ $escapedHealth }}',
                                                   profile_picture_url: '{{ $req->profile_picture ? Storage::url($req->profile_picture) : '' }}',
                                                   voter_cert_url: '{{ $req->voter_cert ? Storage::url($req->voter_cert) : '' }}',
                                                   guardian_gov_id_url: '{{ $req->guardian_gov_id ? Storage::url($req->guardian_gov_id) : '' }}',
                                                   guardian_first_name: '{{ addslashes($req->guardian_first_name) }}',
                                                   guardian_middle_name: '{{ addslashes($req->guardian_middle_name) }}',
                                                   guardian_last_name: '{{ addslashes($req->guardian_last_name) }}',
                                                   guardian_age: '{{ $req->guardian_age }}',
                                                   guardian_relation: '{{ addslashes($req->guardian_relation) }}',
                                                   guardian_contact_number: '{{ $req->guardian_contact_number }}',
                                                   guardian_address: '{{ $escapedGuardianAddress }}',
                                                   status: '{{ $req->status }}',
                                                   processed_by_name: '{{ $req->processedBy ? addslashes($req->processedBy->name) : 'Desk Officer' }}',
                                                   event_date_formatted: '{{ $req->event_date instanceof \Carbon\Carbon ? $req->event_date->format('M d, Y') : $req->event_date }}',
                                                   created_at_formatted: '{{ $req->created_at->format('M d, Y') }}'
                                               }; showViewModal = true">{{ $req->last_name }}, {{ $req->first_name }}</a>
                                        </td>
                                        <td class="py-4 px-6 font-semibold">{{ $req->age ?? 'N/A' }} / <span class="capitalize">{{ $req->gender ?? 'N/A' }}</span></td>
                                        <td class="py-4 px-6">
                                            <span class="font-bold text-slate-800">{{ $req->sport ?? 'N/A' }}</span>
                                            @if($req->division)
                                                <span class="block text-[9px] text-blue-600 font-black mt-0.5 uppercase tracking-wide">{{ $req->division }}</span>
                                            @endif
                                            @if($req->team_name)
                                                <span class="block text-[9px] text-slate-400 font-semibold mt-0.5">Team: {{ $req->team_name }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-center font-bold text-slate-800">{{ $req->event_date instanceof \Carbon\Carbon ? $req->event_date->format('M d, Y') : $req->event_date }}</td>
                                        <td class="py-4 px-6 text-center select-none">
                                            @php
                                                $normalizedStatus = strtolower($req->status);
                                            @endphp
                                            @if($normalizedStatus === 'pending')
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-600 border border-amber-500/25">Pending</span>
                                            @elseif($normalizedStatus === 'review' || $normalizedStatus === 'under review' || $normalizedStatus === 'under_review')
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-600 border border-blue-500/25">Under Review</span>
                                            @elseif($normalizedStatus === 'approved')
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 border border-emerald-500/25">Approved</span>
                                            @elseif($normalizedStatus === 'rejected' || $normalizedStatus === 'declined')
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-600 border border-rose-500/25">Rejected</span>
                                            @elseif($normalizedStatus === 'completed')
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-600 border border-indigo-500/25">Completed</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-500/10 text-slate-600 border border-slate-500/25">{{ $req->status }}</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-700">
                                            @if(in_array($normalizedStatus, ['approved', 'rejected', 'declined', 'completed']))
                                                {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                            @else
                                                <span class="text-slate-400 font-medium">-</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right space-x-1.5 whitespace-nowrap">
                                            <a href="{{ route('admin.sports-league.show', $req->id) }}"
                                               @click.prevent="selectedReq = {
                                                   id: '{{ $req->id }}',
                                                   first_name: '{{ addslashes($req->first_name) }}',
                                                   last_name: '{{ addslashes($req->last_name) }}',
                                                   middle_name: '{{ addslashes($req->middle_name) }}',
                                                   age: {{ $req->age }},
                                                   gender: '{{ $req->gender }}',
                                                   email: '{{ $req->email }}',
                                                   contact_number: '{{ $req->contact_number }}',
                                                   address: '{{ $escapedAddress }}',
                                                   sport: '{{ $req->sport }}',
                                                   division: '{{ $req->division }}',
                                                   team_name: '{{ addslashes($req->team_name) }}',
                                                   position: '{{ addslashes($req->position) }}',
                                                   remarks: '{{ $escapedRemarks }}',
                                                   kk_profiling_status: '{{ $req->kk_profiling_status }}',
                                                   consent_waiver: {{ $req->consent_waiver ? 'true' : 'false' }},
                                                   health_declaration: '{{ $escapedHealth }}',
                                                   profile_picture_url: '{{ $req->profile_picture ? Storage::url($req->profile_picture) : '' }}',
                                                   voter_cert_url: '{{ $req->voter_cert ? Storage::url($req->voter_cert) : '' }}',
                                                   guardian_gov_id_url: '{{ $req->guardian_gov_id ? Storage::url($req->guardian_gov_id) : '' }}',
                                                   guardian_first_name: '{{ addslashes($req->guardian_first_name) }}',
                                                   guardian_middle_name: '{{ addslashes($req->guardian_middle_name) }}',
                                                   guardian_last_name: '{{ addslashes($req->guardian_last_name) }}',
                                                   guardian_age: '{{ $req->guardian_age }}',
                                                   guardian_relation: '{{ addslashes($req->guardian_relation) }}',
                                                   guardian_contact_number: '{{ $req->guardian_contact_number }}',
                                                   guardian_address: '{{ $escapedGuardianAddress }}',
                                                   status: '{{ $req->status }}',
                                                   processed_by_name: '{{ $req->processedBy ? addslashes($req->processedBy->name) : 'Desk Officer' }}',
                                                   event_date_formatted: '{{ $req->event_date instanceof \Carbon\Carbon ? $req->event_date->format('M d, Y') : $req->event_date }}',
                                                   created_at_formatted: '{{ $req->created_at->format('M d, Y') }}'
                                               }; showViewModal = true"
                                               class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg font-bold text-[9px] uppercase tracking-wider transition">View</a>
                                            <a href="{{ route('admin.sports-league.edit', $req->id) }}"
                                               @click.prevent="editReq = {
                                                   id: '{{ $req->id }}',
                                                   first_name: '{{ addslashes($req->first_name) }}',
                                                   last_name: '{{ addslashes($req->last_name) }}',
                                                   middle_name: '{{ addslashes($req->middle_name) }}',
                                                   age: {{ $req->age }},
                                                   gender: '{{ $req->gender }}',
                                                   email: '{{ $req->email }}',
                                                   contact_number: '{{ $req->contact_number }}',
                                                   address: '{{ $escapedAddress }}',
                                                   sport: '{{ $req->sport }}',
                                                   division: '{{ $req->division }}',
                                                   team_name: '{{ addslashes($req->team_name) }}',
                                                   position: '{{ addslashes($req->position) }}',
                                                   remarks: '{{ $escapedRemarks }}',
                                                   kk_profiling_status: '{{ $req->kk_profiling_status }}',
                                                   consent_waiver: {{ $req->consent_waiver ? 'true' : 'false' }},
                                                   health_declaration: '{{ $escapedHealth }}',
                                                   guardian_first_name: '{{ addslashes($req->guardian_first_name) }}',
                                                   guardian_middle_name: '{{ addslashes($req->guardian_middle_name) }}',
                                                   guardian_last_name: '{{ addslashes($req->guardian_last_name) }}',
                                                   guardian_age: '{{ $req->guardian_age }}',
                                                   guardian_relation: '{{ addslashes($req->guardian_relation) }}',
                                                   guardian_contact_number: '{{ $req->guardian_contact_number }}',
                                                   guardian_address: '{{ $escapedGuardianAddress }}'
                                               }; showEditModal = true"
                                               class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg font-bold text-[9px] uppercase tracking-wider transition">Edit</a>
                                            <button type="button" @click="confirmDelete('{{ route('admin.sports-league.destroy', $req->id) }}')" class="inline-flex items-center px-2 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-lg font-bold text-[9px] uppercase tracking-wider transition cursor-pointer">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                        {{ $paginatedRequests->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
    <!-- Password Confirmation Modal for Deletion -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" x-cloak>
        <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 space-y-4 border border-slate-100">
            <div class="flex items-center space-x-3 text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-sm font-black uppercase tracking-wider font-display">Confirm Deletion</h3>
            </div>

            <p class="text-slate-500 text-xs leading-relaxed">
                Are you sure you want to permanently delete this registration? To prevent accidental deletion, please enter your administrator password to proceed.
            </p>

            <form :action="deleteActionUrl" method="POST" class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="admin_password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Confirm Admin Password</label>
                    <input type="password" name="password" id="admin_password" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-rose-500 transition" placeholder="••••••••">
                </div>

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" @click="showDeleteModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-250 text-slate-600 font-bold text-xs uppercase tracking-wider rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition">
                        Confirm Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Details Modal -->
    <div x-show="showViewModal" class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col border border-slate-100 overflow-hidden animate-fade-in-up" @click.away="showViewModal = false">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Registration Details</span>
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">
                        Participant: <span x-text="selectedReq ? selectedReq.last_name + ', ' + selectedReq.first_name + ' ' + (selectedReq.middle_name || '') : ''"></span>
                    </h3>
                </div>
                <button @click="showViewModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200/50 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-xs">
                <template x-if="selectedReq">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Left 2 Cols: Info Grid -->
                        <div class="md:col-span-2 space-y-6">
                            <!-- Section: Personal Info -->
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-slate-450 uppercase tracking-wider border-b pb-1 font-display">Personal Details</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Email</span>
                                        <span class="text-slate-800 font-semibold select-all font-mono" x-text="selectedReq.email"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Contact</span>
                                        <span class="text-slate-805 font-semibold select-all" x-text="selectedReq.contact_number"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Age / Gender</span>
                                        <span class="text-slate-800 font-semibold" x-text="selectedReq.age + ' yrs / ' + selectedReq.gender"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">KK Profiling Status</span>
                                        <span class="text-slate-800 font-semibold" x-text="'Registered: ' + selectedReq.kk_profiling_status"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Complete Address</span>
                                        <span class="text-slate-800 font-semibold" x-text="selectedReq.address"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Guardian Info (Only for minor) -->
                            <div x-show="selectedReq.age < 18" class="space-y-4 border-t border-slate-100 pt-4">
                                <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-wider border-b pb-1 font-display">Parent / Guardian Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Guardian Name</span>
                                        <span class="text-slate-800 font-bold" x-text="selectedReq.guardian_first_name + ' ' + (selectedReq.guardian_middle_name || '') + ' ' + selectedReq.guardian_last_name"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Relationship / Age</span>
                                        <span class="text-slate-800 font-semibold" x-text="selectedReq.guardian_relation + ' / ' + selectedReq.guardian_age + ' yrs'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Guardian Contact</span>
                                        <span class="text-slate-805 font-semibold" x-text="selectedReq.guardian_contact_number"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Guardian Address</span>
                                        <span class="text-slate-800 font-semibold" x-text="selectedReq.guardian_address"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Tournament Details -->
                            <div class="space-y-4 border-t border-slate-100 pt-4">
                                <h4 class="text-[10px] font-black text-[#1e40af] uppercase tracking-wider border-b pb-1 font-display">Tournament Selections</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Sport</span>
                                        <span class="text-slate-800 font-black uppercase text-sm" x-text="selectedReq.sport"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Division</span>
                                        <span class="text-blue-700 font-black uppercase text-sm" x-text="selectedReq.division"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Position</span>
                                        <span class="text-slate-800 font-semibold" x-text="selectedReq.position"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Team Name</span>
                                        <span class="text-slate-800 font-semibold" x-text="selectedReq.team_name || 'Individual'"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Health Declaration</span>
                                        <div class="p-2.5 bg-slate-50 border rounded-xl text-slate-700" x-text="selectedReq.health_declaration || 'None'"></div>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-0.5">Remarks / Configs</span>
                                        <div class="p-2.5 bg-slate-50 border rounded-xl text-slate-700" x-text="selectedReq.remarks || 'None'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right 1 Col: Documents & Actions -->
                        <div class="space-y-6">
                            <!-- Section: Document Attachments -->
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-slate-450 uppercase tracking-wider border-b pb-1 font-display">Attached Files</h4>
                                <div class="space-y-3">
                                    <!-- Profile Picture -->
                                    <div class="p-3 bg-slate-50 border rounded-xl space-y-2">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Profile Picture (2x2)</span>
                                        <template x-if="selectedReq.profile_picture_url">
                                            <div class="space-y-2">
                                                <div class="w-full aspect-video bg-slate-100 rounded-lg overflow-hidden border">
                                                    <img :src="selectedReq.profile_picture_url" class="w-full h-full object-cover" alt="Profile Picture">
                                                </div>
                                                <a :href="selectedReq.profile_picture_url" target="_blank" class="block w-full py-1 text-center bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[9px] uppercase rounded-lg transition">View Image</a>
                                            </div>
                                        </template>
                                        <template x-if="!selectedReq.profile_picture_url">
                                            <span class="text-slate-400 italic text-[10px]">No photo uploaded</span>
                                        </template>
                                    </div>

                                    <!-- Guardian ID (Minor) or Voter Cert (Adult) -->
                                    <div class="p-3 bg-slate-50 border rounded-xl space-y-2">
                                        <span class="block text-[9px] font-bold text-slate-400 uppercase" x-text="selectedReq.age < 18 ? 'Guardian Valid ID' : 'Voter Certificate' "></span>

                                        <!-- If Minor & Guardian ID uploaded -->
                                        <template x-if="selectedReq.age < 18 && selectedReq.guardian_gov_id_url">
                                            <div class="space-y-2">
                                                <div class="w-full aspect-video bg-slate-100 rounded-lg flex items-center justify-center border text-slate-300">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <a :href="selectedReq.guardian_gov_id_url" target="_blank" class="block w-full py-1 text-center bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[9px] uppercase rounded-lg transition">View Document</a>
                                            </div>
                                        </template>

                                        <!-- If Adult & Voter Cert uploaded -->
                                        <template x-if="selectedReq.age >= 18 && selectedReq.voter_cert_url">
                                            <div class="space-y-2">
                                                <div class="w-full aspect-video bg-slate-100 rounded-lg flex items-center justify-center border text-slate-300">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <a :href="selectedReq.voter_cert_url" target="_blank" class="block w-full py-1 text-center bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[9px] uppercase rounded-lg transition">View Document</a>
                                            </div>
                                        </template>

                                        <!-- If missing -->
                                        <template x-if="(selectedReq.age < 18 && !selectedReq.guardian_gov_id_url) || (selectedReq.age >= 18 && !selectedReq.voter_cert_url)">
                                            <span class="text-slate-400 italic text-[10px]">No verification document uploaded</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Console Decision Desk -->
                            <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                <h4 class="text-[9px] font-black text-slate-800 uppercase tracking-widest border-b pb-1">Decision Desk</h4>

                                <form :action="'/admin/sports-league/' + selectedReq.id + '/status/approved'" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <template x-if="selectedReq.status !== 'approved'">
                                        <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase text-[9px] rounded-lg tracking-wider transition">Approve Registration</button>
                                    </template>
                                </form>

                                <form :action="'/admin/sports-league/' + selectedReq.id + '/status/declined'" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <template x-if="selectedReq.status !== 'declined'">
                                        <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold uppercase text-[9px] rounded-lg tracking-wider transition">Decline Registration</button>
                                    </template>
                                </form>

                                <div class="border-t border-slate-200/60 pt-2.5 space-y-2">
                                    <!-- Edit Link -->
                                    <button type="button" @click="editReq = Object.assign({}, selectedReq); showEditModal = true; showViewModal = false" class="block w-full py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold uppercase text-[9px] rounded-lg tracking-wider transition">Edit Details</button>

                                    <!-- Delete Link -->
                                    <button type="button" @click="confirmDelete('/admin/sports-league/' + selectedReq.id); showViewModal = false" class="w-full py-2 bg-slate-250 hover:bg-rose-50 hover:text-rose-700 text-slate-600 font-bold uppercase text-[9px] rounded-lg tracking-wider transition">Delete Record</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" @click="showViewModal = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold uppercase tracking-wider text-[10px] rounded-xl transition">
                    Close Desk
                </button>
            </div>
        </div>
    </div>

    <!-- Register Citizen Modal (Wizard) -->
    <div x-show="showRegisterModal" class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col border border-slate-100 overflow-hidden animate-fade-in-up" @click.away="showRegisterModal = false">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Console Registration</span>
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Register Citizen</h3>
                </div>
                <button @click="showRegisterModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200/50 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="bg-slate-50 dark:bg-slate-800/40 px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between text-[10px] font-black uppercase tracking-wider shrink-0 select-none">
                <!-- Step 1 -->
                <div class="flex items-center space-x-1.5">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-colors"
                          :class="adminRegStep >= 1 ? 'bg-[#1e40af] text-white shadow-sm shadow-[#1e40af]/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-300 dark:border-slate-700'">1</span>
                    <span :class="adminRegStep >= 1 ? 'text-[#1e40af] dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-slate-500'">Sport</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 bg-slate-200 dark:bg-slate-800 transition-colors" :class="adminRegStep >= 2 ? 'bg-[#1e40af] dark:bg-blue-500' : ''"></div>

                <!-- Step 2 -->
                <div class="flex items-center space-x-1.5">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-colors"
                          :class="adminRegStep >= 2 ? 'bg-[#1e40af] text-white shadow-sm shadow-[#1e40af]/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-300 dark:border-slate-700'">2</span>
                    <span :class="adminRegStep >= 2 ? 'text-[#1e40af] dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-slate-500'">Division</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 bg-slate-200 dark:bg-slate-800 transition-colors" :class="adminRegStep >= 3 ? 'bg-[#1e40af] dark:bg-blue-500' : ''"></div>

                <!-- Step 3 -->
                <div class="flex items-center space-x-1.5">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-colors"
                          :class="adminRegStep >= 3 ? 'bg-[#1e40af] text-white shadow-sm shadow-[#1e40af]/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-300 dark:border-slate-700'">3</span>
                    <span :class="adminRegStep >= 3 ? 'text-[#1e40af] dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-slate-500'">Details</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 bg-slate-200 dark:bg-slate-800 transition-colors" :class="adminRegStep >= 4 ? 'bg-[#1e40af] dark:bg-blue-500' : ''"></div>

                <!-- Step 4 -->
                <div class="flex items-center space-x-1.5">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-colors"
                          :class="adminRegStep >= 4 ? 'bg-[#1e40af] text-white shadow-sm shadow-[#1e40af]/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-300 dark:border-slate-700'">4</span>
                    <span :class="adminRegStep >= 4 ? 'text-[#1e40af] dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-slate-500'">Files</span>
                </div>
                <div class="flex-1 h-0.5 mx-3 bg-slate-200 dark:bg-slate-800 transition-colors" :class="adminRegStep >= 5 ? 'bg-[#1e40af] dark:bg-blue-500' : ''"></div>

                <!-- Step 5 -->
                <div class="flex items-center space-x-1.5">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black transition-colors"
                          :class="adminRegStep >= 5 ? 'bg-[#1e40af] text-white shadow-sm shadow-[#1e40af]/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-655 dark:text-slate-400 border border-slate-300 dark:border-slate-700'">5</span>
                    <span :class="adminRegStep >= 5 ? 'text-[#1e40af] dark:text-blue-400 font-extrabold' : 'text-slate-400 dark:text-slate-500'">Consent</span>
                </div>
            </div>

            <!-- Form Container -->
            <form action="{{ route('admin.sports-league.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col overflow-hidden">
                @csrf

                <!-- Modal Scrollable Fields Area -->
                <div class="p-6 overflow-y-auto flex-1 space-y-4 text-xs">

                    <!-- STEP 1: Sport Selection -->
                    <div id="admin-reg-step-1" x-show="adminRegStep === 1" class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-2 font-display">Step 1: Choose Sport / Game</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Basketball Card -->
                            <div @click="adminSport = 'Basketball'"
                                 class="p-5 border rounded-3xl cursor-pointer transition-all duration-300 flex flex-col justify-between space-y-4 select-none relative"
                                 :class="adminSport === 'Basketball' ? 'border-[#1e40af] dark:border-blue-500 bg-blue-500/5 dark:bg-blue-950/40 ring-1 ring-blue-500/30' : 'border-slate-200 dark:border-slate-800 bg-[#1e293b]/10 hover:border-slate-400 dark:hover:border-slate-700'">

                                <div class="flex items-center justify-between">
                                    <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-display">BASKETBALL</span>
                                    <span class="text-[9px] text-slate-450 font-bold uppercase tracking-wider font-display">SIKAP AT ALAB NG BATANG NAMAYAN</span>
                                </div>

                                <div>
                                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider font-display">BASKETBALL TOURNAMENT</h4>
                                    <p class="text-[9px] text-slate-400 mt-1 leading-relaxed">Select a division below to start your registration. Minors must fill in guardian details.</p>
                                </div>

                                <div class="space-y-2 pt-2">
                                    <!-- Midget -->
                                    <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-850 rounded-xl p-2.5 flex items-center justify-between text-[9px]">
                                        <div>
                                            <span class="block font-bold text-slate-850 dark:text-slate-200">Midget Division</span>
                                            <span class="text-slate-400">Edad 6 hanggang 12 taong gulang</span>
                                        </div>
                                    </div>
                                    <!-- Juniors -->
                                    <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-850 rounded-xl p-2.5 flex items-center justify-between text-[9px]">
                                        <div>
                                            <span class="block font-bold text-slate-855 dark:text-slate-200">Juniors Division</span>
                                            <span class="text-slate-400">Edad 13 hanggang 17 taong gulang</span>
                                        </div>
                                    </div>
                                    <!-- Seniors -->
                                    <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-850 rounded-xl p-2.5 flex items-center justify-between text-[9px]">
                                        <div>
                                            <span class="block font-bold text-slate-855 dark:text-slate-200">Seniors Division</span>
                                            <span class="text-slate-400">Edad 18 hanggang 39 taong gulang</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Volleyball Card -->
                            <div @click="adminSport = 'Volleyball'"
                                 class="p-5 border rounded-3xl cursor-pointer transition-all duration-300 flex flex-col justify-between space-y-4 select-none relative"
                                 :class="adminSport === 'Volleyball' ? 'border-[#1e40af] dark:border-blue-500 bg-blue-500/5 dark:bg-blue-950/40 ring-1 ring-blue-500/30' : 'border-slate-200 dark:border-slate-800 bg-[#1e293b]/10 hover:border-slate-400 dark:hover:border-slate-700'">

                                <div class="flex items-center justify-between">
                                    <span class="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-display">VOLLEYBALL</span>
                                    <span class="text-[9px] text-slate-450 font-bold uppercase tracking-wider font-display">SIKAP AT ALAB NG BATANG NAMAYAN</span>
                                </div>

                                <div>
                                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider font-display">VOLLEYBALL TOURNAMENT</h4>
                                    <p class="text-[9px] text-slate-400 mt-1 leading-relaxed">Select a division below to start your registration. Minors must fill in guardian details.</p>
                                </div>

                                <div class="space-y-2 pt-2">
                                    <!-- Mens -->
                                    <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-850 rounded-xl p-2.5 flex items-center justify-between text-[9px]">
                                        <div>
                                            <span class="block font-bold text-slate-855 dark:text-slate-200">Men's Division</span>
                                            <span class="text-slate-400">Edad 15 pataas (Ages 15 and above)</span>
                                        </div>
                                    </div>
                                    <!-- Womens -->
                                    <div class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-850 rounded-xl p-2.5 flex items-center justify-between text-[9px]">
                                        <div>
                                            <span class="block font-bold text-slate-855 dark:text-slate-200">Women's Division</span>
                                            <span class="text-slate-400">Edad 15 pataas (Ages 15 and above)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs to submit selection -->
                        <input type="hidden" name="sport" :value="adminSport">
                    </div>

                    <!-- STEP 2: Division & Position Selection -->
                    <div id="admin-reg-step-2" x-show="adminRegStep === 2" class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 font-display">Step 2: Division and Team details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="admin_reg_division" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Division</label>
                                <select name="division" id="admin_reg_division" required x-model="adminDivision"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                    <template x-if="adminSport === 'Basketball'">
                                        <optgroup label="Basketball Divisions">
                                            <option value="Midget">Midget [Edad 6 hanggang 12]</option>
                                            <option value="Juniors">Juniors [Edad 13 hanggang 17]</option>
                                            <option value="Seniors">Seniors [Edad 18 hanggang 39]</option>
                                        </optgroup>
                                    </template>
                                    <template x-if="adminSport === 'Volleyball'">
                                        <optgroup label="Volleyball Divisions">
                                            <option value="Mens">Men's Division</option>
                                            <option value="Womens">Women's Division</option>
                                        </optgroup>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label for="admin_reg_position" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Position</label>
                                <input type="text" name="position" id="admin_reg_position" required value="{{ old('position') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850" placeholder="e.g. Guard, Libero, Setter">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="admin_reg_team" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Team Name (Optional)</label>
                                <input type="text" name="team_name" id="admin_reg_team" value="{{ old('team_name') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Participant Information -->
                    <div id="admin-reg-step-3" x-show="adminRegStep === 3" class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 font-display">Step 3: Citizen Information</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="admin_reg_fname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">First Name</label>
                                <input type="text" name="first_name" id="admin_reg_fname" required value="{{ old('first_name') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                            <div>
                                <label for="admin_reg_mname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Middle Name</label>
                                <input type="text" name="middle_name" id="admin_reg_mname" value="{{ old('middle_name') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                            <div>
                                <label for="admin_reg_lname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Last Name</label>
                                <input type="text" name="last_name" id="admin_reg_lname" required value="{{ old('last_name') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                            <div>
                                <label for="admin_reg_age" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Age</label>
                                <input type="number" name="age" id="admin_reg_age" required x-model.number="adminAge" value="{{ old('age', 18) }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                            <div>
                                <label for="admin_reg_gender" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</label>
                                <select name="gender" id="admin_reg_gender" required
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Prefer not to say" {{ old('gender') === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                            </div>
                            <div>
                                <label for="admin_reg_contact" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Contact</label>
                                <input type="text" name="contact_number" id="admin_reg_contact" required value="{{ old('contact_number') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="admin_reg_email" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                                <input type="email" name="email" id="admin_reg_email" required value="{{ old('email') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                            <div>
                                <label for="admin_reg_kk" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">KK Profiling?</label>
                                <select name="kk_profiling_status" id="admin_reg_kk" required
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                    <option value="Yes" {{ old('kk_profiling_status') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ old('kk_profiling_status', 'No') === 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="admin_reg_address" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Complete Address</label>
                                <input type="text" name="address" id="admin_reg_address" required value="{{ old('address') }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Documents and Guardian Info (Branching) -->
                    <div id="admin-reg-step-4" x-show="adminRegStep === 4" class="space-y-4">
                        <!-- Case A: Adult Uploads -->
                        <div x-show="adminAge >= 18" class="space-y-4">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 font-display">Step 4: Voter's verification document</h4>
                            <div>
                                <label for="admin_reg_voter" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Voter's Certificate / ID (Optional)</label>
                                <input type="file" name="voter_cert" id="admin_reg_voter"
                                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] hover:file:bg-blue-100 cursor-pointer">
                            </div>
                        </div>

                        <!-- Case B: Minor Parent/Guardian details -->
                        <div x-show="adminAge < 18" class="space-y-4">
                            <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2 font-display">Step 4: Parent / Guardian Information</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="admin_reg_gfname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian First Name</label>
                                    <input type="text" name="guardian_first_name" id="admin_reg_gfname" :required="adminAge < 18" value="{{ old('guardian_first_name') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div>
                                    <label for="admin_reg_gmname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Middle Name</label>
                                    <input type="text" name="guardian_middle_name" id="admin_reg_gmname" value="{{ old('guardian_middle_name') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div>
                                    <label for="admin_reg_glname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Last Name</label>
                                    <input type="text" name="guardian_last_name" id="admin_reg_glname" :required="adminAge < 18" value="{{ old('guardian_last_name') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div>
                                    <label for="admin_reg_gage" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Age</label>
                                    <input type="number" name="guardian_age" id="admin_reg_gage" :required="adminAge < 18" value="{{ old('guardian_age') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div>
                                    <label for="admin_reg_grelation" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Relation</label>
                                    <input type="text" name="guardian_relation" id="admin_reg_grelation" :required="adminAge < 18" value="{{ old('guardian_relation') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div>
                                    <label for="admin_reg_gcontact" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Contact</label>
                                    <input type="text" name="guardian_contact_number" id="admin_reg_gcontact" :required="adminAge < 18" value="{{ old('guardian_contact_number') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="admin_reg_gaddress" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Address</label>
                                    <input type="text" name="guardian_address" id="admin_reg_gaddress" :required="adminAge < 18" value="{{ old('guardian_address') }}"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">
                                </div>
                                <div class="sm:col-span-3">
                                    <label for="admin_reg_gid" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian valid ID (Optional)</label>
                                    <input type="file" name="guardian_gov_id" id="admin_reg_gid"
                                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] hover:file:bg-blue-100 cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: Waiver and Remarks -->
                    <div id="admin-reg-step-5" x-show="adminRegStep === 5" class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 font-display">Step 5: Consent Waiver and Remarks</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="admin_reg_photo" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Participant 2x2 Photo (Optional)</label>
                                <input type="file" name="profile_picture" id="admin_reg_photo"
                                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] hover:file:bg-blue-100 cursor-pointer">
                            </div>
                            <div>
                                <label for="admin_reg_health" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Health Declaration</label>
                                <textarea name="health_declaration" id="admin_reg_health" rows="2" required
                                          class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">{{ old('health_declaration', 'Fit to play') }}</textarea>
                            </div>
                            <div>
                                <label for="admin_reg_remarks" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Remarks / Configuration</label>
                                <textarea name="remarks" id="admin_reg_remarks" rows="2"
                                          class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-850">{{ old('remarks') }}</textarea>
                            </div>
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="consent_waiver" value="1" required checked
                                       class="mt-0.5 w-3.5 h-3.5 rounded text-blue-600 border-slate-355 focus:ring-[#1e40af] transition">
                                <span class="text-[11px] text-slate-650 leading-relaxed font-semibold">
                                    I certify that the participant agrees to the terms and condition guidelines.
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                    <div>
                        <button type="button" x-show="adminRegStep > 1" @click="prevAdminRegStep()"
                                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold uppercase tracking-wider text-[10px] rounded-xl transition">
                            Back
                        </button>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="showRegisterModal = false"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px] rounded-xl transition">
                            Cancel
                        </button>

                        <!-- Next Button -->
                        <button type="button" x-show="adminRegStep < 5" @click="nextAdminRegStep()"
                                class="px-5 py-2 bg-[#1e40af] hover:bg-blue-700 text-white font-bold uppercase tracking-wider text-[10px] rounded-xl transition shadow-sm">
                            Next
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" x-show="adminRegStep === 5"
                                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider text-[10px] rounded-xl transition shadow-sm">
                            Submit Registration
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Edit Details Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col border border-slate-100 overflow-hidden animate-fade-in-up" @click.away="showEditModal = false">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest block font-display">Console Update Desk</span>
                    <h3 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight">Edit Registration details</h3>
                </div>
                <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200/50 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Edit Form -->
            <form :action="'/admin/sports-league/' + editReq.id" method="POST" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                @method('PUT')

                <!-- Modal Scrollable Fields Area -->
                <div class="p-6 overflow-y-auto flex-1 space-y-5 text-xs">

                    <!-- Section: Personal Info -->
                    <div class="space-y-3">
                        <h4 class="text-[10px] font-black text-slate-450 uppercase tracking-wider border-b pb-1 font-display">Participant details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="edit_fname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">First Name</label>
                                <input type="text" name="first_name" id="edit_fname" required x-model="editReq.first_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_mname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Middle Name</label>
                                <input type="text" name="middle_name" id="edit_mname" x-model="editReq.middle_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_lname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Last Name</label>
                                <input type="text" name="last_name" id="edit_lname" required x-model="editReq.last_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_age" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Age</label>
                                <input type="number" name="age" id="edit_age" required x-model.number="editReq.age"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_gender" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</label>
                                <select name="gender" id="edit_gender" required x-model="editReq.gender"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Prefer not to say">Prefer not to say</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_contact" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Contact</label>
                                <input type="text" name="contact_number" id="edit_contact" required x-model="editReq.contact_number"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="edit_email" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                                <input type="email" name="email" id="edit_email" required x-model="editReq.email"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_kk" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">KK Profiling Status</label>
                                <select name="kk_profiling_status" id="edit_kk" required x-model="editReq.kk_profiling_status"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="edit_address" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Complete Address</label>
                                <input type="text" name="address" id="edit_address" required x-model="editReq.address"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Parent / Guardian Info (Conditional Minor) -->
                    <div x-show="editReq.age < 18" class="space-y-3 border-t border-slate-100 pt-4" x-cloak>
                        <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-wider border-b pb-1 font-display">Parent / Guardian details (Participant is minor)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="edit_gfname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian First Name</label>
                                <input type="text" name="guardian_first_name" id="edit_gfname" :required="editReq.age < 18" x-model="editReq.guardian_first_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_gmname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Middle Name</label>
                                <input type="text" name="guardian_middle_name" id="edit_gmname" x-model="editReq.guardian_middle_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_glname" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Last Name</label>
                                <input type="text" name="guardian_last_name" id="edit_glname" :required="editReq.age < 18" x-model="editReq.guardian_last_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_gage" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Age</label>
                                <input type="number" name="guardian_age" id="edit_gage" :required="editReq.age < 18" x-model="editReq.guardian_age"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_grelation" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Relation</label>
                                <input type="text" name="guardian_relation" id="edit_grelation" :required="editReq.age < 18" x-model="editReq.guardian_relation"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_gcontact" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Contact</label>
                                <input type="text" name="guardian_contact_number" id="edit_gcontact" :required="editReq.age < 18" x-model="editReq.guardian_contact_number"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div class="sm:col-span-3">
                                <label for="edit_gaddress" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Address</label>
                                <input type="text" name="guardian_address" id="edit_gaddress" :required="editReq.age < 18" x-model="editReq.guardian_address"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Game Details -->
                    <div class="space-y-3 border-t border-slate-100 pt-4">
                        <h4 class="text-[10px] font-black text-[#1e40af] uppercase tracking-wider border-b pb-1 font-display">Tournament selection</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_sport" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sport / Game</label>
                                <select name="sport" id="edit_sport" required x-model="editReq.sport"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                                    <option value="Basketball">Basketball</option>
                                    <option value="Volleyball">Volleyball</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_division" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Division</label>
                                <select name="division" id="edit_division" required x-model="editReq.division"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                                    <template x-if="editReq.sport === 'Basketball'">
                                        <optgroup label="Basketball Divisions">
                                            <option value="Midget">Midget [Edad 6 hanggang 12]</option>
                                            <option value="Juniors">Juniors [Edad 13 hanggang 17]</option>
                                            <option value="Seniors">Seniors [Edad 18 hanggang 39]</option>
                                        </optgroup>
                                    </template>
                                    <template x-if="editReq.sport === 'Volleyball'">
                                        <optgroup label="Volleyball Divisions">
                                            <option value="Mens">Men's Division</option>
                                            <option value="Womens">Women's Division</option>
                                        </optgroup>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label for="edit_position" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Position</label>
                                <input type="text" name="position" id="edit_position" required x-model="editReq.position"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div>
                                <label for="edit_team" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Team Name (Optional)</label>
                                <input type="text" name="team_name" id="edit_team" x-model="editReq.team_name"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="edit_remarks" class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Remarks / Configuration</label>
                                <textarea name="remarks" id="edit_remarks" rows="2" x-model="editReq.remarks"
                                          class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-855"></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 bg-slate-150 hover:bg-slate-200 text-slate-655 font-bold uppercase tracking-wider text-[10px] rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold uppercase tracking-wider text-[10px] rounded-xl transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
    input[required]:not([type="file"]):not([type="checkbox"]):not([type="radio"]),
    textarea[required] {
        text-transform: uppercase;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Force uppercase on required input fields in the console
    document.addEventListener('input', function(e) {
        const target = e.target;
        if (target && target.hasAttribute('required')) {
            if ((target.tagName === 'INPUT' && target.type !== 'file' && target.type !== 'checkbox' && target.type !== 'radio') || target.tagName === 'TEXTAREA') {
                const upperVal = target.value.toUpperCase();
                if (target.value !== upperVal) {
                    target.value = upperVal;
                    // Trigger Alpine.js model update
                    target.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        }
    });
});
</script>

<x-mobile-bottom-action x-show="!showRegisterModal && !showEditModal && !showViewModal && !showDeleteModal" @click="showRegisterModal = true; adminRegStep = 1; adminSport = 'Basketball'; adminDivision = 'Midget'; adminAge = 18">
    Register Citizen
</x-mobile-bottom-action>
@endsection
