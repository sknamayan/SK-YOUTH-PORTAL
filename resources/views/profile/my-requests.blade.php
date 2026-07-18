@extends('layouts.app')

@section('content')
<div class="flex-1 bg-slate-50 dark:bg-slate-955 font-sans min-h-screen">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">My Requests</span>
            </nav>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="max-w-2xl space-y-2.5">
                    <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[9px] font-black uppercase tracking-widest font-display">Overview</span>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">My Submitted Requests</h1>
                    <p class="text-sm text-slate-300 leading-relaxed">Review and monitor the status of requests submitted under your email address ({{ auth()->user()->email }}).</p>
                </div>
                <a href="/" class="inline-flex items-center justify-center min-h-11 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all text-white shrink-0 self-start sm:self-center">
                    New Request
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8 md:py-10 space-y-6 animate-fade-in-up">

        <!-- Horizontal Citizen Sub-navigation -->
        @include('profile.partials.citizen-nav')

        <!-- Session Notifications -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-250 text-emerald-805 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
                <svg class="w-4 h-4 text-emerald-650 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-250 text-rose-805 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
                <svg class="w-4 h-4 text-rose-650 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 bg-blue-50 border border-blue-250 text-blue-855 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
                <svg class="w-4 h-4 text-blue-650 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Profiling Notice Banner -->
        @if(!$profile)
            <div class="p-5 bg-rose-50 border border-rose-250 rounded-2xl flex flex-col gap-4 text-rose-850 shadow-sm animate-pulse-subtle">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-605 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div class="flex-1 space-y-0.5">
                            <strong class="font-bold block uppercase tracking-wide text-[10px] text-rose-900">Profile Registry Pending (0% Complete)</strong>
                            <p class="text-xs leading-relaxed">You have not registered your Katipunan ng Kabataan profiling form in this system. <strong>All services and SIKLAB registrations are currently locked.</strong> Please complete it to unlock these options.</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.profiling.create') }}" class="btn-primary text-xs font-black uppercase py-2.5 px-5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl active:scale-95 transition shadow-sm shrink-0 text-center">
                        Complete Self Profiling
                    </a>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[9px] font-bold text-rose-800 uppercase tracking-wider">
                        <span>Completeness Status</span>
                        <span>0% Complete</span>
                    </div>
                    <div class="w-full bg-rose-100 border border-rose-200/50 h-3 rounded-full overflow-hidden">
                        <div class="bg-rose-450 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        @elseif($profile->status === 'pending')
            <div class="p-5 bg-amber-50 border border-amber-250 rounded-2xl flex flex-col gap-4 text-amber-850 shadow-sm animate-pulse-subtle">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-605 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="flex-1 space-y-0.5">
                        <strong class="font-bold block uppercase tracking-wide text-[10px] text-amber-900">Awaiting Admin Review (50% Complete)</strong>
                        <p class="text-xs">Your self-profiling has been submitted and is currently pending review by our desk officers. All services and registrations will unlock once approved.</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[9px] font-bold text-amber-855 uppercase tracking-wider">
                        <span>Completeness Status</span>
                        <span>50% Complete (Pending Approval)</span>
                    </div>
                    <div class="w-full bg-amber-100 border border-amber-200/50 h-3 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full transition-all duration-300" style="width: 50%"></div>
                    </div>
                </div>
                <!-- Stepper progress bar -->
                <div class="pt-4 border-t border-amber-200/30">
                    <div class="w-full max-w-xl mx-auto py-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center relative text-amber-700">
                                <div class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-amber-700">Submitted</span>
                            </div>
                            <div class="flex-1 border-t-2 mx-2 border-slate-200"></div>
     
                            <!-- Step 2 -->
                            <div class="flex flex-col items-center relative text-slate-405">
                                <div class="w-7 h-7 rounded-full border-2 border-slate-200 bg-white text-slate-400 flex items-center justify-center font-bold text-[10px]">2</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-slate-400">Under Review</span>
                            </div>
                            <div class="flex-1 border-t-2 mx-2 border-slate-200"></div>
     
                            <!-- Step 3 -->
                            <div class="flex flex-col items-center relative text-slate-405">
                                <div class="w-7 h-7 rounded-full border-2 border-slate-200 bg-white text-slate-400 flex items-center justify-center font-bold text-[10px]">3</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-slate-400">Verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($profile->status === 'declined')
            <div class="p-5 bg-rose-50 border border-rose-250 rounded-2xl flex flex-col gap-4 text-rose-850 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-605 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="flex-1 space-y-0.5">
                            <strong class="font-bold block uppercase tracking-wide text-[10px] text-rose-900">Self-Profiling Declined (0% Complete)</strong>
                            <p class="text-xs">Your self-profiling registry has been declined by the admin/staff. Please review your details and re-submit.</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.profiling.create') }}" class="btn-primary text-xs font-black uppercase py-2.5 px-5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl active:scale-95 transition shadow-sm shrink-0 text-center">
                        Re-submit Profiling
                    </a>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[9px] font-bold text-rose-800 uppercase tracking-wider">
                        <span>Completeness Status</span>
                        <span>0% Complete (Declined)</span>
                    </div>
                    <div class="w-full bg-rose-100 border border-rose-200/50 h-3 rounded-full overflow-hidden">
                        <div class="bg-rose-450 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                <!-- Stepper progress bar -->
                <div class="pt-4 border-t border-rose-200/30">
                    <div class="w-full max-w-xl mx-auto py-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center relative text-rose-750">
                                <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-rose-700">Submitted</span>
                            </div>
                            <div class="flex-1 border-t-2 mx-2 border-rose-600"></div>
     
                            <!-- Step 2 -->
                            <div class="flex flex-col items-center relative text-rose-750">
                                <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">2</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-rose-700">Under Review</span>
                            </div>
                            <div class="flex-1 border-t-2 mx-2 border-rose-600"></div>
     
                            <!-- Step 3 -->
                            <div class="flex flex-col items-center relative text-rose-750">
                                <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">3</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-rose-700">Declined</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($profile->status === 'approved')
            <div class="p-5 bg-emerald-50 border border-emerald-250 rounded-2xl flex flex-col gap-4 text-emerald-850 shadow-sm animate-fade-in">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-605 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"></path></svg>
                    <div class="flex-1 space-y-0.5">
                        <strong class="font-bold block uppercase tracking-wide text-[10px] text-emerald-900 font-display">KK Profile Verified (100% Complete)</strong>
                        <p class="text-xs leading-relaxed">Your Katipunan ng Kabataan profile registry is active. You can now request services and register for SIKLAB.</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[9px] font-bold text-emerald-800 uppercase tracking-wider font-mono">
                        <span>Completeness Status</span>
                        <span>100% Complete</span>
                    </div>
                    <div class="w-full bg-emerald-100 border border-emerald-200/50 h-3 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: 100%"></div>
                    </div>
                </div>
                <!-- Stepper progress bar -->
                <div class="pt-4 border-t border-emerald-200/30">
                    <div class="w-full max-w-xl mx-auto py-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                            <!-- Step 1 -->
                            <div class="flex flex-col items-center relative text-emerald-700">
                                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-emerald-700 font-display">Submitted</span>
                            </div>
                            <div class="flex-1 border-t-2 mx-2 border-emerald-600"></div>
     
                            <!-- Step 2 -->
                            <div class="flex flex-col items-center relative text-emerald-700">
                                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">2</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-emerald-700 font-display">Under Review</span>
                            </div>
                            <div class="flex-1 border-t-2 mx-2 border-emerald-600"></div>
     
                            <!-- Step 3 -->
                            <div class="flex flex-col items-center relative text-emerald-700">
                                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">3</div>
                                <span class="mt-1 text-[9px] uppercase font-bold tracking-wider text-emerald-700 font-display">Verified</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Analytics Stats Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Filed -->
            <div class="card interactive-card p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm border-l-4 border-l-blue-500">
                <div>
                    <span class="block text-2xl font-black text-slate-855 dark:text-slate-100 font-display">{{ $total }}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Filed</span>
                </div>
                <div class="w-8.5 h-8.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            
            <!-- Pending -->
            <div class="card interactive-card p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm border-l-4 border-l-amber-500">
                <div>
                    <span class="block text-2xl font-black text-slate-855 dark:text-slate-100 font-display">{{ $pending }}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pending Review</span>
                </div>
                <div class="w-8.5 h-8.5 rounded-xl bg-amber-50 dark:bg-amber-955/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            
            <!-- Approved -->
            <div class="card interactive-card p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm border-l-4 border-l-emerald-500">
                <div>
                    <span class="block text-2xl font-black text-slate-855 dark:text-slate-100 font-display">{{ $approved }}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Approved / Active</span>
                </div>
                <div class="w-8.5 h-8.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-605 dark:text-emerald-450 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            
            <!-- Declined -->
            <div class="card interactive-card p-5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm border-l-4 border-l-rose-500">
                <div>
                    <span class="block text-2xl font-black text-slate-855 dark:text-slate-100 font-display">{{ $declined }}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Declined</span>
                </div>
                <div class="w-8.5 h-8.5 rounded-xl bg-rose-50 dark:bg-rose-955/30 text-rose-600 dark:text-rose-450 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Requests list table -->
        <div class="card p-0 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl overflow-hidden shadow-sm">
            @if($results->isEmpty())
                <div class="text-center py-16 px-4 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-705 dark:text-slate-300 uppercase tracking-wider">No requests submitted yet</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">You haven't submitted any service requests under your email address.</p>
                    </div>
                    <a href="/" class="btn-primary inline-flex items-center min-h-10 px-5 text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-sm">Create First Request</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/75 dark:bg-slate-955/40 border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">Reference No</th>
                                <th class="py-4 px-6">Service Type</th>
                                <th class="py-4 px-6">Details</th>
                                <th class="py-4 px-6">Filed Date</th>
                                <th class="py-4 px-6 text-center">Status</th>
                                <th class="py-4 px-6">Processed By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-slate-655 dark:text-slate-300">
                            @foreach($results as $req)
                                @php
                                    $referenceNumber = 'SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT);
                                    
                                    $badgeClass = match($req->status) {
                                        'approved' => 'badge-approved',
                                        'declined' => 'badge-declined',
                                        'review' => 'badge-review',
                                        default => 'badge-pending'
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition">
                                    <td class="py-4 px-6 font-mono font-bold text-slate-800 dark:text-slate-200">{{ $referenceNumber }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-400 rounded-full text-[9px] font-extrabold uppercase tracking-wide">{{ $req->type_label }}</span>
                                    </td>
                                    <td class="py-4 px-6 font-medium text-slate-700 dark:text-slate-300 max-w-sm" title="{{ $req->detail }}">
                                        <div class="line-clamp-2 leading-relaxed">{{ $req->detail }}</div>
                                        @if(!empty($req->custom_fields) && is_array($req->custom_fields))
                                            <div class="flex flex-wrap gap-x-2 text-[9px] text-slate-400 dark:text-slate-500 mt-1">
                                                @foreach($req->custom_fields as $key => $val)
                                                    <span>{{ ucwords(str_replace('_', ' ', $key)) }}: <strong class="text-slate-500 dark:text-slate-400 font-semibold">{{ $val }}</strong></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-slate-400 dark:text-slate-550 font-medium">{{ $req->created_at->format('M d, Y') }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="{{ $badgeClass }}">{{ ucfirst($req->status) }}</span>
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-slate-700 dark:text-slate-300">
                                        @if(in_array($req->status, ['approved', 'declined']))
                                            {{ $req->processedBy ? $req->processedBy->name : 'Desk Officer' }}
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 font-medium">Pending Review</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if(!$results->isEmpty() && method_exists($results, 'links'))
            <div class="pt-4">
                {{ $results->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
