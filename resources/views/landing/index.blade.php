@extends('layouts.app')

@section('content')
<!-- Landing page container using Alpine.js for modal state -->
<div x-data="{
    activeCategory: null,
    showModal: false,
    activeForm: '{{ session('failed_form') ?? request()->query('form') }}',
    isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},
    customInitiative: null,
    categoriesData: {
        'education': {
            label: 'EDUCATION',
            subtopics: [
                { name: 'SILID KARUNUNGAN', url: '{{ route('forms.silid.create') }}', active: true },
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'TIPD', url: '#', active: false },
                { name: 'OTHER PROJECTS', url: '#', active: false }
            ]
        },
        'health': {
            label: 'HEALTH',
            subtopics: [
                { name: 'MENTAL HEALTH SUPPORT', url: '{{ route('forms.mental-health.create') }}', active: true },
                { name: 'HEALTH CONSULTATION', url: '{{ route('forms.health.create') }}', active: true },
                { name: 'SPORTS', url: '{{ route('forms.sports.create') }}', active: true },
                { name: 'PABILI MEDICINE SERVICES', url: '{{ route('forms.medicine.create') }}', active: true },
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true }
            ]
        },
        'governance': {
            label: 'GOVERNANCE',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'SANGGUNIANG KABATAAN ASSEMBLY', url: '#', active: false },
                { name: 'LEGISLATIVE TRACKER', url: '#', active: false }
            ]
        },
        'active-citizenship': {
            label: 'ACTIVE CITIZENSHIP',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'YOUTH VOLUNTEER CORPS', url: '#', active: false }
            ]
        },
        'social-inclusion': {
            label: 'SOCIAL INCLUSION',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'ACCESSIBILITY AID', url: '#', active: false }
            ]
        },
        'peace-building': {
            label: 'PEACE BUILDING, DISASTER RISK REDUCTION MANAGEMENT',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'CONFLICT RESOLUTION', url: '#', active: false }
            ]
        },
        'environment': {
            label: 'ENVIRONMENT',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'ECO-WARRIORS REGISTRATION', url: '#', active: false }
            ]
        },
        'youth-employment': {
            label: 'YOUTH EMPLOYMENT & EMPOWERMENT',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'INTERNSHIP PORTAL', url: '#', active: false },
                { name: 'SK LIKHA WORKSHOPS', url: '#', active: false }
            ]
        },
        'agriculture': {
            label: 'AGRICULTURE',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'KABATAANG AGRI-PINS', url: '#', active: false }
            ]
        },
        'global-mobility': {
            label: 'GLOBAL MOBILITY',
            subtopics: [
                { name: 'TRACK REQUEST', url: '{{ route('track.index') }}', active: true },
                { name: 'SCHOLARSHIP VERIFICATION', url: '#', active: false }
            ]
        }
    },
    openCategory(key) {
        this.activeCategory = key;
        this.showModal = true;
    },
    openForm(formName) {
        if (!this.isAuthenticated) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        this.activeForm = formName;
    },
    openCustomForm(initiative) {
        if (!this.isAuthenticated) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        this.customInitiative = initiative;
        this.activeForm = 'custom';
    },
    handleCtaClick(url) {
        if (!url || url === '#') return;
        if (url.includes('health-consultation') || url.includes('mental-health') || url.includes('pabili-medicine') || url.includes('silid-karunungan')) {
            if (!this.isAuthenticated) {
                window.location.href = '{{ route('login') }}';
                return;
            }
        }
        if (url.includes('health-consultation')) {
            this.activeForm = 'health';
        } else if (url.includes('mental-health')) {
            this.activeForm = 'mental-health';
        } else if (url.includes('pabili-medicine')) {
            this.activeForm = 'medicine';
        } else if (url.includes('silid-karunungan')) {
            this.activeForm = 'silid';
        } else {
            window.location.href = url;
        }
    }
}" x-init="$watch('activeForm', value => {
    if (value) {
        document.body.classList.add('overflow-y-hidden');
    } else {
        document.body.classList.remove('overflow-y-hidden');
    }
});
if (activeForm) {
    document.body.classList.add('overflow-y-hidden');
}" class="space-y-16">

    @include('landing.sections.hero-carousel')

    @include('landing.sections.quick-actions')

    @include('landing.sections.featured-programs')

    @include('landing.sections.highlighted-programs')

    @include('landing.sections.news-section')

    @include('landing.sections.announcements')

    <!-- 7. CTA Banner (Full-bleed gradient banner, blobs) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8 pb-12 reveal-on-scroll">
        <div class="relative rounded-3xl bg-gradient-to-r from-blue-700 to-indigo-900 text-white py-12 px-4 sm:p-16 overflow-hidden text-center shadow-lg">

            <!-- Decorative gradient blobs -->
            <div class="absolute -top-16 -left-16 w-48 h-48 bg-blue-500/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-indigo-500/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 max-w-xl mx-auto space-y-4">
                <h2 class="text-3xl font-black font-display text-white uppercase tracking-tight leading-tight">Kabataan, Kilos na!</h2>
                <p class="text-slate-200 text-xs sm:text-sm max-w-sm mx-auto leading-relaxed">
                    Create an account today to easily keep logs of all your requests and submit applications without repetitive typing.
                </p>
                <div class="flex items-center justify-center gap-3 pt-3">
                    @guest
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-md">Create Account</a>
                        <a href="{{ route('login') }}" class="px-5 py-2.5 border border-white/20 hover:border-white text-white font-bold text-xs uppercase tracking-wider rounded-xl transition hover:bg-white/5 active:scale-95">Sign In</a>
                    @else
                        @if(Auth::user()->canAccessDashboard())
                            <a href="{{ route('dashboard.index') }}" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-md">View Admin Dashboard</a>
                        @else
                            <a href="{{ route('profile.my-requests') }}" class="px-5 py-2.5 bg-white text-blue-900 hover:bg-blue-50 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 shadow-md">View My Dashboard</a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Sponsor/Partners logo slider -->
    @if(!$partners->isEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-8 pb-16 reveal-on-scroll">
        <div class="text-center mb-6">
            <span class="text-[9px] font-black tracking-widest text-slate-400 uppercase font-display block">Sponsors & Partners</span>
            <h2 class="text-lg font-black tracking-tight text-slate-500 font-display uppercase mt-1">Our Community Partners</h2>
        </div>

        <div class="relative overflow-hidden w-full bg-slate-50 border border-slate-100 rounded-2xl py-6 px-4">
            <div class="flex items-center space-x-12 animate-marquee whitespace-nowrap min-w-full">
                @foreach($partners as $partner)
                    <div class="inline-block shrink-0 transition-opacity duration-300 hover:opacity-100 opacity-60">
                        @if($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" title="{{ $partner->name }}" class="block">
                                <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}" title="{{ $partner->name }}">
                        @endif
                    </div>
                @endforeach

                <!-- Duplicate for seamless scroll -->
                @foreach($partners as $partner)
                    <div class="inline-block shrink-0 transition-opacity duration-300 hover:opacity-100 opacity-60">
                        @if($partner->website_url)
                            <a href="{{ $partner->website_url }}" target="_blank" title="{{ $partner->name }}" class="block">
                                <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $partner->logo_path) }}" class="h-12 w-auto max-w-[150px] object-contain" alt="{{ $partner->name }}" title="{{ $partner->name }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <style>
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 25s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 700ms ease, transform 700ms ease;
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15,
                rootMargin: '0px 0px -40px 0px'
            });

            document.querySelectorAll('.reveal-on-scroll').forEach((element) => observer.observe(element));
        });
    </script>

    <!-- Overlays / Modals for all forms -->
    <div x-show="activeForm"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>

        <!-- Backdrop shadow -->
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" @click="activeForm = null"></div>

        @php
            $timeOptions = [];
            for ($h = 8; $h <= 17; $h++) {
                $formattedH = str_pad($h, 2, '0', STR_PAD_LEFT);
                $timeOptions["$formattedH:00"] = "$formattedH:00";
                if ($h < 17) {
                    $timeOptions["$formattedH:30"] = "$formattedH:30";
                }
            }
            $genderOptions = [
                'Male' => 'Male',
                'Female' => 'Female',
                'Prefer not to say' => 'Prefer not to say'
            ];
            $sportOptions = [
                'Basketball' => 'Basketball',
                'Volleyball' => 'Volleyball',
                'Football' => 'Football',
                'Badminton' => 'Badminton',
                'Table Tennis' => 'Table Tennis',
                'Swimming' => 'Swimming',
                'Athletics' => 'Athletics',
                'Boxing' => 'Boxing',
                'Martial Arts' => 'Martial Arts',
                'Esports' => 'Esports',
                'Other' => 'Other'
            ];
        @endphp

        <!-- Modal Wrapper -->
        <div class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-8">

            <!-- Modal Box Container -->
            <div class="max-w-2xl w-full relative z-10 transition-all transform max-h-[90vh] flex flex-col overflow-y-auto"
                 @click.stop>

                <!-- 1. HEALTH CONSULTATION FORM -->
                <div x-show="activeForm === 'health'" class="w-full relative">
                    <button type="button" @click="activeForm = null"
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card
                        title="Health Consultation"
                        subtitle="Apply for free medical guidance or health services from SK Namayan representatives."
                        action="{{ route('forms.health.store') }}"
                        enctype="multipart/form-data"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="First Name" name="first_name" required="true" value="{{ mb_strtoupper($kkProfile?->first_name, 'UTF-8') }}" />
                            <x-form-input label="Last Name" name="last_name" required="true" value="{{ mb_strtoupper($kkProfile?->surname, 'UTF-8') }}" />
                            <x-form-input label="Middle Name (type 'NONE' or 'N/A' if none)" name="middle_name" required="true" value="{{ mb_strtoupper($kkProfile?->middle_name, 'UTF-8') }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Age" name="age" type="number" min="0" max="120" required="true" value="{{ $kkProfile?->age }}" />
                            <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" selected="{{ $kkProfile?->gender ?? $kkProfile?->sex }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $kkProfile?->email ?? auth()->user()?->email }}" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $kkProfile?->contact_number }}" />
                        </div>

                        <x-form-input label="Concerns" name="concerns" type="textarea" required="true" placeholder="Detail your symptoms, advice needed, or other medical inquiries..." />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
                            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
                        </div>

                        @php $healthInit = $initiatives['forms.health.create'] ?? null; @endphp
                        @if($healthInit && is_array($healthInit->custom_fields) && count($healthInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($healthInit->custom_fields as $field)
                                        <x-form-input
                                            label="{{ $field['label'] }}"
                                            name="custom_fields[{{ $field['name'] }}]"
                                            type="{{ $field['type'] ?? 'text' }}"
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Health Consultation Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 2. MENTAL HEALTH SUPPORT FORM -->
                <div x-show="activeForm === 'mental-health'" class="w-full relative">
                    <button type="button" @click="activeForm = null"
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card
                        title="Mental Health Support Portal"
                        subtitle="Confidential counseling and mental wellness assistance for SK Namayan youth."
                        action="{{ route('forms.mental-health.store') }}"
                        enctype="multipart/form-data"
                    >
                        <!-- Confidentiality Banner -->
                        <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-xl text-blue-800 text-xs flex items-start space-x-2.5 mb-2 leading-relaxed">
                            <span class="text-base select-none">🔒</span>
                            <div>
                                <span class="font-bold">Confidentiality Guarantee:</span> All details shared in this request are strictly private and will only be accessible by the designated health support team under professional code.
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="First Name" name="first_name" required="true" value="{{ mb_strtoupper($kkProfile?->first_name, 'UTF-8') }}" />
                            <x-form-input label="Last Name" name="last_name" required="true" value="{{ mb_strtoupper($kkProfile?->surname, 'UTF-8') }}" />
                            <x-form-input label="Middle Name (type 'NONE' or 'N/A' if none)" name="middle_name" required="true" value="{{ mb_strtoupper($kkProfile?->middle_name, 'UTF-8') }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Age" name="age" type="number" min="0" max="120" required="true" value="{{ $kkProfile?->age }}" />
                            <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" selected="{{ $kkProfile?->gender ?? $kkProfile?->sex }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $kkProfile?->email ?? auth()->user()?->email }}" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $kkProfile?->contact_number }}" />
                        </div>

                        <x-form-input label="Describe what you are going through (Your mental wellness concerns)" name="concerns" type="textarea" required="true" placeholder="Please feel free to express your mental health queries or challenges..." />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" />
                            <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" />
                        </div>

                        @php $mentalInit = $initiatives['forms.mental-health.create'] ?? null; @endphp
                        @if($mentalInit && is_array($mentalInit->custom_fields) && count($mentalInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($mentalInit->custom_fields as $field)
                                        <x-form-input
                                            label="{{ $field['label'] }}"
                                            name="custom_fields[{{ $field['name'] }}]"
                                            type="{{ $field['type'] ?? 'text' }}"
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Confidential Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 3. PABILI MEDICINE SERVICES FORM -->
                <div x-show="activeForm === 'medicine'" class="w-full relative">
                    <button type="button" @click="activeForm = null"
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card
                        title="Pabili Medicine Services"
                        subtitle="Request essential medicine purchasing support and delivery services to your home."
                        action="{{ route('forms.medicine.store') }}"
                        enctype="multipart/form-data"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" value="{{ mb_strtoupper($kkProfile?->first_name, 'UTF-8') }}" />
                            <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" value="{{ mb_strtoupper($kkProfile?->surname, 'UTF-8') }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" value="{{ $kkProfile?->age }}" />
                            <x-form-select label="Requestor Gender" name="requestor_gender" required="true" :options="$genderOptions" selected="{{ mb_strtoupper($kkProfile?->gender ?? $kkProfile?->sex, 'UTF-8') }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $kkProfile?->email ?? auth()->user()?->email }}" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $kkProfile?->contact_number }}" />
                        </div>

                        <x-form-input label="Complete Delivery Address" name="complete_address" type="textarea" required="true" placeholder="Enter house number, street, barangay, and landmark..." value="{{ mb_strtoupper($kkProfile?->street_address ? ($kkProfile->street_address . ', Purok ' . ($kkProfile->purok?->purok_name ?? '')) : '', 'UTF-8') }}" />

                        @php $medInit = $initiatives['forms.medicine.create'] ?? null; @endphp
                        @if($medInit && is_array($medInit->custom_fields) && count($medInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($medInit->custom_fields as $field)
                                        <x-form-input
                                            label="{{ $field['label'] }}"
                                            name="custom_fields[{{ $field['name'] }}]"
                                            type="{{ $field['type'] ?? 'text' }}"
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" class="btn-primary w-full">Submit Medicine Request</button>
                        </div>
                    </x-form-card>
                </div>

                <!-- 4. SILID KARUNUNGAN BOOKING FORM -->
                <div x-show="activeForm === 'silid'" class="w-full relative" x-cloak
                     x-data="{
                         preferredDate: '',
                         preferredTime: '',
                         loadingSlots: false,
                         bookedSlots: [],
                         async fetchSlots() {
                             if (!this.preferredDate) return;
                             this.loadingSlots = true;
                             try {
                                 const res = await fetch(`{{ route('api.silid.booked-slots') }}?date=${this.preferredDate}`);
                                 const data = await res.json();
                                 this.bookedSlots = data.booked_slots || [];
                                 if (this.bookedSlots.includes(this.preferredTime)) {
                                     this.preferredTime = '';
                                 }
                             } catch (e) {
                                 console.error('Failed to fetch booked slots:', e);
                             } finally {
                                 this.loadingSlots = false;
                             }
                         }
                     }">
                    <button type="button" @click="activeForm = null"
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <x-form-card
                        title="Silid Karunungan Booking"
                        subtitle="Book studying slots at local research library facilities with internet access."
                        action="{{ route('forms.silid.store') }}"
                        enctype="multipart/form-data"
                    >
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" value="{{ mb_strtoupper($kkProfile?->first_name, 'UTF-8') }}" />
                            <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" value="{{ mb_strtoupper($kkProfile?->surname, 'UTF-8') }}" />
                            <x-form-input label="Requestor Middle Name (type 'NONE' or 'N/A' if none)" name="requestor_middle_name" required="true" value="{{ mb_strtoupper($kkProfile?->middle_name, 'UTF-8') }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" value="{{ $kkProfile?->age }}" />
                            <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $kkProfile?->email ?? auth()->user()?->email }}" />
                            <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $kkProfile?->contact_number }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    Preferred Date <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" 
                                       name="preferred_date" 
                                       x-model="preferredDate" 
                                       @change="fetchSlots()" 
                                       min="{{ date('Y-m-d') }}" 
                                       required 
                                       class="w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm font-medium text-slate-800 dark:text-slate-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                                    Preferred Time Slot <span class="text-rose-500">*</span>
                                </label>
                                <select name="preferred_time" 
                                        x-model="preferredTime" 
                                        :disabled="!preferredDate || loadingSlots" 
                                        required 
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-2.5 text-sm font-medium text-slate-800 dark:text-slate-100 disabled:opacity-50 transition">
                                    <option value="">-- Choose Time Slot --</option>
                                    @foreach($timeOptions as $val => $label)
                                        <option value="{{ $val }}" 
                                                :disabled="bookedSlots.includes('{{ $val }}')"
                                                x-text="'{{ $label }}' + (bookedSlots.includes('{{ $val }}') ? ' ❌ (Fully Booked)' : '')">
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <p x-show="loadingSlots" class="text-[10px] text-blue-600 dark:text-blue-400 font-bold mt-1" x-cloak>
                                    Checking slot availability...
                                </p>
                            </div>
                        </div>

                        <div x-show="preferredTime && bookedSlots.includes(preferredTime)" class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold flex items-center gap-2 mt-2" x-cloak>
                            <span>⚠️ The selected time slot is already booked. Please choose an available slot.</span>
                        </div>

                        @php $silidInit = $initiatives['forms.silid.create'] ?? null; @endphp
                        @if($silidInit && is_array($silidInit->custom_fields) && count($silidInit->custom_fields) > 0)
                            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    @foreach($silidInit->custom_fields as $field)
                                        <x-form-input
                                            label="{{ $field['label'] }}"
                                            name="custom_fields[{{ $field['name'] }}]"
                                            type="{{ $field['type'] ?? 'text' }}"
                                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="pt-4">
                            <button type="submit" 
                                    :disabled="loadingSlots || (preferredTime && bookedSlots.includes(preferredTime))" 
                                    class="btn-primary w-full disabled:opacity-50 disabled:cursor-not-allowed">
                                Submit Booking Request
                            </button>
                        </div>
                    </x-form-card>
                </div>


                <!-- 6. CUSTOM DYNAMIC FORM MODAL -->
                <div x-show="activeForm === 'custom'" class="w-full relative" x-cloak>
                    <button type="button" @click="activeForm = null"
                            class="absolute right-4 top-4 text-white hover:text-slate-200 bg-white/10 hover:bg-white/20 p-2 rounded-full transition z-20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    <div class="card max-w-2xl mx-auto overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-700 to-blue-900 -mx-6 -mt-6 md:-mx-8 md:-mt-8 px-6 md:px-8 py-5 text-white mb-6">
                            <h2 class="text-lg font-bold tracking-tight text-white font-display uppercase" x-text="customInitiative?.title"></h2>
                            <p class="text-xs text-blue-200 mt-1" x-text="customInitiative?.description"></p>
                        </div>

                        <form method="POST" :action="'/forms/initiative/' + customInitiative?.id" class="space-y-5" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-form-input label="First Name" name="first_name" :value="auth()->user()->first_name ?? ''" required="true" />
                                <x-form-input label="Last Name" name="last_name" :value="auth()->user()->last_name ?? ''" required="true" />
                            </div>
                            
                            <x-form-input label="Email Address" name="email" type="email" :value="auth()->user()->email ?? ''" required="true" />

                            <!-- Dynamic Fields Loop via Alpine.js template -->
                            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800" x-show="customInitiative?.custom_fields?.length > 0">
                                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                                <div class="grid grid-cols-1 gap-4">
                                    <template x-for="field in customInitiative?.custom_fields" :key="field.name">
                                        <div class="space-y-1.5">
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                <span x-text="field.label"></span>
                                                <span class="text-rose-500 font-extrabold" x-show="field.required">*</span>
                                            </label>
                                            
                                            <!-- Check if type is textarea -->
                                            <template x-if="field.type === 'textarea'">
                                                <textarea :name="'custom_fields[' + field.name + ']'"
                                                          :placeholder="field.placeholder"
                                                          :required="field.required"
                                                          rows="3"
                                                          class="field focus:ring-4 focus:ring-blue-600/10"></textarea>
                                            </template>

                                            <!-- Check if type is select -->
                                            <template x-if="field.type === 'select'">
                                                <select :name="'custom_fields[' + field.name + ']'"
                                                        :required="field.required"
                                                        class="field focus:ring-4 focus:ring-blue-600/10 py-3 px-4 bg-white">
                                                    <option value="" x-text="field.placeholder || 'Select an option'"></option>
                                                    <template x-for="opt in field.options" :key="opt">
                                                        <option :value="opt" x-text="opt"></option>
                                                    </template>
                                                </select>
                                            </template>

                                            <!-- Check if type is file -->
                                            <template x-if="field.type === 'file'">
                                                <input type="file"
                                                       :name="'custom_fields[' + field.name + ']'"
                                                       :required="field.required"
                                                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] hover:file:bg-blue-100 transition cursor-pointer">
                                            </template>

                                            <!-- Default text/number/date inputs -->
                                            <template x-if="['textarea', 'select', 'file'].indexOf(field.type) === -1">
                                                <input :type="field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text')"
                                                       :name="'custom_fields[' + field.name + ']'"
                                                       :placeholder="field.placeholder"
                                                       :required="field.required"
                                                       class="field focus:ring-4 focus:ring-blue-600/10">
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="btn-primary w-full">Submit Request</button>
                            </div>
                        </form>
                    </div>

            </div>
        </div>
    </div>

    <!-- Submission Success Confirmation Modal -->
    @if(session('submitted_success'))
    <div x-data="{ showSuccess: true }"
         x-init="showSuccess = true"
         x-show="showSuccess"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-cloak>

         <!-- Backdrop shadow -->
         <div class="fixed inset-0 bg-slate-950/45 backdrop-blur-sm transition-opacity" @click="showSuccess = false"></div>

         <!-- Modal Wrapper -->
         <div class="flex min-h-screen items-center justify-center p-4">

             <!-- Modal Box -->
             <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 max-w-lg w-full relative z-10 p-6 sm:p-8 text-center space-y-6"
                  @click.stop>

                  <!-- Close Button -->
                  <button type="button" @click="showSuccess = false"
                          class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 p-2 rounded-full transition focus:outline-none">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                  </button>

                  <!-- Animated Success Checkmark -->
                  <div class="relative flex items-center justify-center w-20 h-20 mx-auto">
                      <span class="animate-ping absolute inline-flex h-16 w-16 rounded-full bg-emerald-400 opacity-20"></span>
                      <div class="relative rounded-full w-16 h-16 bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                      </div>
                  </div>

                  <div>
                      <h2 class="text-2xl font-black font-display text-slate-800 uppercase tracking-tight">Request Submitted!</h2>
                      <p class="text-xs text-slate-400 mt-2">Thank you! Your digital request has been successfully filed with the SK council.</p>
                  </div>

                  <!-- Reference card with dashed border -->
                  <div class="p-5 bg-blue-50/50 border-2 border-dashed border-blue-200 rounded-2xl max-w-sm mx-auto text-center space-y-1">
                      <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest block font-display">Reference Number</span>
                      <span class="text-xl font-mono font-black text-[#1e40af] select-all">{{ session('referenceNumber') }}</span>
                      <p class="text-[10px] text-slate-400 pt-1">Copy this code to track your status at any time.</p>
                  </div>

                  <!-- Details summary table -->
                  <div class="card p-0 overflow-hidden text-left border border-slate-100 text-xs">
                      <div class="bg-slate-50 border-b border-slate-100 px-5 py-2.5">
                          <span class="font-bold text-slate-700 font-display uppercase tracking-wider">Submission Summary</span>
                      </div>
                      <table class="w-full">
                          <tbody class="divide-y divide-slate-100 text-slate-600">
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400 w-1/3">Request Type</td>
                                  <td class="px-5 py-2.5 font-bold text-slate-800">{{ session('type') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Requestor Name</td>
                                  <td class="px-5 py-2.5 text-slate-800 font-medium">{{ session('name') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Email Address</td>
                                  <td class="px-5 py-2.5 text-slate-800 font-mono">{{ session('email') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Preferred Details</td>
                                  <td class="px-5 py-2.5 text-slate-800 font-medium">{{ session('detail') }}</td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Initial Status</td>
                                  <td class="px-5 py-2.5">
                                      <span class="badge-pending">Pending</span>
                                  </td>
                              </tr>
                              <tr>
                                  <td class="px-5 py-2.5 font-semibold text-slate-400">Date Submitted</td>
                                  <td class="px-5 py-2.5 text-slate-800">{{ session('date') }}</td>
                              </tr>
                          </tbody>
                      </table>
                  </div>

                  <!-- Email note -->
                  <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs text-emerald-800 max-w-md mx-auto leading-relaxed flex items-start space-x-3 shadow-sm text-left">
                      <span class="text-xl shrink-0">✉️</span>
                      <div>
                          <span class="font-bold block text-emerald-950 text-sm mb-0.5">Confirmation Email Sent!</span>
                          A receipt and confirmation details have been sent to <span class="font-semibold underline text-emerald-950 font-mono">{{ session('email') }}</span>. Please check your inbox (and spam folder) for updates.
                      </div>
                  </div>

                  <!-- Action buttons -->
                  <div class="flex items-center justify-center gap-3 pt-2">
                      <a href="{{ route('track.index') }}?email={{ urlencode(session('email')) }}" class="btn-primary">Track Request</a>
                      <button type="button" @click="showSuccess = false" class="btn-outline">Close</button>
                  </div>

             </div>
         </div>
    </div>

    <!-- Hidden debug dump for session verification -->
    <div id="session-debug-dump" style="display: none;" 
         data-success="true" 
         data-ref="{{ session('referenceNumber') }}" 
         data-name="{{ session('name') }}" 
         data-email="{{ session('email') }}" 
         data-type="{{ session('type') }}">
         Session Debug: {{ json_encode(session()->all()) }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('SK Success Trigger: Session data detected client-side.');
            // Fail-safe: Force open Alpine model showSuccess if it is registered
            setTimeout(function() {
                const modalEl = document.querySelector('[x-data*="showSuccess"]');
                if (modalEl && modalEl.__x) {
                    console.log('SK Success Trigger: Auto-initializing Alpine showSuccess modal to true.');
                    modalEl.__x.$data.showSuccess = true;
                }
            }, 100);
        });
    </script>
    @endif

</div>
@endsection
