@extends('layouts.app')

@section('content')
<div class="flex-1 bg-slate-50 dark:bg-slate-950 font-sans min-h-screen"
     x-data="{
         showFormModal: {{ $errors->any() || session('failed_form') === 'sports' ? 'true' : 'false' }},
         showConfirm: false,
         loading: false,
         formConfirmed: false,
         sport: '{{ old('sport', '') }}',
         division: '{{ old('division', '') }}',
         birthdate: '{{ old('birthdate', $kkProfile?->dob ? $kkProfile->dob->format('Y-m-d') : '') }}',
         age: '{{ old('age', $kkProfile?->age ?? '') }}',
         regStep: {{ $errors->any() ? '5' : "(localStorage.getItem('sports_reg_step') ? parseInt(localStorage.getItem('sports_reg_step')) : 1)" }},
         validateRegStep(s) {
             const stepContainer = document.getElementById(`reg-step-${s}`);
             if (!stepContainer) return true;
             const fields = stepContainer.querySelectorAll('[required]');
             let valid = true;
             let firstInvalid = null;
             fields.forEach(field => {
                 // Ignore fields that are not visible in the DOM
                 if (field.offsetParent === null && field.type !== 'file') {
                     return;
                 }
                 const rect = field.getBoundingClientRect();
                 if (rect.width === 0 && rect.height === 0 && field.type !== 'file') {
                     return;
                 }
                 if (field.type === 'file') {
                     if (!field.files || field.files.length === 0) {
                         valid = false;
                         if (!firstInvalid) firstInvalid = field;
                     }
                 } else {
                     if (!field.value || !field.value.trim() || !field.checkValidity()) {
                         valid = false;
                         if (!firstInvalid) firstInvalid = field;
                     }
                 }
             });
             if (!valid && firstInvalid) {
                 firstInvalid.reportValidity();
                 firstInvalid.focus();
             }
             return valid;
         },
         nextRegStep() {
             if (this.validateRegStep(this.regStep)) {
                 if (this.regStep < 5) {
                     this.regStep++;
                     localStorage.setItem('sports_reg_step', this.regStep);
                 }
             }
         },
         prevRegStep() {
             if (this.regStep > 1) {
                 this.regStep--;
                 localStorage.setItem('sports_reg_step', this.regStep);
             }
         },
         divisions() {
             if (this.sport === 'Basketball') {
                 return [
                     { value: 'Midget', label: 'Midget [ Edad 6 hanggang 12 ]' },
                     { value: 'Juniors', label: 'Juniors [ Edad 13 hanggang 17 ]' },
                     { value: 'Seniors', label: 'Seniors [ Edad 18 hanggang 39 ]' }
                 ];
             } else if (this.sport === 'Volleyball') {
                 return [
                     { value: 'Mens', label: 'Men\'s' },
                     { value: 'Womens', label: 'Women\'s' }
                 ];
             }
             return [];
         },
         ageWarning() {
             if (!this.age || !this.division) return '';
             const ageNum = parseInt(this.age);
             if (this.division === 'Midget' && (ageNum < 6 || ageNum > 12)) {
                 return 'Warning: Age must be 6 to 12 years old for Midget division.';
             }
             if (this.division === 'Juniors' && (ageNum < 13 || ageNum > 17)) {
                 return 'Warning: Age must be 13 to 17 years old for Juniors division.';
             }
             if (this.division === 'Seniors' && (ageNum < 18 || ageNum > 39)) {
                 return 'Warning: Age must be 18 to 39 years old for Seniors division.';
             }
             if ((this.division === 'Mens' || this.division === 'Womens') && ageNum < 15) {
                 return 'Warning: Age must be 15 years or older for Volleyball.';
             }
             return '';
         },
         calculateAge() {
             if (!this.birthdate) {
                 this.age = '';
                 return;
             }
             const birthDate = new Date(this.birthdate);
             const today = new Date();
             let age = today.getFullYear() - birthDate.getFullYear();
             const m = today.getMonth() - birthDate.getMonth();
             if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                 age--;
             }
             this.age = age >= 0 ? age : 0;
         },
         get isMinor() {
             return this.age !== '' && this.age < 18;
         },
         get isAdult() {
             return this.age !== '' && this.age >= 18;
         }
     }"
     x-init="calculateAge()">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">SIKLAB</span>
            </nav>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="max-w-2xl space-y-2.5">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest font-display">Sports Portal</span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">SIKLAB Registration</h1>
                    <p class="text-sm text-slate-300 leading-relaxed">View active sports tournaments and submit your registration details.</p>
                </div>
                @if(!$alreadyRegistered)
                    <button @click="sport = ''; division = ''; regStep = 1; showFormModal = true" class="inline-flex items-center justify-center min-h-11 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all text-white shrink-0 self-start sm:self-center cursor-pointer">
                        Register Now
                    </button>
                @endif
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8 md:py-10 space-y-6 animate-fade-in-up">

        <!-- Horizontal Citizen Sub-navigation -->
        @include('profile.partials.citizen-nav')

        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/30 rounded-2xl flex items-start gap-3 shadow-sm transition">
                <span class="text-emerald-500 text-lg font-bold">✓</span>
                <div>
                    <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-450">Registration Submitted Successfully!</h4>
                    <p class="text-xs text-emerald-605 dark:text-emerald-500 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/30 rounded-2xl flex items-start gap-3 shadow-sm transition">
                <span class="text-rose-500 text-lg font-bold">⚠</span>
                <div>
                    <h4 class="text-sm font-bold text-rose-805 dark:text-rose-455">Registration Constraint Warning</h4>
                    <p class="text-xs text-rose-605 dark:text-rose-500 mt-0.5 font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($alreadyRegistered)
            <div class="p-4 bg-amber-50 dark:bg-amber-955/40 border border-amber-200 dark:border-amber-900/30 rounded-2xl flex items-start gap-3 shadow-sm transition">
                <span class="text-amber-500 text-lg font-bold">⚠</span>
                <div>
                    <h4 class="text-sm font-bold text-amber-805 dark:text-amber-450">Active Sports Registration Detected</h4>
                    <p class="text-xs text-amber-605 dark:text-amber-500 mt-0.5 leading-relaxed">
                        You already have an active registration for **{{ $alreadyRegistered->sport }}** ({{ $alreadyRegistered->division }} Division).
                        To maintain league rules, citizens are permitted only **one active registration** across all sports tournaments.
                        You can track your request status under <a href="{{ route('profile.my-requests') }}" class="font-bold underline text-[#1e40af] dark:text-blue-400">My Requests</a>.
                    </p>
                </div>
            </div>
        @endif        <!-- Active Tournaments Section -->
        <div class="space-y-4">
            <h3 class="text-xs font-black text-slate-705 dark:text-slate-350 uppercase tracking-widest block font-display">Active Tournaments</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basketball Card -->
                <div class="card p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex flex-col justify-between shadow-sm relative transition hover:shadow-md">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase tracking-wider">
                                Basketball
                            </span>
                            <span class="text-xs text-slate-400">SIKAP AT ALAB NG BATANG NAMAYAN</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white font-display uppercase tracking-tight">Basketball Tournament</h4>
                        <p class="text-xs text-slate-505 dark:text-slate-405 leading-relaxed">Select a division below to start your registration. Minors must fill in guardian details.</p>

                        <div class="space-y-2 pt-2">
                            <!-- Division Midget -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-xl hover:bg-slate-100/50 dark:hover:bg-slate-900 transition">
                                <div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Midget Division</span>
                                    <span class="text-[9px] text-slate-450 dark:text-slate-500">Edad 6 hanggang 12 taong gulang</span>
                                </div>
                                @if(!$alreadyRegistered)
                                    <button @click="sport = 'Basketball'; division = 'Midget'; regStep = 3; showFormModal = true; calculateAge()"
                                            class="px-3.5 py-1.5 bg-[#1e40af] hover:bg-blue-700 text-white text-[9px] font-black uppercase tracking-wider rounded-lg transition active:scale-95 cursor-pointer">
                                        Register
                                    </button>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg uppercase tracking-wider">Locked</span>
                                @endif
                            </div>

                            <!-- Division Juniors -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-xl hover:bg-slate-100/50 dark:hover:bg-slate-900 transition">
                                <div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Juniors Division</span>
                                    <span class="text-[9px] text-slate-450 dark:text-slate-500">Edad 13 hanggang 17 taong gulang</span>
                                </div>
                                @if(!$alreadyRegistered)
                                    <button @click="sport = 'Basketball'; division = 'Juniors'; regStep = 3; showFormModal = true; calculateAge()"
                                            class="px-3.5 py-1.5 bg-[#1e40af] hover:bg-blue-700 text-white text-[9px] font-black uppercase tracking-wider rounded-lg transition active:scale-95 cursor-pointer">
                                        Register
                                    </button>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg uppercase tracking-wider">Locked</span>
                                @endif
                            </div>

                            <!-- Division Seniors -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-xl hover:bg-slate-100/50 dark:hover:bg-slate-900 transition">
                                <div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Seniors Division</span>
                                    <span class="text-[9px] text-slate-450 dark:text-slate-500">Edad 18 hanggang 39 taong gulang</span>
                                </div>
                                @if(!$alreadyRegistered)
                                    <button @click="sport = 'Basketball'; division = 'Seniors'; regStep = 3; showFormModal = true; calculateAge()"
                                            class="px-3.5 py-1.5 bg-[#1e40af] hover:bg-blue-700 text-white text-[9px] font-black uppercase tracking-wider rounded-lg transition active:scale-95 cursor-pointer">
                                        Register
                                    </button>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg uppercase tracking-wider">Locked</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volleyball Card -->
                <div class="card p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex flex-col justify-between shadow-sm relative transition hover:shadow-md">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[9px] font-black uppercase tracking-wider">
                                Volleyball
                            </span>
                            <span class="text-xs text-slate-400">SIKAP AT ALAB NG BATANG NAMAYAN</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white font-display uppercase tracking-tight">Volleyball Tournament</h4>
                        <p class="text-xs text-slate-505 dark:text-slate-405 leading-relaxed">Select a division below to start your registration. Minors must fill in guardian details.</p>

                        <div class="space-y-2 pt-2">
                            <!-- Men's Division -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-xl hover:bg-slate-100/50 dark:hover:bg-slate-900 transition">
                                <div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Men's Division</span>
                                    <span class="text-[9px] text-slate-450 dark:text-slate-500">Edad 15 pataas (Ages 15 and above)</span>
                                </div>
                                @if(!$alreadyRegistered)
                                    <button @click="sport = 'Volleyball'; division = 'Mens'; regStep = 3; showFormModal = true; calculateAge()"
                                            class="px-3.5 py-1.5 bg-[#1e40af] hover:bg-blue-700 text-white text-[9px] font-black uppercase tracking-wider rounded-lg transition active:scale-95 cursor-pointer">
                                        Register
                                    </button>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg uppercase tracking-wider">Locked</span>
                                @endif
                            </div>

                            <!-- Women's Division -->
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-955 border border-slate-100 dark:border-slate-850 rounded-xl hover:bg-slate-100/50 dark:hover:bg-slate-900 transition">
                                <div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Women's Division</span>
                                    <span class="text-[9px] text-slate-450 dark:text-slate-500">Edad 15 pataas (Ages 15 and above)</span>
                                </div>
                                @if(!$alreadyRegistered)
                                    <button @click="sport = 'Volleyball'; division = 'Womens'; regStep = 3; showFormModal = true; calculateAge()"
                                            class="px-3.5 py-1.5 bg-[#1e40af] hover:bg-blue-700 text-white text-[9px] font-black uppercase tracking-wider rounded-lg transition active:scale-95 cursor-pointer">
                                        Register
                                    </button>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg uppercase tracking-wider">Locked</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Registration Modal -->
    <div x-show="showFormModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>

         <!-- Modal Box -->
         <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl relative"
              @click.away="showFormModal = false"
              x-transition:enter="transition ease-out duration-300 transform scale-95"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-200 transform scale-100"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95">

              <!-- Modal Header -->
              <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between shrink-0 gap-4">
                  <div class="flex-1 min-w-0">
                      <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider font-display truncate">League Registration Form</h3>
                      <p class="text-[10px] text-slate-450 dark:text-slate-500 mt-0.5">Please provide correct details. Minors will require guardian validation.</p>
                  </div>
                  <button @click="showFormModal = false" type="button" class="shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl transition cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 -mr-2 -mt-1 focus:outline-none">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>
              </div>

              <!-- Step Progress Indicator -->
              <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 flex items-center justify-between text-xs font-semibold text-slate-400 select-none shrink-0">
                  <div class="flex items-center gap-1.5" :class="regStep >= 1 ? 'text-[#1e40af] dark:text-blue-400' : 'text-slate-400'">
                      <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold"
                            :class="regStep >= 1 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-400'">1</span>
                      <span class="hidden sm:inline text-[9px] uppercase tracking-wider">Sport</span>
                  </div>
                  <div class="flex-1 border-t border-dashed mx-2" :class="regStep >= 2 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                  <div class="flex items-center gap-1.5" :class="regStep >= 2 ? 'text-[#1e40af] dark:text-blue-400' : 'text-slate-400'">
                      <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold"
                            :class="regStep >= 2 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-400'">2</span>
                      <span class="hidden sm:inline text-[9px] uppercase tracking-wider">Division</span>
                  </div>
                  <div class="flex-1 border-t border-dashed mx-2" :class="regStep >= 3 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                  <div class="flex items-center gap-1.5" :class="regStep >= 3 ? 'text-[#1e40af] dark:text-blue-400' : 'text-slate-400'">
                      <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold"
                            :class="regStep >= 3 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-400'">3</span>
                      <span class="hidden sm:inline text-[9px] uppercase tracking-wider">Personal</span>
                  </div>
                  <div class="flex-1 border-t border-dashed mx-2" :class="regStep >= 4 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                  <div class="flex items-center gap-1.5" :class="regStep >= 4 ? 'text-[#1e40af] dark:text-blue-400' : 'text-slate-400'">
                      <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold"
                            :class="regStep >= 4 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-400'">4</span>
                      <span class="hidden sm:inline text-[9px] uppercase tracking-wider">Files</span>
                  </div>
                  <div class="flex-1 border-t border-dashed mx-2" :class="regStep >= 5 ? 'border-[#1e40af]' : 'border-slate-200'"></div>

                  <div class="flex items-center gap-1.5" :class="regStep >= 5 ? 'text-[#1e40af] dark:text-blue-400' : 'text-slate-400'">
                      <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold"
                            :class="regStep >= 5 ? 'border-[#1e40af] bg-[#1e40af] text-white' : 'border-slate-200 bg-white text-slate-400'">5</span>
                      <span class="hidden sm:inline text-[9px] uppercase tracking-wider font-display">Waiver</span>
                  </div>
              </div>

              <!-- Modal Body (Scrollable Form) -->
              <div class="p-6 md:p-8 pb-24 md:pb-8 overflow-y-auto flex-1">

                  @if($errors->any())
                      <div class="p-4 mb-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-250 dark:border-rose-900/30 rounded-2xl flex items-start gap-3">
                          <span class="text-rose-500 font-bold">⚠</span>
                          <div>
                              <h4 class="text-xs font-bold text-rose-800 dark:text-rose-455">Please correct the validation errors:</h4>
                              <ul class="list-disc pl-4 text-[10px] text-rose-600 dark:text-rose-505 mt-1 font-semibold space-y-0.5">
                                  @foreach($errors->all() as $err)
                                      <li>{{ $err }}</li>
                                  @endforeach
                              </ul>
                          </div>
                      </div>
                  @endif

                  <form method="POST" action="{{ route('forms.sports.store') }}" enctype="multipart/form-data" class="request-form space-y-6" x-ref="regForm" id="sportsRegForm" novalidate @submit="if (!formConfirmed) { $event.preventDefault(); showConfirm = true; }">
                      @csrf

                      <!-- Step 1: Sport Selection -->
                      <div x-show="regStep === 1" id="reg-step-1" class="space-y-4">
                          <h3 class="text-xs font-black uppercase tracking-wider text-[#1e40af] dark:text-blue-400 border-b border-slate-105 dark:border-slate-850 pb-2">
                              1. Select Sport Category
                          </h3>
                          <div>
                              <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                  Sport <span class="text-rose-500">*</span>
                              </label>
                              <select name="sport" x-model="sport" @change="division = ''" :required="regStep === 1" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer">
                                  <option value="">Select Sport</option>
                                  <option value="Basketball">Basketball</option>
                                  <option value="Volleyball">Volleyball</option>
                              </select>
                          </div>
                      </div>

                      <!-- Step 2: Division details -->
                      <div x-show="regStep === 2" id="reg-step-2" class="space-y-4" x-cloak>
                          <h3 class="text-xs font-black uppercase tracking-wider text-[#1e40af] dark:text-blue-400 border-b border-slate-105 dark:border-slate-850 pb-2">
                              2. League Category Details
                          </h3>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Division <span class="text-rose-500">*</span>
                                  </label>
                                  <select name="division" x-model="division" :required="regStep === 2" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer">
                                      <option value="">Select Division</option>
                                      <template x-for="div in divisions()" :key="div.value">
                                          <option :value="div.value" x-text="div.label" :selected="division === div.value"></option>
                                      </template>
                                  </select>
                              </div>
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Position <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="text" name="position" value="{{ old('position') }}" :required="regStep === 2" placeholder="e.g. Point Guard, Spiker"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                              </div>
                          </div>
                          <div>
                              <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                  Team Name (Optional)
                              </label>
                              <input type="text" name="team_name" value="{{ old('team_name') }}" placeholder="Enter team name if registering as a team"
                                     class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                          </div>
                      </div>

                      <!-- Step 3: Participant Information -->
                      <div x-show="regStep === 3" id="reg-step-3" class="space-y-4" x-cloak>
                          <h3 class="text-xs font-black uppercase tracking-wider text-[#1e40af] dark:text-blue-400 border-b border-slate-105 dark:border-slate-850 pb-2">
                              3. Participant Information
                          </h3>
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      First Name <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="text" name="first_name" value="{{ mb_strtoupper(old('first_name', $kkProfile?->first_name ?? (auth()->user() ? auth()->user()->first_name : '')), 'UTF-8') }}" required placeholder="First Name"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                              </div>
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Middle Name <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-medium lowercase">(type 'NONE' or 'N/A' if none)</span>
                                  </label>
                                  <input type="text" name="middle_name" value="{{ mb_strtoupper(old('middle_name', $kkProfile?->middle_name ?? ''), 'UTF-8') }}" required placeholder="Middle Name"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                              </div>
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Last Name <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="text" name="last_name" value="{{ mb_strtoupper(old('last_name', $kkProfile?->surname ?? (auth()->user() ? auth()->user()->last_name : '')), 'UTF-8') }}" required placeholder="Last Name"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                              </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Birthdate <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="date" name="birthdate" x-model="birthdate" @change="calculateAge()" required
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                              </div>
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Age <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="number" name="age" x-model="age" readonly required placeholder="Computed automatically"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-3.5 py-2.5 text-xs dark:text-white outline-none cursor-not-allowed">
                                  <p x-show="ageWarning()" x-text="ageWarning()" class="text-amber-600 dark:text-amber-400 text-[9px] font-bold mt-1" x-cloak></p>
                              </div>
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Gender <span class="text-rose-500">*</span>
                                  </label>
                                  @php
                                      $selectedGender = old('gender', $kkProfile?->gender ?? $kkProfile?->sex ?? '');
                                  @endphp
                                  <select name="gender" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer">
                                      <option value="">Select Gender</option>
                                      <option value="Male" {{ $selectedGender === 'Male' ? 'selected' : '' }}>Male</option>
                                      <option value="Female" {{ $selectedGender === 'Female' ? 'selected' : '' }}>Female</option>
                                      <option value="Prefer not to say" {{ $selectedGender === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                  </select>
                              </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Email Address <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="email" name="email" value="{{ old('email', $kkProfile?->email ?? (auth()->user() ? auth()->user()->email : '')) }}" required placeholder="e.g. participant@example.com"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                              </div>
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Contact Number <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="text" name="contact_number" value="{{ old('contact_number', $kkProfile?->contact_number ?? '') }}" required placeholder="e.g. 09171234567"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                              </div>
                          </div>

                          <div>
                              <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                  Complete Address <span class="text-rose-500">*</span>
                              </label>
                              <input type="text" name="address" value="{{ mb_strtoupper(old('address', $kkProfile?->street_address ? ($kkProfile->street_address . ', PUROK ' . ($kkProfile->purok?->purok_name ?? '')) : ''), 'UTF-8') }}" required placeholder="Block, Lot, Street, Barangay, Mandaluyong City"
                                     class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Profile Picture (2x2 / Passport size) <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="file" name="profile_picture" :required="regStep === 3"
                                         class="w-full text-xs text-slate-505 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] dark:file:bg-slate-800 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-slate-755 transition cursor-pointer">
                              </div>
                          </div>
                      </div>

                      <!-- Step 4: Verification Documents (Guardian Details vs Voter Cert) -->
                      <div x-show="regStep === 4" id="reg-step-4" class="space-y-4" x-cloak>
                          <!-- For Minors Under 18 -->
                          <div x-show="isMinor" class="space-y-4">
                              <h3 class="text-xs font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 border-b border-slate-105 dark:border-slate-850 pb-2">
                                  4. Parent / Guardian Information (Required for Minors)
                              </h3>
                              <p class="text-[11px] text-slate-450 dark:text-slate-400">Because you are under 18 years old, please provide the details of your parent or legal guardian.</p>

                              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                  <div>
                                      <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                          Guardian First Name <span class="text-rose-500">*</span>
                                      </label>
                                      <input type="text" name="guardian_first_name" value="{{ old('guardian_first_name') }}" :required="isMinor" placeholder="First Name"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                  </div>
                                  <div>
                                      <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                          Guardian Middle Name
                                      </label>
                                      <input type="text" name="guardian_middle_name" value="{{ old('guardian_middle_name') }}" placeholder="Middle Name"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                  </div>
                                  <div>
                                      <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                          Guardian Last Name <span class="text-rose-500">*</span>
                                      </label>
                                      <input type="text" name="guardian_last_name" value="{{ old('guardian_last_name') }}" :required="isMinor" placeholder="Last Name"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                  </div>
                              </div>

                              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                  <div>
                                      <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                          Guardian Age <span class="text-rose-500">*</span>
                                      </label>
                                      <input type="number" name="guardian_age" value="{{ old('guardian_age') }}" :required="isMinor" min="18" placeholder="e.g. 45"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                                  </div>
                                  <div>
                                      <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                          Relationship <span class="text-rose-500">*</span>
                                      </label>
                                      <input type="text" name="guardian_relation" value="{{ old('guardian_relation') }}" :required="isMinor" placeholder="e.g. Father, Mother, Aunt"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                  </div>
                                  <div>
                                      <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                          Guardian Contact Number <span class="text-rose-500">*</span>
                                      </label>
                                      <input type="text" name="guardian_contact_number" value="{{ old('guardian_contact_number') }}" :required="isMinor" placeholder="e.g. 09179998888"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                                  </div>
                              </div>

                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Guardian Complete Address <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="text" name="guardian_address" value="{{ old('guardian_address') }}" :required="isMinor" placeholder="Complete address of parent/guardian"
                                         class="w-full rounded-xl border border-slate-200 dark:border-slate-750 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                              </div>

                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      Guardian Government Valid ID <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="file" name="guardian_gov_id" :required="isMinor"
                                         class="w-full text-xs text-slate-505 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] dark:file:bg-slate-800 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-slate-755 transition cursor-pointer">
                              </div>
                          </div>

                          <!-- For Adults 18+ -->
                          <div x-show="isAdult" class="space-y-4">
                              <h3 class="text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-450 border-b border-slate-105 dark:border-slate-850 pb-2">
                                  4. Valid goverment ID
                              </h3>
                              <p class="text-[11px] text-slate-455 dark:text-slate-400">Because you are 18 years or older, please upload your valid goverment ID for Barangay verification.</p>

                              <div>
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                      UPLOAD YOUR VALID GOVERMENT ID, FOR SK VERIFICATION <span class="text-rose-500">*</span>
                                  </label>
                                  <input type="file" name="voter_cert" :required="isAdult"
                                         class="w-full text-xs text-slate-505 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] dark:file:bg-slate-800 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-slate-755 transition cursor-pointer">
                              </div>
                          </div>
                      </div>

                      <!-- Step 5: Waivers & Agreements -->
                      <div x-show="regStep === 5" id="reg-step-5" class="space-y-4" x-cloak>
                          <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-350 border-b border-slate-105 dark:border-slate-850 pb-2">
                              5. Health and Liability Waivers
                          </h3>

                          <div class="space-y-4">
                              <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl space-y-2">
                                  <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 font-display">
                                      Health Declaration Details <span class="text-rose-500">*</span>
                                  </label>
                                  <p class="text-[10px] text-slate-400 mb-2">Please declare any existing medical conditions or allergies (type "None" if healthy).</p>
                                  <textarea name="health_declaration" required rows="3" placeholder="Specify any health concerns, asthma, allergies, or type 'None' if fit to play."
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition resize-none font-sans">{{ old('health_declaration') }}</textarea>
                              </div>

                              <label class="flex items-start gap-3 p-3 border border-slate-100 dark:border-slate-855 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-900 transition cursor-pointer select-none">
                                  <input type="checkbox" name="consent_waiver" value="1" required class="rounded border-slate-350 text-[#1e40af] focus:ring-0 mt-0.5">
                                  <div class="text-[11px] text-slate-500 dark:text-slate-455 leading-relaxed">
                                      <strong class="font-bold text-slate-750 dark:text-slate-200">Consent Waiver and Liability Agreement:</strong>
                                      I hereby certify that the above information is true and correct. I declare that I am physically fit to participate in SIKLAB, and I release the administrative council of Barangay Namayan from any liability for injury or accidents occurring during the tournament.
                                  </div>
                              </label>
                          </div>

                          <div>
                              <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                  Remarks / Additional Notes (Optional)
                              </label>
                              <textarea name="remarks" rows="2" placeholder="e.g. Jersey size, group note, or scheduling request"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition resize-none font-sans">{{ old('remarks') }}</textarea>
                          </div>
                      </div>

                      <!-- Form Buttons -->
                      <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-850 gap-3 shrink-0">
                          <button type="button"
                                  x-show="regStep > 1"
                                  @click="prevRegStep()"
                                  class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition select-none cursor-pointer">
                              &larr; Back
                          </button>
                          <button type="button"
                                  x-show="regStep === 1"
                                  @click="showFormModal = false"
                                  class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition select-none cursor-pointer">
                              Cancel
                          </button>
                          <div class="flex items-center gap-3">
                              <button type="button"
                                      x-show="regStep < 5"
                                      @click="nextRegStep()"
                                      class="inline-flex items-center px-6 py-3 rounded-xl bg-[#1e40af] hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm select-none cursor-pointer">
                                  Next &rarr;
                              </button>
                              <button type="submit"
                                      x-show="regStep === 5"
                                      :disabled="loading"
                                      class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm disabled:opacity-50 select-none cursor-pointer">
                                  <span x-text="loading ? 'Processing...' : 'Submit Registration'"></span>
                              </button>
                          </div>
                      </div>
                  </form>
              </div>
         </div>
    </div>

    <!-- Confirm Submission Modal -->
    <div x-show="showConfirm" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-xl max-w-sm w-full space-y-4 text-center transform scale-100 transition duration-200">
            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-955/45 text-amber-600 dark:text-amber-300 flex items-center justify-center text-xl mx-auto">⚠️</div>
            <div class="space-y-1">
                <h3 class="text-base font-black text-slate-800 dark:text-white uppercase font-display tracking-tight">Confirm Submission</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Are you sure you want to submit your sports registration? Please review all inputs before confirming.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="showConfirm = false" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold uppercase transition hover:bg-slate-50 dark:hover:bg-slate-800">
                    Go Back
                </button>
                <button type="button" @click="showConfirm = false; formConfirmed = true; loading = true; $nextTick(() => { $refs.regForm.submit(); })" class="flex-1 py-2.5 rounded-xl bg-[#1e40af] hover:bg-blue-700 text-white text-xs font-bold uppercase transition shadow-md active:scale-95">
                    Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<x-mobile-bottom-action
    x-show="showFormModal"
    @click="regStep < 5 ? nextRegStep() : (validateRegStep(5) ? (showConfirm = true) : null)"
    x-bind:disabled="loading"
    x-cloak
>
    <span x-show="regStep < 5">Next Step &rarr;</span>
    <span x-show="regStep === 5">Submit Registration</span>
</x-mobile-bottom-action>

<style>
    input[required]:not([type="file"]):not([type="checkbox"]):not([type="radio"]),
    textarea[required] {
        text-transform: uppercase;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Force uppercase on required input fields
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

    // 2. Persist Form Drafts across page refreshes
    const form = document.getElementById('sportsRegForm');
    if (form) {
        // Load saved values from localStorage
        const savedData = localStorage.getItem('sports_registration_draft');
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(key => {
                    const el = form.querySelector(`[name="${key}"]`);
                    if (el) {
                        if (el.type === 'checkbox') {
                            el.checked = !!data[key];
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        } else if (el.type === 'radio') {
                            const radioOpt = form.querySelector(`[name="${key}"][value="${data[key]}"]`);
                            if (radioOpt) {
                                radioOpt.checked = true;
                                radioOpt.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        } else {
                            el.value = data[key];
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                });
            } catch (e) {
                console.error('Error restoring draft:', e);
            }
        }

        // Save values to localStorage on input/change
        const saveFormState = () => {
            const formData = new FormData(form);
            const data = {};
            for (let [key, val] of formData.entries()) {
                if (key === '_token' || val instanceof File) continue;
                data[key] = val;
            }
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                if (cb.name) {
                    data[cb.name] = cb.checked;
                }
            });
            localStorage.setItem('sports_registration_draft', JSON.stringify(data));
        };

        form.addEventListener('input', saveFormState);
        form.addEventListener('change', saveFormState);

        // Clear draft upon successful submit
        form.addEventListener('submit', () => {
            if (form.checkValidity()) {
                localStorage.removeItem('sports_registration_draft');
                localStorage.removeItem('sports_reg_step');
            }
        });
    }
});
</script>
@endsection
