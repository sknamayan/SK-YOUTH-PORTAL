@extends('layouts.app')

@section('content')
<div x-data="{ age: {{ $req->age }} }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <!-- Main Pane -->
    <div class="flex-1 flex flex-col min-w-0">

        <div class="p-6 md:p-8 pb-24 md:pb-8 space-y-6 flex-1 overflow-y-auto">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('admin.sports-league.index') }}" class="text-slate-400 hover:text-[#1e40af]">SIKLAB</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('admin.sports-league.show', $req->id) }}" class="text-slate-400 hover:text-[#1e40af]">Registration #{{ $req->id }}</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Edit Details</span>
                </div>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-250 rounded-2xl flex items-start gap-3 shadow-sm">
                    <span class="text-rose-500 font-bold text-base">⚠</span>
                    <div>
                        <h4 class="text-xs font-bold text-rose-800 uppercase tracking-wide">Validation Issues Found</h4>
                        <ul class="text-xs text-rose-600 mt-1 list-disc list-inside font-semibold space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form id="edit-sports-registration-form" action="{{ route('admin.sports-league.update', $req->id) }}" method="POST" class="max-w-4xl space-y-6">
                @csrf
                @method('PUT')

                <!-- Card 1: Participant Information -->
                <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-6">
                    <div>
                        <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Participant Fields</span>
                        <h2 class="text-base font-black text-slate-800 font-display uppercase tracking-tight">Edit Participant Info</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">First Name</label>
                            <input type="text" name="first_name" id="first_name" required value="{{ old('first_name', $req->first_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label for="middle_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $req->middle_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Last Name</label>
                            <input type="text" name="last_name" id="last_name" required value="{{ old('last_name', $req->last_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Age -->
                        <div>
                            <label for="age" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Age</label>
                            <input type="number" name="age" id="age" required x-model="age" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</label>
                            <select name="gender" id="gender" required 
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                                <option value="Male" {{ old('gender', $req->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $req->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Prefer not to say" {{ old('gender', $req->gender) === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <label for="contact_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" required value="{{ old('contact_number', $req->contact_number) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</label>
                            <input type="email" name="email" id="email" required value="{{ old('email', $req->email) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- KK Profiling Status -->
                        <div>
                            <label for="kk_profiling_status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Registered in KK Profiling?</label>
                            <select name="kk_profiling_status" id="kk_profiling_status" required 
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                                <option value="Yes" {{ old('kk_profiling_status', $req->kk_profiling_status) === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('kk_profiling_status', $req->kk_profiling_status) === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-3">
                            <label for="address" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Complete Address</label>
                            <input type="text" name="address" id="address" required value="{{ old('address', $req->address) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tournament Selections -->
                <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-6">
                    <div>
                        <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display font-black">Tournament Fields</span>
                        <h2 class="text-base font-black text-slate-800 font-display uppercase tracking-tight">Edit Tournament Info</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Sport -->
                        <div>
                            <label for="sport" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sport / Game</label>
                            <select name="sport" id="sport" required 
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                                <option value="Basketball" {{ old('sport', $req->sport) === 'Basketball' ? 'selected' : '' }}>Basketball</option>
                                <option value="Volleyball" {{ old('sport', $req->sport) === 'Volleyball' ? 'selected' : '' }}>Volleyball</option>
                            </select>
                        </div>

                        <!-- Division -->
                        <div>
                            <label for="division" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Division</label>
                            <input type="text" name="division" id="division" required value="{{ old('division', $req->division) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800" placeholder="e.g. Midget, Juniors, Seniors, Mens, Womens">
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Position</label>
                            <input type="text" name="position" id="position" required value="{{ old('position', $req->position) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800" placeholder="e.g. Guard, Forward, Center, Libero, Setter">
                        </div>

                        <!-- Team Name -->
                        <div>
                            <label for="team_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Team Name (Optional)</label>
                            <input type="text" name="team_name" id="team_name" value="{{ old('team_name', $req->team_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Remarks -->
                        <div class="sm:col-span-2">
                            <label for="remarks" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Remarks / Configs</label>
                            <textarea name="remarks" id="remarks" rows="4" 
                                      class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">{{ old('remarks', $req->remarks) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Guardian Details (Only visible if age is under 18) -->
                <div x-show="age < 18" class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-6" x-cloak>
                    <div>
                        <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest block font-display">Guardian Fields</span>
                        <h2 class="text-base font-black text-slate-800 font-display uppercase tracking-tight">Edit Parent / Guardian Info</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <!-- Guardian First Name -->
                        <div>
                            <label for="guardian_first_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian First Name</label>
                            <input type="text" name="guardian_first_name" id="guardian_first_name" :required="age < 18" value="{{ old('guardian_first_name', $req->guardian_first_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Guardian Middle Name -->
                        <div>
                            <label for="guardian_middle_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Middle Name</label>
                            <input type="text" name="guardian_middle_name" id="guardian_middle_name" value="{{ old('guardian_middle_name', $req->guardian_middle_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Guardian Last Name -->
                        <div>
                            <label for="guardian_last_name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Last Name</label>
                            <input type="text" name="guardian_last_name" id="guardian_last_name" :required="age < 18" value="{{ old('guardian_last_name', $req->guardian_last_name) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Guardian Age -->
                        <div>
                            <label for="guardian_age" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Age</label>
                            <input type="number" name="guardian_age" id="guardian_age" :required="age < 18" value="{{ old('guardian_age', $req->guardian_age) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Relation -->
                        <div>
                            <label for="guardian_relation" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Relation to Participant</label>
                            <input type="text" name="guardian_relation" id="guardian_relation" :required="age < 18" value="{{ old('guardian_relation', $req->guardian_relation) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800" placeholder="e.g. Mother, Father, Uncle">
                        </div>

                        <!-- Contact -->
                        <div>
                            <label for="guardian_contact_number" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Contact</label>
                            <input type="text" name="guardian_contact_number" id="guardian_contact_number" :required="age < 18" value="{{ old('guardian_contact_number', $req->guardian_contact_number) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-3">
                            <label for="guardian_address" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian Complete Address</label>
                            <input type="text" name="guardian_address" id="guardian_address" :required="age < 18" value="{{ old('guardian_address', $req->guardian_address) }}" 
                                   class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition font-semibold text-slate-800">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 justify-end pt-4">
                    <a href="{{ route('admin.sports-league.show', $req->id) }}" 
                       class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#1e40af] hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition active:scale-95 shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-mobile-bottom-action form="edit-sports-registration-form" type="submit">
    Save Changes
</x-mobile-bottom-action>
@endsection
