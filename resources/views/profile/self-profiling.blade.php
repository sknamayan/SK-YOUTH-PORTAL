@extends('layouts.app')

@section('content')
<div class="flex-1 bg-slate-50 dark:bg-slate-950 font-sans min-h-screen pt-8 pb-36 md:py-10 md:pb-40" x-data="{
    showConfirm: false,
    loading: false,
    step: 1,
    partOfOrg: '{{ old('part_of_youth_org', '0') }}',
    isPwd: '{{ old('pwd', '0') }}',

    // Form fields to track for percentage calculation
    consent_given: {{ old('consent_given', '0') === '1' ? 'true' : 'false' }},
    surname: '{{ old('surname', $user->last_name ?? '') }}',
    first_name: '{{ old('first_name', $user->first_name ?? '') }}',
    middle_name: '{{ old('middle_name', '') }}',
    ext: '{{ old('ext', '') }}',
    age: '{{ old('age', '') }}',
    sex: '{{ old('sex', '') }}',
    gender: '{{ old('gender', '') }}',
    dob: '{{ old('dob', '') }}',
    civil_status: '{{ old('civil_status', '') }}',
    purok_id: '{{ old('purok_id', '') }}',
    street_address: '{{ old('street_address', '') }}',
    contact_number: '{{ old('contact_number', '') }}',
    registered_sk_voter: '{{ old('registered_sk_voter', '') }}',
    registered_national_voter: '{{ old('registered_national_voter', '') }}',
    attended_kk_assembly: '{{ old('attended_kk_assembly', '') }}',
    youth_org_name: '{{ old('youth_org_name', '') }}',
    interested_in_joining: '{{ old('interested_in_joining', '') }}',
    part_of_lgbtqia: '{{ old('part_of_lgbtqia', '') }}',
    registered_disability: '{{ old('registered_disability', '') }}',
    highest_educational_attainment: '{{ old('highest_educational_attainment', '') }}',

    get requiredFields() {
        let fields = [
            this.consent_given,
            this.surname,
            this.first_name,
            this.age,
            this.sex,
            this.dob,
            this.civil_status,
            this.purok_id,
            this.contact_number,
            this.registered_sk_voter,
            this.registered_national_voter,
            this.attended_kk_assembly,
            this.partOfOrg,
            this.part_of_lgbtqia,
            this.isPwd,
            this.highest_educational_attainment
        ];
        
        if (this.partOfOrg === '1') {
            fields.push(this.youth_org_name);
        } else if (this.partOfOrg === '0') {
            fields.push(this.interested_in_joining);
        }
        
        if (this.isPwd === '1') {
            fields.push(this.registered_disability);
        }
        
        return fields;
    },

    get completeness() {
        const req = this.requiredFields;
        const filled = req.filter(val => {
            if (val === true) return true;
            if (val === false || val === '' || val === null || val === undefined) return false;
            return String(val).trim().length > 0;
        }).length;
        
        return Math.round((filled / req.length) * 100);
    },

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
            if (this.step < 4) {
                this.step++;
            }
        }
    },

    handlePrimaryAction() {
        if (this.step < 4) {
            this.nextStep();
            return;
        }

        if (this.validateStep(4)) {
            this.showConfirm = true;
        }
    },

    submitProfile() {
        if (!this.validateStep(4)) {
            return;
        }

        this.loading = true;
        document.getElementById('profileForm').submit();
    },

    prevStep() {
        if (this.step > 1) {
            this.step--;
        }
    }
}">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">Profiling</span>
            </nav>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="max-w-2xl space-y-2.5">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[9px] font-black uppercase tracking-widest font-display">Youth Portal</span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">Katipunan ng Kabataan Self Profiling</h1>
                    <p class="text-sm text-slate-300 leading-relaxed">Please complete all steps of the KK Profiling registry form to verify your residency and citizen status.</p>
                </div>
                <a href="{{ route('profile.my-requests') }}" class="inline-flex items-center justify-center min-h-11 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all text-white shrink-0 self-start sm:self-center">
                    Return to Portal
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto w-full overflow-x-hidden px-4 pb-32 sm:px-6 sm:pb-36 lg:px-8 lg:pb-40 py-8 md:py-10 space-y-6 animate-fade-in-up">
        
        <!-- Horizontal Citizen Sub-navigation -->
        @include('profile.partials.citizen-nav')

        <!-- Form Card -->
    <div class="bg-white border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
        
        <form id="profileForm" method="POST" action="{{ route('profile.profiling.store') }}" class="p-6 md:p-8 space-y-6">
            @csrf

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-semibold space-y-1 animate-fade-in">
                    <p class="font-bold text-rose-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Please resolve the following registration errors:
                    </p>
                    <ul class="list-disc pl-5 space-y-0.5 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Step Indicator / Progress Bar -->
            <div class="border-b border-slate-100 pb-5">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none max-w-xl mx-auto">
                    <!-- Step 1 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 1 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 1 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">1</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Consent</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 2 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                    <!-- Step 2 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 2 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 2 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">2</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Details</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 3 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                    <!-- Step 3 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 3 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 3 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">3</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Affiliations</span>
                    </div>
                    <div class="flex-1 border-t-2 mx-4 transition duration-300" :class="step >= 4 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                    <!-- Step 4 Indicator -->
                    <div class="flex flex-col items-center relative transition duration-300" :class="step >= 4 ? 'text-[#1e40af]' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px] transition duration-300"
                             :class="step >= 4 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-450'">4</div>
                        <span class="mt-1.5 text-[9px] uppercase font-bold tracking-wider font-display">Inclusivity</span>
                    </div>
                </div>
            </div>

            <!-- Form Completeness Progress Card -->
            <div class="bg-blue-50/40 dark:bg-slate-900/40 border border-blue-100/50 dark:border-slate-800/80 p-5 rounded-2xl mb-6">
                <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                    <span class="uppercase tracking-wider text-slate-500 dark:text-slate-400 font-display">Profile Completeness</span>
                    <span class="text-[#1e40af] dark:text-blue-400 text-sm font-black" x-text="completeness + '%'"></span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700/50 h-3 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-[#1e40af] h-full rounded-full transition-all duration-300" :style="'width: ' + completeness + '%'"></div>
                </div>
            </div>

            <!-- STEP 1: Data Privacy Consent -->
            <div x-show="step === 1" id="step-1" class="space-y-4">
                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">1. Informed Data Privacy Consent</h3>
                <div class="p-6 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl text-xs text-slate-600 dark:text-slate-400 leading-relaxed space-y-4 font-medium shadow-inner">
                    <p class="font-black text-slate-800 text-[13px] tracking-tight">Sangguniang Kabataan of Barangay Namayan - Data Privacy Notice & Consent Agreement</p>
                    <p>In accordance with <strong>Republic Act No. 10173</strong> (the <strong>Data Privacy Act of 2012</strong>), the Sangguniang Kabataan Council of Barangay Namayan hereby informs you of the protocols regarding your personal data:</p>
                    
                    <div class="space-y-3 pl-3 border-l-2 border-[#1e40af]/30">
                        <p><strong>1. Collection and Usage:</strong> We collect personal, demographic, educational, voter, and inclusivity information. This data will be processed and used solely for the Katipunan ng Kabataan profiling registry, youth services programming, community assistance targeting, and official reports to the National Youth Commission (NYC) and the Department of the Interior and Local Government (DILG).</p>
                        <p><strong>2. Storage and Security:</strong> Your data is transmitted over secure channels (HTTPS) and encrypted at rest in our systems. Only authorized SK officials have access to review or process database records.</p>
                        <p><strong>3. Rights of the Data Subject:</strong> You have the right to access, update, correct, or request deletion of your information from our database at any time by contacting the SK Secretariat.</p>
                    </div>

                    <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-tight">By checking the box below, you signify that you are at least 15 years of age and voluntarily give your consent to these terms.</p>
                </div>
                <div class="mt-4 flex items-start">
                    <div class="flex items-center h-5">
                        <input id="consent_checkbox" name="consent_given" type="checkbox" value="1" required x-model="consent_given" class="focus:ring-[#1e40af] h-4 w-4 text-[#1e40af] border-slate-350 rounded cursor-pointer">
                    </div>
                    <div class="ml-3 text-xs">
                        <label for="consent_checkbox" class="font-bold text-slate-700 cursor-pointer select-none">I have read and understood the Data Privacy Consent Notice and hereby give my voluntary consent to the collection, processing, use, and storage of my personal data for SK profiling purposes.</label>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Personal Details -->
            <div x-show="step === 2" id="step-2" class="space-y-4" x-cloak>
                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-wider border-b border-slate-100 pb-2">1. Personal Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Surname <span class="text-rose-500">*</span></label>
                        <input type="text" name="surname" value="{{ old('surname') }}" x-model="surname" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Dela Cruz">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', explode(' ', $user->name)[0] ?? '') }}" x-model="first_name" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Juan">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" x-model="middle_name" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Santiago">
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
                        <input type="number" name="age" value="{{ old('age') }}" x-model="age" min="15" max="30" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="15 to 30">
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
                        <input type="text" name="gender" value="{{ old('gender') }}" x-model="gender" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. LGBTQIA+">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                        <x-date-picker name="dob" value="{{ old('dob') }}" x-model="dob" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" />
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
                        <input type="text" name="street_address" value="{{ old('street_address') }}" x-model="street_address" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. 594 J.P Rizal Street">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Contact Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}" x-model="contact_number" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. 09171234567">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ $user->email }}" readonly class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed" placeholder="e.g. citizen@namayan.local">
                    </div>
                </div>
            </div>

            <!-- STEP 3: Affiliations -->
            <div x-show="step === 3" id="step-3" class="space-y-6" x-cloak>
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
                <div x-show="partOfOrg === '1'" x-transition class="space-y-2" x-cloak>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Name of Youth Organization <span class="text-rose-500">*</span></label>
                    <input type="text" name="youth_org_name" value="{{ old('youth_org_name') }}" x-model="youth_org_name" :required="partOfOrg === '1'" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Sangguniang Kabataan Movement">
                </div>

                <!-- Interested in joining (Conditional if No) -->
                <div x-show="partOfOrg === '0'" x-transition class="space-y-2 text-xs text-slate-700" x-cloak>
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

            <!-- STEP 4: Inclusivity & Education -->
            <div x-show="step === 4" id="step-4" class="space-y-6" x-cloak>
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
                            <label class="inline-flex items-center">
                                <input type="radio" name="pwd" value="1" x-model="isPwd" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="pwd" value="0" x-model="isPwd" required class="text-[#1e40af] focus:ring-[#1e40af]">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Disability Name (Conditional if Yes) -->
                <div x-show="isPwd === '1'" x-transition class="space-y-2" x-cloak>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Registered Disability <span class="text-rose-500">*</span></label>
                    <input type="text" name="registered_disability" value="{{ old('registered_disability') }}" x-model="registered_disability" :required="isPwd === '1'" class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. Visual Impairment">
                </div>

                <!-- Highest Educational Attainment -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Highest Educational Attainment <span class="text-rose-500">*</span></label>
                    <input type="text" name="highest_educational_attainment" value="{{ old('highest_educational_attainment') }}" x-model="highest_educational_attainment" required class="field focus:ring-4 focus:ring-blue-600/10 text-xs py-2 bg-slate-50/50 border border-slate-200 rounded-xl" placeholder="e.g. College Graduate, 2nd Year College">
                </div>
            </div>
            
            <!-- Navigation Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between shrink-0">
                <button type="button" 
                        x-show="step > 1" 
                        @click="prevStep()" 
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold rounded-xl transition text-xs uppercase tracking-wider select-none cursor-pointer">
                    &larr; Back
                </button>
                <div x-show="step === 1" class="w-10"></div> <!-- Placeholder -->
                
                <button type="button" 
                        x-show="step < 4" 
                        @click="handlePrimaryAction()" 
                        class="btn-primary text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer">
                    Next &rarr;
                </button>
                
                <button type="button" 
                        x-show="step === 4" 
                        @click="handlePrimaryAction()"
                        :disabled="loading"
                        class="btn-success text-xs uppercase tracking-wider py-2 px-5 font-bold rounded-xl select-none cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white border border-transparent transition active:scale-95 shadow-sm disabled:opacity-50">
                    <span x-text="loading ? 'Processing...' : 'Submit Profile'"></span>
                </button>
            </div>
        </form>
    </div>
    </div>

    <!-- Confirm Submission Modal -->
    <div x-show="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-xl max-w-sm w-full space-y-4 text-center transform scale-100 transition duration-200">
            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-955/45 text-amber-600 dark:text-amber-300 flex items-center justify-center text-xl mx-auto">⚠️</div>
            <div class="space-y-1">
                <h3 class="text-base font-black text-slate-800 dark:text-white uppercase font-display tracking-tight">Confirm Profile Submission</h3>
                <p class="text-[11px] text-slate-400">Are you sure you want to submit your Katipunan ng Kabataan profile registration? Check your details one last time.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="showConfirm = false" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold uppercase transition hover:bg-slate-50">
                    Go Back
                </button>
                <button type="button" @click="showConfirm = false; submitProfile()" class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase transition shadow-md active:scale-95">
                    Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>
<x-mobile-bottom-action @click="handlePrimaryAction()" x-bind:disabled="loading" x-cloak>
    <span x-show="step < 4">Next Step &rarr;</span>
    <span x-show="step === 4">Submit Profile</span>
</x-mobile-bottom-action>

<!-- Example bottom-sheet component (reusable slide-up panel) -->
<x-bottom-sheet title="Submit Registration" buttonText="Open Registration">
    {{-- Example form skeleton: no DB logic here; replace with actual fields as needed --}}
    <form id="bottomSheetForm" method="POST" action="#" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 gap-3">
            <label class="text-xs font-semibold">Full name</label>
            <input name="name" type="text" class="field rounded-xl w-full" placeholder="e.g., Juan Dela Cruz">

            <label class="text-xs font-semibold">Email</label>
            <input name="email" type="email" class="field rounded-xl w-full" placeholder="email@example.com">
        </div>

        <div class="pt-2">
            <!-- This button demonstrates submitting the main profile form from within the sheet -->
            <button type="button" onclick="document.getElementById('profileForm').submit();" class="w-full rounded-2xl bg-[#1e40af] hover:bg-blue-700 text-white py-3 font-bold uppercase tracking-wider">Submit Profile</button>
        </div>
    </form>
</x-bottom-sheet>

@endsection
