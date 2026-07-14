<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Dashboard - SK Namayan</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-gray-900 font-sans antialiased" x-data="{ activeTab: 'health' }">

        <!-- Header Navigation -->
        <nav class="bg-[#1e40af] text-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-3">
                        <a href="/" class="text-lg font-bold tracking-wider uppercase text-white hover:text-blue-100 transition">
                            SK PORTAL
                        </a>
                        <span class="text-blue-200">/</span>
                        <span class="text-xs font-semibold tracking-wider bg-white/20 px-2.5 py-1 rounded-full uppercase">
                            {{ Auth::user()->role }} Panel
                        </span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold">{{ Auth::user()->name }}</div>
                            <div class="text-[10px] text-blue-200 uppercase font-semibold tracking-wider">{{ Auth::user()->email }}</div>
                        </div>

                        <!-- Home Page button -->
                        <a href="/" class="text-xs font-bold text-white hover:bg-white/10 px-3 py-2 rounded-lg border border-white/25 transition">
                            View Website
                        </a>

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold bg-white text-[#1e40af] hover:bg-blue-50 px-3 py-2 rounded-lg transition">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Welcome Info banner -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-sm mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Portal Submissions</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage, track, and process citizen requests submitted through the landing page.</p>
                </div>
                <div class="flex gap-4">
                    <div class="bg-blue-50 border border-blue-100 px-4 py-3 rounded-xl text-center min-w-[100px]">
                        <span class="block text-xl font-extrabold text-[#1e40af]">{{ $healthRequests->count() + $medicineRequests->count() + $silidRequests->count() }}</span>
                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Total Requests</span>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 mb-6 overflow-x-auto whitespace-nowrap">
                <button @click="activeTab = 'health'"
                        :class="activeTab === 'health' ? 'border-[#1e40af] text-[#1e40af] font-bold' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="py-4 px-6 border-b-2 text-sm uppercase tracking-wider transition duration-150">
                    Health Consultations ({{ $healthRequests->count() }})
                </button>
                <button @click="activeTab = 'medicine'"
                        :class="activeTab === 'medicine' ? 'border-[#1e40af] text-[#1e40af] font-bold' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="py-4 px-6 border-b-2 text-sm uppercase tracking-wider transition duration-150">
                    Pabili Medicine ({{ $medicineRequests->count() }})
                </button>
                <button @click="activeTab = 'silid'"
                        :class="activeTab === 'silid' ? 'border-[#1e40af] text-[#1e40af] font-bold' : 'border-transparent text-gray-400 hover:text-gray-600'"
                        class="py-4 px-6 border-b-2 text-sm uppercase tracking-wider transition duration-150">
                    Silid Karunungan ({{ $silidRequests->count() }})
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                <!-- 1. HEALTH TAB -->
                <div x-show="activeTab === 'health'">
                    @if($healthRequests->isEmpty())
                        <div class="text-center py-16 text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium">No health consultation requests submitted yet.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400">
                                        <th class="py-4 px-6">Date Submitted</th>
                                        <th class="py-4 px-6">Name</th>
                                        <th class="py-4 px-6">Age/Gender</th>
                                        <th class="py-4 px-6">Email & Contact</th>
                                        <th class="py-4 px-6">Concerns</th>
                                        <th class="py-4 px-6">Preferred Appointment</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach($healthRequests as $req)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-4 px-6 text-xs text-gray-400 font-medium">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                                            <td class="py-4 px-6 font-bold text-gray-900">{{ $req->last_name }}, {{ $req->first_name }} {{ $req->middle_name }}</td>
                                            <td class="py-4 px-6"><span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $req->age }} yrs</span> / <span class="capitalize">{{ $req->gender }}</span></td>
                                            <td class="py-4 px-6">
                                                <div class="text-gray-900">{{ $req->email }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">{{ $req->contact_number }}</div>
                                            </td>
                                            <td class="py-4 px-6 max-w-xs truncate" title="{{ $req->concerns }}">{{ $req->concerns }}</td>
                                            <td class="py-4 px-6">
                                                <span class="block font-semibold text-gray-900">{{ \Carbon\Carbon::parse($req->preferred_date)->format('M d, Y') }}</span>
                                                <span class="block text-xs text-[#1e40af] font-medium mt-0.5">{{ $req->preferred_time }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- 2. MEDICINE TAB -->
                <div x-show="activeTab === 'medicine'">
                    @if($medicineRequests->isEmpty())
                        <div class="text-center py-16 text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium">No medicine requests submitted yet.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400">
                                        <th class="py-4 px-6">Date Submitted</th>
                                        <th class="py-4 px-6">Requestor Name</th>
                                        <th class="py-4 px-6">Age/Gender</th>
                                        <th class="py-4 px-6">Email & Contact</th>
                                        <th class="py-4 px-6">Complete Address</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach($medicineRequests as $req)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-4 px-6 text-xs text-gray-400 font-medium">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                                            <td class="py-4 px-6 font-bold text-gray-900">{{ $req->requestor_last_name }}, {{ $req->requestor_first_name }}</td>
                                            <td class="py-4 px-6"><span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $req->requestor_age }} yrs</span> / <span class="capitalize">{{ $req->requestor_gender }}</span></td>
                                            <td class="py-4 px-6">
                                                <div class="text-gray-900">{{ $req->email }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">{{ $req->contact_number }}</div>
                                            </td>
                                            <td class="py-4 px-6 max-w-sm truncate" title="{{ $req->complete_address }}">{{ $req->complete_address }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- 3. SILID KARUNUNGAN TAB -->
                <div x-show="activeTab === 'silid'">
                    @if($silidRequests->isEmpty())
                        <div class="text-center py-16 text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium">No Silid Karunungan request records found.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400">
                                        <th class="py-4 px-6">Date Submitted</th>
                                        <th class="py-4 px-6">Requestor Name</th>
                                        <th class="py-4 px-6">Age</th>
                                        <th class="py-4 px-6">Email & Contact</th>
                                        <th class="py-4 px-6">Preferred Appointment Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach($silidRequests as $req)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-4 px-6 text-xs text-gray-400 font-medium">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                                            <td class="py-4 px-6 font-bold text-gray-900">{{ $req->requestor_last_name }}, {{ $req->requestor_first_name }} {{ $req->requestor_middle_name }}</td>
                                            <td class="py-4 px-6"><span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $req->requestor_age }} yrs</span></td>
                                            <td class="py-4 px-6">
                                                <div class="text-gray-900">{{ $req->email }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">{{ $req->contact_number }}</div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="block font-semibold text-gray-900">{{ \Carbon\Carbon::parse($req->preferred_date)->format('M d, Y') }}</span>
                                                <span class="block text-xs text-[#1e40af] font-medium mt-0.5">{{ $req->preferred_time }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </body>
</html>
