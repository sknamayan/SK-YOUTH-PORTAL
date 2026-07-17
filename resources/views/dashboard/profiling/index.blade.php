@extends('layouts.app')

@section('content')
<div x-data="{
    showModal: false,
    showViewModal: false,
    editMode: false,
    formAction: '{{ route('dashboard.profiling.store') }}',
    profileId: null,
    step: 1,
    selectedProfile: {},

    // Inputs for edit pre-filling and dynamic validation
    surname: '',
    first_name: '',
    middle_name: '',
    ext: '',
    age: '',
    sex: '',
    gender: '',
    dob: '',
    civil_status: '',
    purok_id: '',
    street_address: '',
    youth_classification: '',
    contact_number: '',
    email: '',
    registered_sk_voter: '',
    registered_national_voter: '',
    attended_kk_assembly: '',
    partOfOrg: '0',
    youth_org_name: '',
    interested_in_joining: '',
    part_of_lgbtqia: '',
    isPwd: '0',
    registered_disability: '',
    highest_educational_attainment: '',
    consent_given: false,

    resetForm() {
        this.step = 1;
        this.editMode = false;
        this.formAction = '{{ route('dashboard.profiling.store') }}';
        this.profileId = null;
        this.surname = '';
        this.first_name = '';
        this.middle_name = '';
        this.ext = '';
        this.age = '';
        this.sex = '';
        this.gender = '';
        this.dob = '';
        this.civil_status = '';
        this.purok_id = '';
        this.street_address = '';
        this.youth_classification = '';
        this.contact_number = '';
        this.email = '';
        this.registered_sk_voter = '';
        this.registered_national_voter = '';
        this.attended_kk_assembly = '';
        this.partOfOrg = '0';
        this.youth_org_name = '';
        this.interested_in_joining = '';
        this.part_of_lgbtqia = '';
        this.isPwd = '0';
        this.registered_disability = '';
        this.highest_educational_attainment = '';
        this.consent_given = false;
        const form = document.getElementById('profileForm');
        if (form) form.reset();
    },

    openEdit(profile) {
        this.resetForm();
        this.editMode = true;
        this.profileId = profile.id;
        this.formAction = `/dashboard/profiling/${profile.id}`;

        this.surname = profile.surname;
        this.first_name = profile.first_name;
        this.middle_name = profile.middle_name || '';
        this.ext = profile.ext || '';
        this.age = profile.age;
        this.sex = profile.sex;
        this.gender = profile.gender || '';
        this.dob = profile.dob ? (profile.dob.length > 10 ? profile.dob.substring(0, 10) : profile.dob) : '';
        this.civil_status = profile.civil_status;
        this.purok_id = profile.purok_id;
        this.street_address = profile.street_address || '';
        this.youth_classification = profile.youth_classification;
        this.contact_number = profile.contact_number;
        this.email = profile.email;
        this.registered_sk_voter = profile.registered_sk_voter ? '1' : '0';
        this.registered_national_voter = profile.registered_national_voter ? '1' : '0';
        this.attended_kk_assembly = profile.attended_kk_assembly ? '1' : '0';
        this.partOfOrg = profile.part_of_youth_org ? '1' : '0';
        this.youth_org_name = profile.youth_org_name || '';
        this.interested_in_joining = profile.interested_in_joining ? '1' : '0';
        this.part_of_lgbtqia = profile.part_of_lgbtqia ? '1' : '0';
        this.isPwd = (profile.pwd === '' || profile.pwd === null) ? '' : (profile.pwd ? '1' : '0');
        this.registered_disability = profile.registered_disability || '';
        this.highest_educational_attainment = profile.highest_educational_attainment;
        this.consent_given = profile.consent_given ? true : false;

        this.showModal = true;
    },

    openView(profile, purokName) {
        this.selectedProfile = {
            ...profile,
            purokName: purokName
        };
        this.showViewModal = true;
    },

    // Client-side validation per step
    validateStep(s) {
        const fields = document.querySelectorAll(`#step-${s} [required]`);
        let valid = true;
        fields.forEach(field => {
            if (!field.value || !field.checkValidity()) {
                field.reportValidity();
                valid = false;
            }
        });
        return valid;
    },

    nextStep() {
        if (this.validateStep(this.step)) {
            if (this.step < 3) {
                this.step++;
            }
        }
    },

    prevStep() {
        if (this.step > 1) {
            this.step--;
        }
    }
}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar Navigation -->
    @include('layouts.dashboard-sidebar')

    <!-- Overlay back shadow on mobile -->

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Mobile Sidebar Trigger Header -->

        <!-- Page Main Wrapper -->
        <div class="p-6 md:p-8 pb-24 md:pb-8 space-y-8 flex-1 overflow-y-auto font-sans">

            <!-- Breadcrumbs / Top Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Profiling List</span>
                </div>
            </div>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Katipunan ng Kabataan</span>
                    <h1 class="text-2xl font-black text-slate-800 font-display uppercase tracking-tight">Youth Profiling Registry</h1>
                    <p class="text-xs text-slate-500">Manage, record, and filter local KK members profiles in Barangay Namayan.</p>
                </div>
            </div>

            <!-- Session Notifications -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-250 text-emerald-800 rounded-2xl text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-250 text-rose-800 rounded-2xl text-xs font-semibold space-y-1">
                    <p class="font-bold">Please correct the following validation errors:</p>
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-3 border-b border-slate-100 pb-px mb-6 overflow-x-auto whitespace-nowrap scrollbar-hide min-w-0">
                <a href="{{ route('dashboard.profiling.index', array_merge(request()->query(), ['status' => 'approved'])) }}"
                   class="pb-3 text-xs font-bold transition flex items-center gap-2 px-1 relative {{ $statusFilter === 'approved' ? 'text-[#1e40af]' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Active Registry</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $statusFilter === 'approved' ? 'bg-blue-100 text-[#1e40af]' : 'bg-slate-100 text-slate-650' }}">{{ $approvedCount }}</span>
                    @if($statusFilter === 'approved')
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#1e40af] rounded-full"></div>
                    @endif
                </a>

                <a href="{{ route('dashboard.profiling.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
                   class="pb-3 text-xs font-bold transition flex items-center gap-2 px-1 relative {{ $statusFilter === 'pending' ? 'text-[#1e40af]' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Pending Review</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $pendingCount > 0 ? 'bg-rose-100 text-rose-750 animate-pulse' : ($statusFilter === 'pending' ? 'bg-blue-100 text-[#1e40af]' : 'bg-slate-100 text-slate-650') }}">{{ $pendingCount }}</span>
                    @if($statusFilter === 'pending')
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#1e40af] rounded-full"></div>
                    @endif
                </a>

                <a href="{{ route('dashboard.profiling.index', array_merge(request()->query(), ['status' => 'declined'])) }}"
                   class="pb-3 text-xs font-bold transition flex items-center gap-2 px-1 relative {{ $statusFilter === 'declined' ? 'text-[#1e40af]' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>Declined</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $statusFilter === 'declined' ? 'bg-blue-100 text-[#1e40af]' : 'bg-slate-100 text-slate-650' }}">{{ $declinedCount }}</span>
                    @if($statusFilter === 'declined')
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#1e40af] rounded-full"></div>
                    @endif
                </a>

                <a href="{{ route('dashboard.profiling.index', array_merge(request()->query(), ['status' => 'all'])) }}"
                   class="pb-3 text-xs font-bold transition flex items-center gap-2 px-1 relative {{ $statusFilter === 'all' ? 'text-[#1e40af]' : 'text-slate-500 hover:text-slate-800' }}">
                    <span>All Records</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $statusFilter === 'all' ? 'bg-blue-100 text-[#1e40af]' : 'bg-slate-100 text-slate-650' }}">{{ $approvedCount + $pendingCount + $declinedCount }}</span>
                    @if($statusFilter === 'all')
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#1e40af] rounded-full"></div>
                    @endif
                </a>
            </div>

            <!-- Search Bar & Filters Card -->
            <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm"
                 x-data="{
                     lgbt: '{{ $lgbtqiaFilter }}',
                     pwd: '{{ $pwdFilter }}',
                     toggleLgbt() {
                         this.lgbt = this.lgbt === '1' ? '' : '1';
                         this.$nextTick(() => this.$refs.filterForm.submit());
                     },
                     togglePwd() {
                         this.pwd = this.pwd === '1' ? '' : '1';
                         this.$nextTick(() => this.$refs.filterForm.submit());
                     }
                 }">
                <form id="filterForm" x-ref="filterForm" method="GET" action="{{ route('dashboard.profiling.index') }}" class="space-y-4">
                    <!-- Hidden filters for LGBTQIA / PWD toggles & status tab -->
                    <input type="hidden" name="lgbtqia" :value="lgbt">
                    <input type="hidden" name="pwd" :value="pwd">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <input type="hidden" name="archive" value="{{ $showArchive ? '1' : '0' }}">

                    <!-- Row 1: Search, Purok, Year -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <!-- Search Box (Col span 6) -->
                        <div class="md:col-span-6 relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search by name, email or contact number..."
                                class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans placeholder-slate-400"
                            >
                        </div>

                        <!-- Purok Dropdown (Col span 3) -->
                        <div class="md:col-span-3 relative">
                            <select
                                name="purok"
                                onchange="this.form.submit()"
                                class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                            >
                                <option value="">All Puroks</option>
                                @foreach($puroks as $purok)
                                    <option value="{{ $purok->id }}" {{ $purokFilter == $purok->id ? 'selected' : '' }}>
                                        {{ $purok->purok_name }} {{ $purok->street_name ? '('.$purok->street_name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <!-- Year Picker Dropdown (Col span 3) -->
                        <div class="md:col-span-3 relative">
                            <select
                                name="year"
                                onchange="this.form.submit()"
                                class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                            >
                                <option value="">All Registration Years</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Limit, Sex, Classification, Badges, Reset, Actions -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-2 border-t border-slate-100/60">
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Page Size Limit select -->
                            <div class="relative w-32 shrink-0">
                                <select
                                    name="limit"
                                    onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                >
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

                            <!-- Sex Select -->
                            <div class="relative w-32 shrink-0">
                                <select
                                    name="sex"
                                    onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                >
                                    <option value="">All Sexes</option>
                                    <option value="Male" {{ $sexFilter == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $sexFilter == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Youth Classification select -->
                            <div class="relative w-44 shrink-0">
                                <select
                                    name="classification"
                                    onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                >
                                    <option value="">All Classifications</option>
                                    <option value="ISY" {{ $classFilter == 'ISY' ? 'selected' : '' }}>In-School Youth (ISY)</option>
                                    <option value="OSY" {{ $classFilter == 'OSY' ? 'selected' : '' }}>Out-of-School Youth (OSY)</option>
                                    <option value="WY" {{ $classFilter == 'WY' ? 'selected' : '' }}>Working Youth (WY)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- LGBTQIA Toggle Badge -->
                            @if(auth()->user()->isSuperAdmin())
                            <button type="button" @click="toggleLgbt()"
                                    :class="lgbt === '1' ? 'bg-blue-50 border-blue-200 text-[#1e40af] font-black' : 'bg-slate-50 border-slate-100 text-slate-600 hover:bg-slate-100'"
                                    class="px-3.5 py-2 border rounded-2xl text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none"
                            >
                                <span>Demographics (LGBTQIA+)</span>
                            </button>

                            <!-- PWD Toggle Badge -->
                            <button type="button" @click="togglePwd()"
                                    :class="pwd === '1' ? 'bg-blue-50 border-blue-200 text-[#1e40af] font-black' : 'bg-slate-50 border-slate-100 text-slate-600 hover:bg-slate-100'"
                                    class="px-3.5 py-2 border rounded-2xl text-[11px] font-bold tracking-wide transition active:scale-95 cursor-pointer flex items-center space-x-1.5 select-none"
                            >
                                <span>Health Info (PWD)</span>
                            </button>
                            @endif

                            <!-- Reset Filter Link -->
                            @if($search || $purokFilter || $classFilter || $yearFilter || $sexFilter || $skVoterFilter || $nationalVoterFilter || $lgbtqiaFilter || $pwdFilter || $limit != 15)
                                <a href="{{ route('dashboard.profiling.index') }}"
                                   class="inline-flex items-center text-[11px] font-bold text-slate-450 hover:text-slate-600 transition space-x-1 select-none cursor-pointer pl-2 py-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                        </div>

                        <!-- Right Primary Trigger -->
                        <div class="flex flex-wrap items-center justify-end gap-2 text-right">
                            <button type="submit" class="hidden"></button> <!-- form submission placeholder -->
                            <a href="{{ route('dashboard.profiling.index', array_merge(request()->query(), ['archive' => $showArchive ? '0' : '1'])) }}"
                               class="btn-primary text-[11px] font-black uppercase py-2 px-5 flex items-center space-x-1.5 cursor-pointer transition shadow-sm border rounded-2xl {{ $showArchive ? 'bg-[#1e40af] hover:bg-blue-700 text-white border-transparent' : 'bg-slate-100 hover:bg-slate-200/80 text-slate-650 border-slate-250' }}">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span>Archive ({{ $archivedCount }})</span>
                            </a>
                            <a href="{{ route('dashboard.export', ['profiling']) }}"
                               class="btn-primary text-[11px] font-black uppercase py-2 px-5 flex items-center space-x-1.5 cursor-pointer bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition shadow-sm border border-transparent rounded-2xl">
                                <span>Export CSV</span>
                            </a>
                            <button type="button" @click="resetForm(); showModal = true" class="btn-primary text-[11px] font-black uppercase py-2 px-5 flex items-center space-x-2 cursor-pointer shadow-sm shadow-blue-500/10 active:scale-95 transition">
                                <span>Add New Member</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Profiles Data Table -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                @if($profiles->isEmpty())
                    <div class="text-center py-12 text-slate-450 text-xs">No youth profiling records match the filter selections.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[10px] select-all font-sans">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-450 font-bold uppercase tracking-wider text-[10px] whitespace-nowrap">
                                    <th class="py-4 px-6">Count</th>
                                    <th class="py-4 px-6">Surname</th>
                                    <th class="py-4 px-6">First Name</th>
                                    <th class="py-4 px-6">Middle Name</th>
                                    <th class="py-4 px-6">Ext.</th>
                                    <th class="py-4 px-6 text-center">Age</th>
                                    <th class="py-4 px-6">Sex</th>
                                    <th class="py-4 px-6">Gender</th>
                                    <th class="py-4 px-6">DOB</th>
                                    <th class="py-4 px-6">Civil Status</th>
                                    <th class="py-4 px-6">Classification</th>
                                    <th class="py-4 px-6">Contact Number</th>
                                    <th class="py-4 px-6">Email Address</th>
                                    <th class="py-4 px-6 text-center">SK Voter?</th>
                                    <th class="py-4 px-6 text-center">National Voter?</th>
                                    <th class="py-4 px-6 text-center">Attended Assembly?</th>
                                    <th class="py-4 px-6">Youth Org Info</th>
                                    <th class="py-4 px-6 text-center">Interested to Join?</th>
                                    <th class="py-4 px-6 text-center">LGBTQIA+?</th>
                                    <th class="py-4 px-6">PWD Disability</th>
                                    <th class="py-4 px-6">Education</th>
                                    <th class="py-4 px-6">Purok</th>
                                    <th class="py-4 px-6">Street Address</th>
                                    <th class="py-4 px-6">Processed By</th>
                                    <th class="py-4 px-6">Status</th>
                                    <th class="py-4 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600 text-[11px] whitespace-nowrap">
                                @foreach($profiles as $profile)
                                    @php
                                        $count = $loop->iteration + ($profiles->currentPage() - 1) * $profiles->perPage();
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition duration-150">
                                        <td class="py-4 px-6 font-bold text-slate-800">{{ $count }}</td>
                                        <td class="py-4 px-6 font-semibold text-slate-700">{{ $profile->surname }}</td>
                                        <td class="py-4 px-6 font-semibold text-slate-700">{{ $profile->first_name }}</td>
                                        <td class="py-4 px-6 text-slate-500">{{ $profile->middle_name ?? '-' }}</td>
                                        <td class="py-4 px-6 text-slate-500">{{ $profile->ext ?? '-' }}</td>
                                        <td class="py-4 px-6 text-center font-bold text-slate-700">{{ $profile->age }}</td>
                                        <td class="py-4 px-6">{{ $profile->sex }}</td>
                                        <td class="py-4 px-6 text-slate-500">{{ $profile->gender ?? '-' }}</td>
                                        <td class="py-4 px-6 font-mono">
                                            @if(auth()->user()->isSuperAdmin())
                                                {{ \Carbon\Carbon::parse($profile->dob)->format('M d, Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-6">{{ $profile->civil_status }}</td>
                                        <td class="py-4 px-6">
                                            @if($profile->youth_classification === 'ISY')
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded-full text-[9px] font-black uppercase tracking-wide">In-School (ISY)</span>
                                            @elseif($profile->youth_classification === 'OSY')
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-800 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">Out-of-School (OSY)</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-full text-[9px] font-black uppercase tracking-wide">Working (WY)</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-medium text-slate-700">
                                            @if(auth()->user()->isSuperAdmin())
                                                {{ $profile->contact_number }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 font-mono text-slate-500">
                                            @if(auth()->user()->isSuperAdmin())
                                                {{ $profile->email }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Registered SK Voter -->
                                        <td class="py-4 px-6 text-center">
                                            @if($profile->registered_sk_voter)
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">No</span>
                                            @endif
                                        </td>

                                        <!-- Registered National Voter -->
                                        <td class="py-4 px-6 text-center">
                                            @if($profile->registered_national_voter)
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">No</span>
                                            @endif
                                        </td>

                                        <!-- Attended KK Assembly -->
                                        <td class="py-4 px-6 text-center">
                                            @if($profile->attended_kk_assembly)
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">No</span>
                                            @endif
                                        </td>

                                        <!-- Youth Org Member -->
                                        <td class="py-4 px-6">
                                            @if($profile->part_of_youth_org)
                                                <span class="font-bold text-slate-800">Yes</span>
                                                <span class="text-slate-455 block text-[9px] mt-0.5">{{ $profile->youth_org_name }}</span>
                                            @else
                                                <span class="text-slate-400 italic">No</span>
                                            @endif
                                        </td>

                                        <!-- Interested to Join -->
                                        <td class="py-4 px-6 text-center">
                                            @if(!$profile->part_of_youth_org)
                                                @if($profile->interested_in_joining)
                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-slate-50 text-slate-500 border border-slate-200 rounded-full text-[9px] font-bold uppercase tracking-wide">No</span>
                                                @endif
                                            @else
                                                <span class="text-slate-350">-</span>
                                            @endif
                                        </td>

                                        <!-- LGBTQIA+ -->
                                        <td class="py-4 px-6 text-center">
                                            @if($profile->part_of_lgbtqia)
                                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-slate-50 text-slate-500 border border-slate-200 rounded-full text-[9px] font-bold uppercase tracking-wide">No</span>
                                            @endif
                                        </td>

                                        <!-- PWD Disability -->
                                        <td class="py-4 px-6">
                                            @if(auth()->user()->isSuperAdmin())
                                                @if($profile->pwd)
                                                    <span class="font-bold text-rose-700">Yes</span>
                                                    <span class="text-slate-455 block text-[9px] mt-0.5">{{ $profile->registered_disability }}</span>
                                                @else
                                                    <span class="text-slate-400 italic">No</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Highest Educational Attainment -->
                                        <td class="py-4 px-6 font-medium">{{ $profile->highest_educational_attainment }}</td>

                                        <!-- Purok -->
                                        <td class="py-4 px-6 font-bold text-slate-800">{{ $profile->purok->purok_name }}</td>

                                        <!-- Street Address -->
                                        <td class="py-4 px-6 text-slate-500">{{ $profile->street_address ?? '-' }}</td>

                                        <!-- Processed By -->
                                        <td class="py-4 px-6 font-semibold text-slate-700">
                                            @if($profile->processedBy)
                                                @if($profile->processedBy->role === 'user')
                                                    <span class="px-2.5 py-0.5 bg-blue-50/50 text-[#1e40af] border border-blue-100 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Self Profiling</span>
                                                @else
                                                    {{ $profile->processedBy->name }}
                                                @endif
                                            @else
                                                <span class="px-2.5 py-0.5 bg-blue-50/50 text-[#1e40af] border border-blue-100 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Self Profiling</span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="py-4 px-6">
                                            @if($profile->deleted_at !== null)
                                                <span class="px-2.5 py-0.5 bg-rose-100 text-rose-700 border border-rose-200 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Archived (Removed)</span>
                                            @elseif($profile->age > 30)
                                                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 border border-amber-250 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Archived (Age > 30)</span>
                                            @else
                                                @if($profile->status === 'approved')
                                                    <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Approved</span>
                                                @elseif($profile->status === 'pending')
                                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Pending Review</span>
                                                @else
                                                    <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Declined</span>
                                                @endif
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                         <td class="py-4 px-6 text-center whitespace-nowrap">
                                             <div class="flex items-center justify-center space-x-2">
                                                 @if($profile->deleted_at !== null)
                                                     <form action="{{ route('dashboard.profiling.restore', $profile->id) }}" method="POST" class="inline">
                                                         @csrf
                                                         <button type="submit" class="p-1.5 bg-indigo-50 text-[#1e40af] border border-blue-100 rounded-lg hover:bg-[#1e40af] hover:text-white transition active:scale-95" title="Restore Profile">
                                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                                         </button>
                                                     </form>
                                                 @else
                                                     <!-- Approve/Decline Buttons for Pending citizens -->
                                                     @if($profile->status === 'pending')
                                                         <form action="{{ route('dashboard.profiling.approve', $profile) }}" method="POST" class="inline">
                                                             @csrf
                                                             @method('PATCH')
                                                             <button type="submit" class="p-1.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg hover:bg-emerald-600 hover:text-white transition active:scale-95" title="Approve Profile">
                                                                 <svg class="w-4 h-4 text-emerald-700 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                             </button>
                                                         </form>

                                                         <form action="{{ route('dashboard.profiling.decline', $profile) }}" method="POST" class="inline">
                                                             @csrf
                                                             @method('PATCH')
                                                             <button type="submit" class="p-1.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg hover:bg-rose-600 hover:text-white transition active:scale-95" title="Decline Profile">
                                                                 <svg class="w-4 h-4 text-rose-700 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                             </button>
                                                         </form>
                                                     @endif

                                                     <!-- View Button -->
                                                     <button type="button" @click="openView({{ json_encode($profile->toPresentableArray(auth()->user())) }}, '{{ $profile->purok->purok_name }}')" class="p-1.5 bg-blue-50 text-[#1e40af] border border-blue-100 rounded-lg hover:bg-[#1e40af] hover:text-white transition active:scale-95" title="View Profile">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                     </button>

                                                     <!-- Edit Button -->
                                                     <button type="button" @click="openEdit({{ json_encode($profile->toPresentableArray(auth()->user())) }})" class="p-1.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg hover:bg-amber-600 hover:text-white transition active:scale-95" title="Edit Profile">
                                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                     </button>

                                                     <!-- Delete Button -->
                                                     <x-alert-dialog>
                                                         <x-slot name="trigger">
                                                             <button class="p-1.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-lg hover:bg-rose-600 hover:text-white transition active:scale-95" title="Archive Profile">
                                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                             </button>
                                                         </x-slot>
                                                         <x-slot name="icon">
                                                             <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                         </x-slot>
                                                         <x-slot name="title">Archive Profile</x-slot>
                                                         <x-slot name="description">
                                                             Are you sure you want to archive this profile? This action will hide the profile from active records.
                                                         </x-slot>
                                                         <x-slot name="footer">
                                                             <button @click="open = false" type="button" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition">
                                                                 Cancel
                                                             </button>
                                                             <form action="{{ route('dashboard.profiling.destroy', $profile) }}" method="POST" class="inline">
                                                                 @csrf
                                                                 @method('DELETE')
                                                                 <button type="submit" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition">
                                                                     Confirm Archive
                                                                 </button>
                                                             </form>
                                                         </x-slot>
                                                     </x-alert-dialog>
                                                 @endif
                                             </div>
                                         </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $profiles->links() }}
                    </div>
                @endif
            </div>

            <!-- Recent Activity & Processing History Card -->
            <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider font-display">Recent Activity & Processing History</h3>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase">Log of latest profile registry changes within the system</p>
                    </div>
                    <span class="px-2.5 py-0.5 bg-blue-50 text-[#1e40af] rounded-full text-[9px] font-black uppercase font-mono">
                        Audit Trail
                    </span>
                </div>

                @if($historyLogs->isEmpty())
                    <p class="text-xs text-slate-400 italic py-4">No recent profiling actions logged.</p>
                @else
                    <div class="relative pl-6 border-l-2 border-slate-100 space-y-6">
                        @foreach($historyLogs as $log)
                            @php
                                $badgeColor = match($log->action) {
                                    'kk_profile_created' => 'bg-emerald-50 text-emerald-700 border-emerald-150',
                                    'kk_profile_updated' => 'bg-amber-50 text-amber-700 border-amber-150',
                                    'kk_profile_deleted' => 'bg-rose-50 text-rose-700 border-rose-150',
                                    default => 'bg-blue-50 text-blue-700 border-blue-150'
                                };

                                $actionName = match($log->action) {
                                    'kk_profile_created' => 'Created Profile',
                                    'kk_profile_updated' => 'Updated Profile',
                                    'kk_profile_deleted' => 'Deleted Profile',
                                    default => str_replace('_', ' ', $log->action)
                                };

                                $targetName = $log->payload['name'] ?? 'Unknown Member';
                                $targetEmail = $log->payload['email'] ?? '';

                                // Determine processor
                                $isSelf = $log->payload['self_profiled'] ?? false;
                                if ($isSelf || ($log->user && $log->user->role === 'user')) {
                                    $processor = 'Self Profiling';
                                    $processorSub = 'Citizen Registry';
                                } else {
                                    $processor = $log->user ? $log->user->name : 'System';
                                    $processorSub = $log->user ? ucfirst($log->user->role) : 'Automated';
                                }
                            @endphp
                            <!-- Timeline Item -->
                            <div class="relative">
                                <!-- Dot indicator -->
                                <div class="absolute -left-[31px] mt-1.5 w-3.5 h-3.5 rounded-full border-2 border-white bg-blue-500 shadow-sm"></div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-0.5 border rounded-full text-[9px] font-black uppercase tracking-wide font-display {{ $badgeColor }}">
                                                {{ $actionName }}
                                            </span>
                                            <span class="font-bold text-slate-800" x-text="'{{ addslashes($targetName) }}'"></span>
                                            @if($targetEmail)
                                                <span class="text-[10px] text-slate-400 font-mono">({{ $targetEmail }})</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-450">
                                            Action performed by: <span class="font-bold text-slate-700">{{ $processor }}</span>
                                            <span class="text-[9px] text-slate-400 font-semibold uppercase font-mono">({{ $processorSub }})</span>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[10px] text-slate-400 font-mono block">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold block">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- View 1: Add New Profile Multi-step Modal Form -->
    <div x-show="showModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>

        <!-- Modal Card Container -->
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-3xl max-h-[90vh] flex flex-col border border-slate-100 overflow-hidden"
             @click.away="showModal = false">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <h2 class="text-sm font-black text-[#1e40af] uppercase tracking-wider font-display">SK Namayan Profiling</h2>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Katipunan ng Kabataan Registry Form</p>
                </div>
                <button @click="showModal = false" class="p-2 rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Content (Scrollable Form Pane) -->
            <form id="profileForm" method="POST" :action="formAction" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Step Indicator / Progress Bar -->
                <div class="border-b border-slate-100 pb-5">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none max-w-xl mx-auto">
                        <!-- Step 1 Indicator -->
                        <div class="flex flex-col items-center relative transition duration-300" :class="step >= 1 ? 'text-[#1e40af]' : 'text-slate-400'">
                            <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                                 :class="step >= 1 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">1</div>
                            <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Personal Details</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 2 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                        <!-- Step 2 Indicator -->
                        <div class="flex flex-col items-center relative transition duration-300" :class="step >= 2 ? 'text-[#1e40af]' : 'text-slate-400'">
                            <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                                 :class="step >= 2 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">2</div>
                            <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Affiliations</span>
                        </div>
                        <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 3 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                        <!-- Step 3 Indicator -->
                        <div class="flex flex-col items-center relative transition duration-300" :class="step >= 3 ? 'text-[#1e40af]' : 'text-slate-400'">
                            <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                                 :class="step >= 3 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">3</div>
                            <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Inclusivity</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 1: Personal Details -->
                <div x-show="step === 1" id="step-1" class="space-y-4">
                    <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">1. Personal Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Surname <span class="text-rose-500">*</span></label>
                            <input type="text" name="surname" x-model="surname" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Dela Cruz">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">First Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="first_name" x-model="first_name" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Juan">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Middle Name</label>
                            <input type="text" name="middle_name" x-model="middle_name" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Santiago">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Suffix (Ext.)</label>
                            <select name="ext" x-model="ext" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                                <option value="">None</option>
                                <option value="Jr.">Jr.</option>
                                <option value="Sr.">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Age <span class="text-rose-500">*</span></label>
                            <input type="number" name="age" x-model="age" min="15" max="30" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="15 to 30">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sex <span class="text-rose-500">*</span></label>
                            <select name="sex" x-model="sex" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Gender Identity</label>
                            <input type="text" name="gender" x-model="gender" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. LGBTQIA+">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                            <input :type="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }} ? 'text' : 'date'" name="dob" x-model="dob" required
                                   :readonly="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }}"
                                   class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl"
                                   :class="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }} ? 'bg-slate-100/80 cursor-not-allowed opacity-75' : ''">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Civil Status <span class="text-rose-500">*</span></label>
                            <select name="civil_status" x-model="civil_status" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                                <option value="">Select Civil Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Youth Classification <span class="text-rose-500">*</span></label>
                            <select name="youth_classification" x-model="youth_classification" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                                <option value="">Select Classification</option>
                                <option value="ISY">In-School Youth (ISY)</option>
                                <option value="OSY">Out-of-School Youth (OSY)</option>
                                <option value="WY">Working Youth (WY)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Purok (Barangay Namayan) <span class="text-rose-500">*</span></label>
                            <select name="purok_id" x-model="purok_id" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl">
                                <option value="">Select Purok</option>
                                @foreach($puroks as $purok)
                                    <option value="{{ $purok->id }}">
                                        {{ $purok->purok_name }} {{ $purok->street_name ? '('.$purok->street_name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Street Address</label>
                            <input type="text" name="street_address" x-model="street_address" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. 594 J.P Rizal Street">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Contact Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="contact_number" x-model="contact_number" required
                                   :readonly="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }}"
                                   class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl"
                                   :class="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }} ? 'bg-slate-100/80 cursor-not-allowed opacity-75' : ''"
                                   placeholder="e.g. 09171234567">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" x-model="email" required
                                   :readonly="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }}"
                                   class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl"
                                   :class="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }} ? 'bg-slate-100/80 cursor-not-allowed opacity-75' : ''"
                                   placeholder="e.g. citizen@namayan.local">
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Affiliations -->
                <div x-show="step === 2" id="step-2" class="space-y-6">
                    <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">2. Affiliations & Voter Info</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700">
                        <!-- Registered SK Voter -->
                        <div class="space-y-2">
                            <span class="block font-bold text-slate-500 uppercase text-[10px]">Registered SK Voter? <span class="text-rose-500">*</span></span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="registered_sk_voter" value="1" x-model="registered_sk_voter" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="registered_sk_voter" value="0" x-model="registered_sk_voter" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Registered National Voter -->
                        <div class="space-y-2">
                            <span class="block font-bold text-slate-500 uppercase text-[10px]">Registered National Voter? <span class="text-rose-500">*</span></span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="registered_national_voter" value="1" x-model="registered_national_voter" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="registered_national_voter" value="0" x-model="registered_national_voter" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Attended KK Assembly -->
                        <div class="space-y-2">
                            <span class="block font-bold text-slate-500 uppercase text-[10px]">Attended KK Assembly? <span class="text-rose-500">*</span></span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="attended_kk_assembly" value="1" x-model="attended_kk_assembly" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="attended_kk_assembly" value="0" x-model="attended_kk_assembly" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Part of Youth Org -->
                        <div class="space-y-2">
                            <span class="block font-bold text-slate-500 uppercase text-[10px]">Part of Youth Organization? <span class="text-rose-500">*</span></span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="part_of_youth_org" value="1" x-model="partOfOrg" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="part_of_youth_org" value="0" x-model="partOfOrg" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Youth Org Name (Conditional if Yes) -->
                    <div x-show="partOfOrg === '1'" x-transition class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Name of Youth Organization <span class="text-rose-500">*</span></label>
                        <input type="text" name="youth_org_name" x-model="youth_org_name" :required="partOfOrg === '1'" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Sangguniang Kabataan Movement">
                    </div>

                    <!-- Interested in joining (Conditional if No) -->
                    <div x-show="partOfOrg === '0'" x-transition class="space-y-2 text-xs text-slate-700">
                        <span class="block font-bold text-slate-500 uppercase text-[10px]">Interested in joining a Youth Organization? <span class="text-rose-500">*</span></span>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="interested_in_joining" value="1" x-model="interested_in_joining" :required="partOfOrg === '0'" class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="interested_in_joining" value="0" x-model="interested_in_joining" :required="partOfOrg === '0'" class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Inclusivity & Education -->
                <div x-show="step === 3" id="step-3" class="space-y-6">
                    <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">3. Inclusivity & Education</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-slate-700">
                        <!-- Part of LGBTQIA -->
                        <div class="space-y-2">
                            <span class="block font-bold text-slate-500 uppercase text-[10px]">Part of the LGBTQIA+ Community? <span class="text-rose-500">*</span></span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="part_of_lgbtqia" value="1" x-model="part_of_lgbtqia" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="part_of_lgbtqia" value="0" x-model="part_of_lgbtqia" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Person With Disability (PWD) -->
                        <div class="space-y-2">
                            <span class="block font-bold text-slate-500 uppercase text-[10px]">Person with Disability (PWD)? <span class="text-rose-500">*</span></span>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center" x-show="isPwd !== ''">
                                    <input type="radio" name="pwd" value="1" x-model="isPwd" required
                                           :disabled="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }}"
                                           class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center" x-show="isPwd !== ''">
                                    <input type="radio" name="pwd" value="0" x-model="isPwd" required
                                           :disabled="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }}"
                                           class="text-[#1e40af] focus:ring-[#1e40af]">
                                    <span class="ml-2">No</span>
                                </label>
                                <span x-show="isPwd === ''" class="text-slate-450 italic font-semibold">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Disability Name (Conditional if Yes) -->
                    <div x-show="isPwd === '1' || isPwd === ''" x-transition class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Registered Disability <span class="text-rose-500">*</span></label>
                        <input type="text" name="registered_disability" x-model="registered_disability" :required="isPwd === '1'"
                               :readonly="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }}"
                               class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl"
                               :class="editMode && !{{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }} ? 'bg-slate-100/80 cursor-not-allowed opacity-75' : ''"
                               placeholder="e.g. Visual Impairment">
                    </div>

                    <!-- Highest Educational Attainment -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Highest Educational Attainment <span class="text-rose-500">*</span></label>
                        <input type="text" name="highest_educational_attainment" x-model="highest_educational_attainment" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. College Graduate, 2nd Year College">
                    </div>

                    <!-- Data Privacy Consent Confirmation (RA 10173 compliance) -->
                    <div class="space-y-2 pt-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="admin_consent_checkbox" name="consent_given" type="checkbox" value="1" x-model="consent_given" required class="focus:ring-[#1e40af] h-4 w-4 text-[#1e40af] border-slate-350 rounded cursor-pointer">
                            </div>
                            <div class="ml-3 text-xs">
                                <label for="admin_consent_checkbox" class="font-bold text-slate-700 cursor-pointer select-none">I confirm that I have read, understood, and voluntarily agreed to the Data Privacy Notice in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173).</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Navigation -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between shrink-0">
                    <button type="button"
                            x-show="step > 1"
                            @click="prevStep()"
                            class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold rounded-xl transition text-xs uppercase tracking-wider select-none cursor-pointer">
                        &larr; Back
                    </button>
                    <div x-show="step === 1" class="w-10"></div> <!-- Placeholder -->

                    <button type="button"
                            x-show="step < 3"
                            @click="nextStep()"
                            class="btn-primary text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer">
                        Next &rarr;
                    </button>

                    <button type="submit"
                            x-show="step === 3"
                            class="btn-success text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer">
                        Submit Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View 2: Detailed Profile Read-Only Modal Card -->
    <div x-show="showViewModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>

        <!-- Modal Card Container -->
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col border border-slate-100 overflow-hidden"
             @click.away="showViewModal = false">

            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <h2 class="text-sm font-black text-[#1e40af] uppercase tracking-wider font-display">Katipunan ng Kabataan Registry</h2>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">Member Profile Detail Card</p>
                </div>
                <button @click="showViewModal = false" class="p-2 rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Content (Scrollable Details) -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 text-xs text-slate-700">
                <!-- Header Card (Name & Key Stats) -->
                <div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-base font-black text-slate-800" x-text="(selectedProfile.first_name || '') + ' ' + (selectedProfile.middle_name ? selectedProfile.middle_name + ' ' : '') + (selectedProfile.surname || '') + (selectedProfile.ext ? ' ' + selectedProfile.ext : '')"></span>
                            <span class="px-2.5 py-0.5 bg-blue-50 text-[#1e40af] border border-blue-150 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="selectedProfile.youth_classification"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-mono" x-text="selectedProfile.email || '-'"></p>
                    </div>
                    <div class="flex items-center space-x-3 shrink-0">
                        <div class="text-center bg-white border border-slate-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="block text-[10px] text-slate-400 font-bold uppercase">Age</span>
                            <span class="text-sm font-black text-slate-800" x-text="selectedProfile.age"></span>
                        </div>
                        <div class="text-center bg-white border border-slate-100 rounded-xl px-3 py-1.5 shadow-sm">
                            <span class="block text-[10px] text-slate-400 font-bold uppercase">Sex</span>
                            <span class="text-sm font-black text-slate-800" x-text="selectedProfile.sex"></span>
                        </div>
                    </div>
                </div>

                <!-- 1. Personal & Contact Details -->
                <div class="space-y-3">
                    <h3 class="text-[11px] font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-1.5">1. Personal & Contact Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Gender Identity</span>
                            <span class="font-semibold text-slate-700 text-[11px]" x-text="selectedProfile.gender || 'Not specified'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Date of Birth</span>
                            <span class="font-semibold text-slate-700 text-[11px]" x-text="selectedProfile.dob ? new Date(selectedProfile.dob).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '-'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Civil Status</span>
                            <span class="font-semibold text-slate-700 text-[11px]" x-text="selectedProfile.civil_status"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Contact Number</span>
                            <span class="font-semibold text-slate-700 text-[11px]" x-text="selectedProfile.contact_number || '-'"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Purok</span>
                            <span class="font-semibold text-slate-850 text-[11px]" x-text="selectedProfile.purokName"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Street Address</span>
                            <span class="font-semibold text-slate-700 text-[11px]" x-text="selectedProfile.street_address || '-'"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Affiliations & Voter Info -->
                <div class="space-y-3">
                    <h3 class="text-[11px] font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-1.5">2. Affiliations & Voter Status</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-1">Registered SK Voter?</span>
                            <template x-if="selectedProfile.registered_sk_voter == 1">
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                            </template>
                            <template x-if="selectedProfile.registered_sk_voter != 1">
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">No</span>
                            </template>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-1">Registered National Voter?</span>
                            <template x-if="selectedProfile.registered_national_voter == 1">
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                            </template>
                            <template x-if="selectedProfile.registered_national_voter != 1">
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">No</span>
                            </template>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-1">Attended KK Assembly?</span>
                            <template x-if="selectedProfile.attended_kk_assembly == 1">
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                            </template>
                            <template x-if="selectedProfile.attended_kk_assembly != 1">
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">No</span>
                            </template>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-1">Youth Organization Membership</span>
                            <template x-if="selectedProfile.part_of_youth_org == 1">
                                <div>
                                    <span class="font-bold text-slate-700 text-[11px]">Member</span>
                                    <span class="text-slate-450 block text-[9px] mt-0.5" x-text="selectedProfile.youth_org_name"></span>
                                </div>
                            </template>
                            <template x-if="selectedProfile.part_of_youth_org != 1">
                                <div>
                                    <span class="text-slate-450 italic text-[11px]">Non-Member</span>
                                    <span class="text-slate-450 block text-[9px] mt-0.5" x-text="selectedProfile.interested_in_joining == 1 ? 'Interested to join' : 'Not interested to join'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 3. Inclusivity & Educational Attainment -->
                <div class="space-y-3">
                    <h3 class="text-[11px] font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-1.5">3. Inclusivity & Education</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-1">Part of the LGBTQIA+ Community?</span>
                            <template x-if="selectedProfile.part_of_lgbtqia == 1">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                            </template>
                            <template x-if="selectedProfile.part_of_lgbtqia != 1">
                                <span class="px-2 py-0.5 bg-slate-50 text-slate-500 border border-slate-200 rounded-full text-[9px] font-bold uppercase tracking-wide">No</span>
                            </template>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-1">Person with Disability (PWD)?</span>
                            <template x-if="selectedProfile.pwd === 1 || selectedProfile.pwd === '1' || selectedProfile.pwd === true">
                                <div>
                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-250 rounded-full text-[9px] font-black uppercase tracking-wide">Yes</span>
                                    <span class="text-slate-455 block text-[9px] mt-1" x-text="'Registered Disability: ' + (selectedProfile.registered_disability || 'None')"></span>
                                </div>
                            </template>
                            <template x-if="selectedProfile.pwd === 0 || selectedProfile.pwd === '0' || selectedProfile.pwd === false">
                                <span class="px-2 py-0.5 bg-slate-50 text-slate-500 border border-slate-200 rounded-full text-[9px] font-bold uppercase tracking-wide">No</span>
                            </template>
                            <template x-if="selectedProfile.pwd === '' || selectedProfile.pwd === null">
                                <span class="text-slate-400 italic">-</span>
                            </template>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase text-[9px] text-slate-400 mb-0.5">Highest Educational Attainment</span>
                            <span class="font-semibold text-slate-700 text-[11px]" x-text="selectedProfile.highest_educational_attainment"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-slate-450 font-bold uppercase text-[9px] text-slate-400 mb-1">Data Privacy Agreement</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-250 rounded-full text-[9px] font-black uppercase tracking-wide">Agreed & Consented</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0">
                <button type="button" @click="showViewModal = false" class="px-5 py-2 bg-[#1e40af] text-white hover:bg-blue-700 font-bold rounded-xl transition text-xs uppercase tracking-wider select-none cursor-pointer">
                    Close Details
                </button>
            </div>
        </div>
    </div>

</div>

<x-mobile-bottom-action x-show="!showModal && !showViewModal" @click="resetForm(); showModal = true">
    Add New Member
</x-mobile-bottom-action>
@endsection
