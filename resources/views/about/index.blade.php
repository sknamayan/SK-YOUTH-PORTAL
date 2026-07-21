@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-8 py-12 md:py-16">
    <div class="text-center mb-10">
        <img src="{{ asset('images/logo.png') }}" class="w-24 h-24 mx-auto object-contain rounded-full bg-white p-1 border shadow-md mb-4" alt="SK Logo">
        <h1 class="text-3xl font-black text-slate-800 font-display tracking-tight uppercase">About SK Namayan</h1>
        <p class="text-sm text-slate-400 mt-2 max-w-md mx-auto">Learn more about the governing youth council of Barangay Namayan, Mandaluyong City.</p>
    </div>

    <div class="card space-y-6 leading-relaxed">
        <div>
            <h2 class="text-lg font-bold text-[#1e40af] font-display uppercase mb-2">Our Mission</h2>
            <p class="text-slate-600">
                To provide a platform for the youth of Namayan to actively engage in community-building, driven by a deep commitment to service and an uncompromising pursuit of excellence. Through inclusive programs, leadership training, and service-driven initiatives, we inspire a culture of  innovation, and proactive action for the betterment of our community. 
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-[#1e40af] font-display uppercase mb-2">Our Vision</h2>
            <p class="text-slate-600">
                A barangay of empowered youth dedicated to the principles of excellence and service, striving to become proactive leaders and beacon of positive change who contribute meaningfully to the growth and development of Namayan.
            </p>
        </div>

        <hr class="border-slate-100">

        <div>
            <h2 class="text-lg font-bold text-[#1e40af] font-display uppercase mb-2">Our Key Service Areas</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-slate-600">
                <div class="p-3 bg-slate-50 rounded-xl flex items-start space-x-2.5">
                    <x-category-icon name="health" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold text-slate-700">Health & Well-being</span>
                        <p class="text-xs mt-1">Free physical health and mental health consultations, plus local community medicine support.</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl flex items-start space-x-2.5">
                    <x-category-icon name="education" class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold text-slate-700">Education (Silid Karunungan)</span>
                        <p class="text-xs mt-1">Providing safe, modern studying spaces with high-speed internet to support digital learners.</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl flex items-start space-x-2.5">
                    <x-category-icon name="sports" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold text-slate-700">Sports Development</span>
                        <p class="text-xs mt-1">Year-round SIKLAB league registrations to encourage active, healthy lifestyles.</p>
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl flex items-start space-x-2.5">
                    <x-category-icon name="active-citizenship" class="w-5 h-5 text-slate-500 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold text-slate-700">Livelihood & Civic Duty</span>
                        <p class="text-xs mt-1">Youth leadership seminars, active citizenship events, and environment tree planting programs.</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-slate-100">

        <div>
            <h2 class="text-lg font-bold text-[#1e40af] font-display uppercase mb-2"></h2>
            <p class="text-slate-600">
                You are welcome to visit our physical office from Monday to Friday, 8:00 AM to 5:00 PM:
            </p>
            <p class="text-xs text-slate-500 mt-2 font-mono">
                Barangay Namayan SK Office, Mandaluyong City, Metro Manila<br>
                Email: info@sknamayan.gov.ph<br>
                Phone: +63 (2) 8532 5001
            </p>
        </div>
    </div>
</div>
@endsection
