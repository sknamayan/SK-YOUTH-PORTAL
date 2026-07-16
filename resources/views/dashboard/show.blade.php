@extends('layouts.app')

@section('content')
<div x-data="{}" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc]">

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
                    <a href="{{ route('dashboard.index') }}?tab={{ $type }}" class="text-slate-400 hover:text-[#1e40af]">{{ $typeName }}</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-800">Detail #{{ $req->id }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left 2 Cols: Form Labeled Grid -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="card space-y-6">
                        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Submitted Form Fields</span>
                                <h2 class="text-lg font-bold text-slate-800 font-display uppercase">Request Information</h2>
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
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider text-slate-600 font-display font-black">Filed</span>
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
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider font-display font-black" :class="status === 'approved' ? 'text-emerald-600' : (status === 'declined' ? 'text-rose-600' : 'text-slate-400')" x-text="status === 'declined' ? 'Declined' : 'Resolved'">Resolved</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                            
                            <!-- Render conditionally based on request model type -->
                            @if($basename === 'CustomRequest')
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">First Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->first_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Last Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->last_name }}</span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Email Address</span>
                                    <span class="text-slate-800 font-mono text-sm select-all">{{ $req->email }}</span>
                                </div>

                            @elseif($basename === 'HealthRequest')
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">First Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->first_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Last Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->last_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Middle Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->middle_name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Age</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->age }} yrs</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Gender</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->gender }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Email Address</span>
                                    <span class="text-slate-800 font-mono text-sm select-all">{{ $req->email }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Contact Number</span>
                                    <span class="text-slate-800 font-medium text-sm select-all">{{ $req->contact_number }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Preferred Date</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->preferred_date->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Preferred Time</span>
                                    <span class="text-[#1e40af] font-bold text-sm">{{ $req->preferred_time }}</span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Concerns / Medical Description</span>
                                    <div class="p-3 bg-slate-50 rounded-xl text-slate-700 leading-relaxed text-[11px] whitespace-pre-line">{{ $req->concerns }}</div>
                                </div>

                            @elseif($basename === 'MedicineRequest')
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor First Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_first_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor Last Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_last_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor Age</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_age }} yrs</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor Gender</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_gender }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Email Address</span>
                                    <span class="text-slate-800 font-mono text-sm select-all">{{ $req->email }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Contact Number</span>
                                    <span class="text-slate-800 font-medium text-sm select-all">{{ $req->contact_number }}</span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Complete Delivery Address</span>
                                    <div class="p-3 bg-slate-50 rounded-xl text-slate-700 leading-relaxed text-[11px] whitespace-pre-line">{{ $req->complete_address }}</div>
                                </div>

                            @elseif($basename === 'SilidKarununganRequest')
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor First Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_first_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor Last Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_last_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor Middle Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_middle_name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Requestor Age</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->requestor_age }} yrs</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Email Address</span>
                                    <span class="text-slate-800 font-mono text-sm select-all">{{ $req->email }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Contact Number</span>
                                    <span class="text-slate-800 font-medium text-sm select-all">{{ $req->contact_number }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Preferred Date</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->preferred_date->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Preferred Time</span>
                                    <span class="text-[#1e40af] font-bold text-sm">{{ $req->preferred_time }}</span>
                                </div>

                            @elseif($basename === 'SportsRegistration')
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Participant First Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->first_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Participant Last Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->last_name }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Middle Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->middle_name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Age</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->age }} yrs</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Gender</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->gender }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Email Address</span>
                                    <span class="text-slate-800 font-mono text-sm select-all">{{ $req->email }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Contact Number</span>
                                    <span class="text-slate-800 font-medium text-sm select-all">{{ $req->contact_number }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Tournament Sport</span>
                                    <span class="text-slate-800 font-bold text-sm uppercase">{{ $req->sport }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Team Name</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->team_name ?? 'Individual' }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Preferred Event Date</span>
                                    <span class="text-slate-800 font-medium text-sm">{{ $req->event_date->format('M d, Y') }}</span>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="block text-slate-400 font-semibold uppercase tracking-wider mb-1">Remarks / Participant Notes</span>
                                    <div class="p-3 bg-slate-50 rounded-xl text-slate-700 leading-relaxed text-[11px] whitespace-pre-line">{{ $req->remarks ?? 'No remarks provided.' }}</div>
                                </div>
                            @endif

                            @if(!empty($req->custom_fields) && is_array($req->custom_fields))
                                <div class="sm:col-span-2 border-t border-slate-100 pt-4 mt-2">
                                    <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display mb-3">Custom Field Answers</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach($req->custom_fields as $key => $val)
                                            <div class="p-3 bg-slate-50/50 border border-slate-100 rounded-xl">
                                                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                                <span class="text-slate-800 font-bold text-xs mt-1 block select-all">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>

                        <!-- Status actions triggers -->
                        <div class="border-t border-slate-100 pt-5 flex flex-wrap gap-3">
                            @if($req->status !== 'approved')
                                <form method="POST" action="{{ route('dashboard.requests.status', [$type, $req->id, 'approved']) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-success">Approve Request</button>
                                </form>
                            @endif

                            @if($req->status !== 'declined')
                                <form method="POST" action="{{ route('dashboard.requests.status', [$type, $req->id, 'declined']) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-danger">Decline Request</button>
                                </form>
                            @endif

                            @if($req->status !== 'pending')
                                <form method="POST" action="{{ route('dashboard.requests.status', [$type, $req->id, 'pending']) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-secondary">Reset to Pending</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Activity log feed -->
                <div class="space-y-6">
                    @include('dashboard.partials.comment-thread', ['comments' => $comments])

                    <div class="card space-y-4">
                        <div>
                            <span class="text-[9px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Historical Timeline</span>
                            <h2 class="text-sm font-bold text-slate-800 font-display uppercase">Activity Logs</h2>
                        </div>
                        <hr class="border-slate-100">
                        @include('dashboard.partials.activity-log')
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection
