@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-8 py-12 md:py-16 flex-1">
    
    <div class="text-center mb-10">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display block">Citizens Area</span>
        <h1 class="text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Track Your Requests</h1>
        <p class="text-xs text-slate-400 mt-2 max-w-md mx-auto">Enter the email address or request reference number (e.g., SK-HEA-00033) to view historical and active request statuses.</p>
    </div>

    <!-- Search Input Card -->
    <div class="card mb-8">
        <form method="POST" action="{{ route('track.search') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="flex-1">
                <input 
                    type="text" 
                    name="email" 
                    value="{{ old('email', $email) }}" 
                    placeholder="Enter email address or reference number..." 
                    required 
                    class="field focus:ring-4 focus:ring-blue-600/10"
                >
                @error('email')
                    <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-primary shrink-0 px-6">Search Requests</button>
        </form>
    </div>

    <!-- Results list -->
    @if($searched)
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest font-display mb-2">Search Results for: <span class="text-slate-700 font-mono">{{ $email }}</span></h2>
            
            @if($results->isEmpty())
                <!-- Empty State -->
                <div class="card text-center py-12 space-y-4 border-2 border-dashed border-slate-200">
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">No requests found</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">We couldn't find any filed submissions matching the query <span class="font-mono text-slate-600">{{ $email }}</span>.</p>
                    </div>
                    <div class="pt-2">
                        <a href="/" class="btn-primary text-xs">File a New Request</a>
                    </div>
                </div>
            @else
                <!-- Result Cards -->
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

                    <div class="card hover:border-blue-200 hover:shadow-md transition-all duration-200 p-5 md:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start">
                                <!-- Meta -->
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[9px] font-extrabold uppercase tracking-wide font-display">{{ $req->type_label }}</span>
                                        <span class="text-xs font-mono font-bold text-slate-400">{{ $referenceNumber }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 font-display">{{ $req->title }}</h3>
                                    <p class="text-xs text-slate-500 leading-normal">{{ $req->summary }}</p>
                                    @if(!empty($req->custom_fields) && is_array($req->custom_fields))
                                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-semibold text-slate-400 mt-1">
                                            @foreach($req->custom_fields as $key => $val)
                                                <span>{{ ucwords(str_replace('_', ' ', $key)) }}: <strong class="text-slate-600 font-bold">{{ $val }}</strong></span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status and Date -->
                            <div class="sm:text-right flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                                <span class="{{ $badgeClass }}">{{ ucfirst($req->status) }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-1.5 block">Filed: {{ $req->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        @if(!empty($req->submitted_data))
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-3">Submitted Details</h4>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                                    @foreach($req->submitted_data as $label => $value)
                                        @if($value !== null && $value !== '')
                                            <div class="flex flex-col sm:flex-row sm:justify-between py-1 border-b border-slate-100/50 dark:border-slate-700/30 last:border-0">
                                                <dt class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ $label }}</dt>
                                                <dd class="text-xs font-semibold text-slate-700 dark:text-slate-200 mt-0.5 sm:mt-0 sm:text-right truncate max-w-full" title="{{ $value }}">{{ $value }}</dd>
                                            </div>
                                        @endif
                                    @endforeach
                                </dl>
                            </div>
                        @endif

                        <!-- Stepper progress bar -->
                        <div class="pt-4 border-t border-slate-100">
                            <!-- Alpine.js stepper context -->
                            <div x-data="{ status: '{{ $req->status }}' }" class="w-full max-w-xl mx-auto py-2">
                                <div class="flex items-center justify-between text-xs font-semibold text-slate-400 select-none">
                                    <!-- Step 1 -->
                                    <div class="flex flex-col items-center relative text-blue-600">
                                        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-[10px]">1</div>
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider text-slate-600">Filed</span>
                                    </div>
                                    <div class="flex-1 border-t-2 mx-2 transition duration-500" :class="status !== 'pending' ? 'border-blue-600' : 'border-slate-200'"></div>

                                    <!-- Step 2 -->
                                    <div class="flex flex-col items-center relative" :class="status !== 'pending' ? 'text-blue-600' : ''">
                                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px]"
                                             :class="status !== 'pending' ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-400'">2</div>
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider" :class="status !== 'pending' ? 'text-slate-600' : 'text-slate-400'">Review</span>
                                    </div>
                                    <div class="flex-1 border-t-2 mx-2 transition duration-500" :class="status === 'approved' || status === 'declined' ? 'border-blue-600' : 'border-slate-200'"></div>

                                    <!-- Step 3 -->
                                    <div class="flex flex-col items-center relative" :class="status === 'approved' || status === 'declined' ? (status === 'approved' ? 'text-emerald-600' : 'text-rose-600') : ''">
                                        <div class="w-7 h-7 rounded-full border-2 flex items-center justify-center font-bold text-[10px]"
                                             :class="status === 'approved' ? 'border-emerald-500 bg-emerald-500 text-white' : (status === 'declined' ? 'border-rose-500 bg-rose-500 text-white' : 'border-slate-200 bg-white text-slate-400')">3</div>
                                        <span class="mt-1 text-[10px] uppercase font-bold tracking-wider" :class="status === 'approved' ? 'text-emerald-600' : (status === 'declined' ? 'text-rose-600' : 'text-slate-400')" x-text="status === 'declined' ? 'Declined' : 'Resolved'">Resolved</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit / Cancel buttons for Requesters on pending items -->
                        @if($req->status === 'pending')
                            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                                <a href="{{ route('track.edit', [$req->type_slug, $req->id]) }}" class="px-3.5 py-1.5 border border-slate-200 text-slate-600 hover:text-[#1e40af] hover:border-[#1e40af] font-bold rounded-xl transition text-[10px] uppercase tracking-wider active:scale-95 flex items-center space-x-1">
                                    <x-category-icon name="profile" class="w-3.5 h-3.5" />
                                    <span>Edit Details</span>
                                </a>
                                <x-alert-dialog>
                                    <x-slot:trigger>
                                        <button type="button" class="px-3.5 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-xl transition text-[10px] uppercase tracking-wider active:scale-95 flex items-center space-x-1">
                                            <x-category-icon name="peace-building" class="w-3.5 h-3.5" />
                                            <span>Cancel Request</span>
                                        </button>
                                    </x-slot:trigger>
                                    
                                     <x-slot:icon>
                                         <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                         </svg>
                                     </x-slot:icon>
                                    
                                    <x-slot:title>
                                        Cancel Request
                                    </x-slot:title>
                                    
                                    <x-slot:description>
                                        Are you sure you want to cancel and withdraw this pending request "{{ $req->title }}"? This action cannot be undone.
                                    </x-slot:description>
                                    
                                    <x-slot:footer>
                                        <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">
                                            Cancel
                                        </button>
                                        <form method="POST" action="{{ route('track.cancel', [$req->type_slug, $req->id]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm hover:shadow-md border border-transparent">
                                                Confirm Cancel
                                            </button>
                                        </form>
                                    </x-slot:footer>
                                </x-alert-dialog>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    @endif

</div>
@endsection
