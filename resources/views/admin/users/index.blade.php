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
                    <span class="text-slate-800">User Accounts</span>
                </div>
            </div>

            <!-- Page Title -->
            <div class="space-y-1">
                <h1 class="text-2xl font-black text-slate-800 font-display uppercase tracking-tight">Manage User Roles</h1>
                <p class="text-xs text-slate-400">Search registrants, promote roles, or delete inactive citizen accounts.</p>
            </div>
            <!-- Search & Filters Card -->
            <div class="card p-6 bg-white border border-slate-100 rounded-3xl shadow-sm">
                <form id="filterForm" method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                    <!-- Row 1: Search, Role, Year -->
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
                                placeholder="Search accounts by name or email address..." 
                                class="pl-10 pr-4 py-2.5 w-full bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans placeholder-slate-400"
                            >
                        </div>

                        <!-- Role Dropdown (Col span 3) -->
                        <div class="md:col-span-3 relative">
                            <select 
                                name="role" 
                                onchange="this.form.submit()"
                                class="block w-full py-2.5 pl-4 pr-10 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-xs text-slate-700 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none"
                            >
                                <option value="">All Roles</option>
                                <option value="user" {{ $roleFilter == 'user' ? 'selected' : '' }}>Citizen (User)</option>
                                <option value="staff" {{ $roleFilter == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="dpo" {{ $roleFilter == 'dpo' ? 'selected' : '' }}>DPO</option>
                                <option value="admin" {{ $roleFilter == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" {{ $roleFilter == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
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
                                <option value="">All Created Years</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $yearFilter == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Limit, Approval Status, Reset -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-t border-slate-100/60">
                        <div class="flex items-center gap-3">
                            <!-- Page Size Limit select -->
                            <div class="relative w-32 shrink-0">
                                <select 
                                    name="limit" 
                                    onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                >
                                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 rows</option>
                                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 rows</option>
                                    <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25 rows</option>
                                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 rows</option>
                                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100 rows</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Approval status select -->
                            <div class="relative w-44 shrink-0">
                                <select 
                                    name="approved" 
                                    onchange="this.form.submit()"
                                    class="block w-full py-2 pl-3 pr-8 bg-slate-50/70 border border-slate-200/60 rounded-2xl text-[11px] text-slate-650 outline-none focus:bg-white focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer appearance-none font-semibold"
                                >
                                    <option value="">All Approval Status</option>
                                    <option value="1" {{ $approvedFilter === '1' ? 'selected' : '' }}>Approved Only</option>
                                    <option value="0" {{ $approvedFilter === '0' ? 'selected' : '' }}>Pending Review</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <!-- Reset Filter Link -->
                            @if($search || $roleFilter || $yearFilter || ($approvedFilter !== null && $approvedFilter !== '') || $limit != 20)
                                <a href="{{ route('admin.users.index') }}" 
                                   class="inline-flex items-center text-[11px] font-bold text-slate-450 hover:text-slate-600 transition space-x-1 select-none cursor-pointer pl-2 py-1.5"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3.582"></path></svg>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                        </div>

                        <div>
                            <button type="submit" class="hidden"></button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- User table list -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                @if($users->isEmpty())
                    <div class="text-center py-12 text-slate-400 text-xs">No user accounts found matching the criteria.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">Name</th>
                                    <th class="py-4 px-6">Email Address</th>
                                    <th class="py-4 px-6 text-center">Current Role</th>
                                    <th class="py-4 px-6 text-center">Approval Status</th>
                                    @if(Auth::user()->isSuperAdmin())
                                        <th class="py-4 px-6 text-center">Promote / Change Role</th>
                                        <th class="py-4 px-6 text-center">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach($users as $user)
                                    @php
                                        $roleBadge = match($user->role) {
                                            'superadmin' => 'bg-red-50 text-red-700 border-red-200',
                                            'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'staff' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'dpo' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-slate-50 text-slate-600 border-slate-200'
                                        };
                                        $approvalBadge = $user->is_approved
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-250'
                                            : 'bg-amber-50 text-amber-700 border-amber-250';
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-slate-800">{{ $user->name }}</td>
                                        <td class="py-4 px-6 font-mono select-all">{{ $user->email }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <span class="px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider {{ $roleBadge }}">
                                                {{ strtoupper($user->role) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
                                            <div class="flex flex-col items-center space-y-1.5">
                                                <span class="px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider {{ $approvalBadge }}">
                                                    {{ $user->is_approved ? 'Approved' : 'Pending Review' }}
                                                </span>
                                                @if(!$user->is_approved && Auth::user()->isSuperAdmin())
                                                    <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white hover:bg-emerald-700 font-bold rounded-lg transition text-[9px] uppercase tracking-wider active:scale-95 border border-transparent shadow-sm">
                                                            Approve
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        @if(Auth::user()->isSuperAdmin())
                                            <!-- Role update selector -->
                                            <td class="py-4 px-6 text-center">
                                                @if($user->id === Auth::id())
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Logged In (Role Locked)</span>
                                                @else
                                                     <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="flex items-center justify-center space-x-2">
                                                         @csrf
                                                         @method('PATCH')
                                                         <select name="role" required class="rounded-xl border border-slate-200 py-1.5 px-3 text-xs bg-white text-slate-800 focus:border-[#1e40af] outline-none">
                                                             <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                             <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                                             <option value="dpo" {{ $user->role == 'dpo' ? 'selected' : '' }}>DPO</option>
                                                             <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                             <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                                         </select>
                                                         <button type="submit" class="btn-primary py-1.5 px-3 text-[10px] uppercase tracking-wide">Save</button>
                                                     </form>
                                                @endif
                                            </td>
                                            
                                            <!-- Delete Button -->
                                            <td class="py-4 px-6 text-center">
                                                @if($user->id === Auth::id())
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Self-Delete Blocked</span>
                                                @else
                                                    <x-alert-dialog>
                                                        <x-slot:trigger>
                                                            <button type="button" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 hover:text-rose-850 font-bold rounded-xl transition text-[10px] uppercase tracking-wider active:scale-95">
                                                                Delete
                                                            </button>
                                                        </x-slot:trigger>
                                                        
                                                         <x-slot:icon>
                                                             <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                             </svg>
                                                         </x-slot:icon>
                                                        
                                                        <x-slot:title>
                                                            Delete User Account
                                                        </x-slot:title>
                                                        
                                                        <x-slot:description>
                                                            Are you sure you want to permanently delete the user account "{{ $user->name }}"? All request records will remain archived. This action cannot be undone.
                                                        </x-slot:description>
                                                        
                                                        <x-slot:footer>
                                                            <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">
                                                                Cancel
                                                            </button>
                                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm hover:shadow-md border border-transparent">
                                                                    Confirm Delete
                                                                </button>
                                                            </form>
                                                        </x-slot:footer>
                                                    </x-alert-dialog>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 border-t border-slate-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
