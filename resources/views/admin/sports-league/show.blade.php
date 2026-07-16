@extends('layouts.app')

@section('content')
<div x-data="{ showDeleteModal: false, showDeclineModal: false }" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

    <!-- Left Sidebar -->
    @include('layouts.dashboard-sidebar')

    <!-- Main Pane -->
    <div class="flex-1 flex flex-col min-w-0">

        <div class="p-6 md:p-8 space-y-8 flex-1 overflow-y-auto">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider">
                    <a href="{{ route('dashboard.index') }}" class="text-slate-400 hover:text-[#1e40af]">Dashboard</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('admin.sports-league.index') }}" class="text-slate-400 hover:text-[#1e40af]">SIKLAB</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Registration #{{ $req->id }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left 2 Cols: Form Labeled Grid -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="card space-y-6 bg-white border border-slate-100 p-6 rounded-3xl shadow-sm">
                        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display font-black">Submitted Registration Fields</span>
                                <h2 class="text-lg font-bold text-slate-800 font-display uppercase tracking-tight">Participant Information</h2>
                                @if(in_array($req->status, ['approved', 'declined']))
                                    <span class="text-[10px] text-slate-450 font-semibold block mt-1 uppercase tracking-wider">Processed By: <strong class="text-slate-700 font-bold">{{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}</strong></span>
                                @endif
                            </div>
                            <span class="badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                        </div>

                        <!-- Stepper progress bar -->
                        <div class="py-4 border-b border-slate-100">
                            <div x-data="{ status: '{{ $req->status }}' }" class="w-full max-w-xl mx-auto py-2">
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                                    <!-- Step 1 -->
                                    <div class="flex flex-col items-center relative text-blue-600">
                                        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider text-slate-600 font-display font-black">Registered</span>
                                    </div>
                                    <div class="flex-1 border-t-2 mx-2 transition duration-500" :class="status !== 'pending' ? 'border-blue-600' : 'border-slate-200'"></div>

                                    <!-- Step 2 -->
                                    <div class="flex flex-col items-center relative" :class="status !== 'pending' ? 'text-blue-600' : ''">
                                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px]"
                                             :class="status !== 'pending' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-400'">2</div>
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider font-display font-black" :class="status !== 'pending' ? 'text-slate-600' : 'text-slate-400'">Review</span>
                                    </div>
                                    <div class="flex-1 border-t-2 mx-2 transition duration-500" :class="status === 'approved' || status === 'declined' ? 'border-blue-600' : 'border-slate-200'"></div>

                                    <!-- Step 3 -->
                                    <div class="flex flex-col items-center relative" :class="status === 'approved' || status === 'declined' ? (status === 'approved' ? 'text-emerald-600' : 'text-rose-600') : ''">
                                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px]"
                                             :class="status === 'approved' ? 'border-emerald-500 bg-emerald-500 text-white' : (status === 'declined' ? 'border-rose-500 bg-rose-500 text-white' : 'border-slate-200 bg-white text-slate-400')">3</div>
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider font-display font-black" :class="status === 'approved' ? 'text-emerald-600' : (status === 'declined' ? 'text-rose-600' : 'text-slate-400')" x-text="status === 'declined' ? 'Declined' : 'Approved'">Approved</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">First Name</span>
                                <span class="text-slate-850 font-bold text-sm">{{ $req->first_name }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Last Name</span>
                                <span class="text-slate-855 font-bold text-sm">{{ $req->last_name }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Middle Name</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->middle_name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Age</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->age }} yrs</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Gender</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->gender }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Email Address</span>
                                <span class="text-slate-850 font-mono text-sm select-all">{{ $req->email }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Contact Number</span>
                                <span class="text-slate-850 font-medium text-sm select-all">{{ $req->contact_number }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Sport / League</span>
                                <span class="text-slate-850 font-bold text-sm">{{ $req->sport }}</span>
                            </div>
                            @if($req->division)
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Division</span>
                                <span class="text-[#1e40af] font-black text-sm uppercase tracking-wide">{{ $req->division }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Team Name</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->team_name ?? 'None (Individual)' }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Event Date</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->event_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Complete Address</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->address ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Registered in KK Profiling?</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->kk_profiling_status ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Consent Waiver Agreement</span>
                                <span class="text-slate-850 font-semibold text-sm">{{ $req->consent_waiver ? 'Agreed' : 'Not Agreed' }}</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Health Declaration Details</span>
                                <div class="p-3 bg-slate-50 rounded-xl text-slate-700 leading-relaxed text-[11px] whitespace-pre-line">{{ $req->health_declaration ?? 'None' }}</div>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Remarks / Team Configurations</span>
                                <div class="p-3 bg-slate-50 rounded-xl text-slate-700 leading-relaxed text-[11px] whitespace-pre-line">{{ $req->remarks ?? 'None' }}</div>
                            </div>

                            @if($req->age < 18)
                            <div class="sm:col-span-2 border-t border-slate-100 pt-4 mt-4 space-y-4">
                                <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest block font-display">Parent / Guardian Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Guardian Name</span>
                                        <span class="text-slate-850 font-bold text-sm">{{ $req->guardian_first_name }} {{ $req->guardian_middle_name }} {{ $req->guardian_last_name }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Age / Relationship</span>
                                        <span class="text-slate-850 font-semibold text-sm">{{ $req->guardian_age }} yrs / {{ $req->guardian_relation }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Guardian Contact</span>
                                        <span class="text-slate-850 font-medium text-sm select-all">{{ $req->guardian_contact_number }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Guardian Complete Address</span>
                                        <span class="text-slate-850 font-semibold text-sm">{{ $req->guardian_address }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="sm:col-span-2 border-t border-slate-100 pt-4 mt-4 space-y-4">
                                <h3 class="text-xs font-black text-[#1e40af] uppercase tracking-widest block font-display">Uploaded Verification Documents</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Profile Picture -->
                                    <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col justify-between space-y-3">
                                        <div>
                                            <span class="block text-[10px] font-black uppercase text-slate-400">Profile Picture</span>
                                            <span class="text-[9px] text-slate-405 block mt-0.5">2x2 passport photo</span>
                                        </div>
                                        @if($req->profile_picture)
                                            <div class="w-full aspect-video bg-slate-100 rounded-xl overflow-hidden mb-2">
                                                <img src="{{ Storage::url($req->profile_picture) }}" class="w-full h-full object-cover" alt="Profile Picture">
                                            </div>
                                            <a href="{{ Storage::url($req->profile_picture) }}" target="_blank" class="w-full py-1.5 text-center bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[10px] uppercase rounded-lg transition">View Image</a>
                                        @else
                                            <span class="text-xs text-slate-400 font-semibold italic">No file uploaded</span>
                                        @endif
                                    </div>

                                    <!-- Guardian ID or Voter Cert -->
                                    @if($req->age < 18)
                                        <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col justify-between space-y-3">
                                            <div>
                                                <span class="block text-[10px] font-black uppercase text-slate-400">Guardian Valid ID</span>
                                                <span class="text-[9px] text-slate-405 block mt-0.5">Government ID upload</span>
                                            </div>
                                            @if($req->guardian_gov_id)
                                                <div class="w-full aspect-video bg-slate-100 rounded-xl overflow-hidden mb-2 flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <a href="{{ Storage::url($req->guardian_gov_id) }}" target="_blank" class="w-full py-1.5 text-center bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[10px] uppercase rounded-lg transition">View ID</a>
                                            @else
                                                <span class="text-xs text-slate-400 font-semibold italic">No file uploaded</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="p-4 bg-slate-50 border rounded-2xl flex flex-col justify-between space-y-3">
                                            <div>
                                                <span class="block text-[10px] font-black uppercase text-slate-400">Voter Certificate</span>
                                                <span class="text-[9px] text-slate-405 block mt-0.5">Comelec / Voter's ID cert</span>
                                            </div>
                                            @if($req->voter_cert)
                                                <div class="w-full aspect-video bg-slate-100 rounded-xl overflow-hidden mb-2 flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <a href="{{ Storage::url($req->voter_cert) }}" target="_blank" class="w-full py-1.5 text-center bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-[10px] uppercase rounded-lg transition">View Cert</a>
                                            @else
                                                <span class="text-xs text-slate-400 font-semibold italic">No file uploaded</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Administrative Action Center -->
                <div class="space-y-6">
                    <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-4">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest border-b border-slate-100 pb-2">Action Desk</h3>

                        <form action="{{ route('admin.sports-league.status', [$req->id, 'approved']) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label for="action_remarks" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Officer remarks (Optional)</label>
                                <textarea name="remarks" id="action_remarks" rows="3" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:bg-white focus:border-[#1e40af] transition" placeholder="Add approval remarks or instructions..."></textarea>
                            </div>

                            @if($req->status !== 'approved')
                                <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 flex items-center justify-center space-x-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Approve Registration</span>
                                </button>
                            @endif
                        </form>

                        @if($req->status !== 'declined')
                            <button type="button" @click="showDeclineModal = true" class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 flex items-center justify-center space-x-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <span>Decline Registration</span>
                            </button>
                        @endif

                        <div class="border-t border-slate-100 pt-4 space-y-3">
                            <a href="{{ route('admin.sports-league.edit', $req->id) }}" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 flex items-center justify-center space-x-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span>Edit Record</span>
                            </a>
                            <button @click="showDeleteModal = true" class="w-full py-2.5 px-4 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-500 font-bold text-xs uppercase tracking-wider rounded-xl transition active:scale-95 flex items-center justify-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Delete Record</span>
                            </button>
                        </div>
                    </div>
                </div>

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

            <form action="{{ route('admin.sports-league.destroy', $req->id) }}" method="POST" class="space-y-4">
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

    <!-- Confirmation Modal for Declining Registration -->
    <div x-show="showDeclineModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" x-cloak>
        <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 space-y-4 border border-slate-100">
            <div class="flex items-center space-x-3 text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-sm font-black uppercase tracking-wider font-display">Decline Registration</h3>
            </div>
            
            <p class="text-slate-500 text-xs leading-relaxed">
                Are you sure you want to decline this sports registration? This will mark the registration as declined and notify the applicant.
            </p>

            <form action="{{ route('admin.sports-league.status', [$req->id, 'declined']) }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="flex items-center justify-end space-x-3">
                    <button type="button" @click="showDeclineModal = false" class="py-2 px-4 bg-slate-100 hover:bg-slate-250 text-slate-600 font-bold text-xs uppercase tracking-wider rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="py-2 px-4 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition">
                        Confirm Decline
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
