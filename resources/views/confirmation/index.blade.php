@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-12 sm:py-16 flex-1 flex flex-col justify-center">

    <div class="text-center space-y-6">
        
        <!-- Animated Success Ring -->
        <div class="relative flex items-center justify-center w-20 h-20 mx-auto">
            <span class="animate-ping absolute inline-flex h-16 w-16 rounded-full bg-emerald-400 opacity-20"></span>
            <div class="relative rounded-full w-16 h-16 bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <div>
            <h1 class="text-2xl font-black font-display text-slate-800 uppercase tracking-tight">Request Submitted!</h1>
            <p class="text-xs text-slate-400 mt-2">Thank you! Your digital request has been successfully filed with the SK council.</p>
        </div>

        <!-- Reference card with dashed border -->
        <div class="p-6 bg-blue-50/50 border-2 border-dashed border-blue-200 rounded-2xl max-w-sm mx-auto text-center space-y-1">
            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest block font-display">Reference Number</span>
            <span class="text-xl font-mono font-black text-[#1e40af] select-all">{{ $referenceNumber }}</span>
            <p class="text-[10px] text-slate-400 pt-1">Copy this code to track your status at any time.</p>
        </div>

        <!-- Details summary table -->
        <div class="card p-0 overflow-hidden text-left border border-slate-100 text-xs">
            <div class="bg-slate-50 border-b border-slate-100 px-5 py-3">
                <span class="font-bold text-slate-700 font-display uppercase tracking-wider">Submission Summary</span>
            </div>
            <table class="w-full">
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-400 w-1/3">Request Type</td>
                        <td class="px-5 py-3 font-bold text-slate-800">{{ $type }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-400">Requestor Name</td>
                        <td class="px-5 py-3 text-slate-800 font-medium">{{ $name }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-400">Email Address</td>
                        <td class="px-5 py-3 text-slate-800 font-mono">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-400">Preferred Details</td>
                        <td class="px-5 py-3 text-slate-800 font-medium">{{ $detail }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-400">Initial Status</td>
                        <td class="px-5 py-3">
                            <span class="badge-pending">Pending</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-400">Date Submitted</td>
                        <td class="px-5 py-3 text-slate-800">{{ $date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Email note -->
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs text-emerald-800 max-w-md mx-auto leading-relaxed flex items-start space-x-3 shadow-sm text-left">
            <span class="text-xl shrink-0">✉️</span>
            <div>
                <span class="font-bold block text-emerald-950 text-sm mb-0.5">Confirmation Email Sent!</span>
                A receipt and confirmation details have been sent to <span class="font-semibold underline text-emerald-950 font-mono">{{ $email }}</span>. Please check your inbox (and spam folder) for your reference code and updates.
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex items-center justify-center gap-3 pt-2">
            <a href="{{ route('track.index') }}" class="btn-primary">Track This Request</a>
            <a href="/" class="btn-outline">Back to Home</a>
        </div>

    </div>

</div>
@endsection
