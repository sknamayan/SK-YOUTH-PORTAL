@extends('layouts.app')

@section('content')
<div class="flex-1 bg-[#f8fafc] dark:bg-slate-950 font-sans min-h-screen pt-12 pb-24 md:py-12" x-data="{ showConfirm: false, loading: false }">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <a href="{{ route('landing') }}" class="hover:text-[#1e40af] dark:hover:text-blue-400">Home</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 dark:text-slate-100">Initiative Request</span>
        </div>

        <!-- Page Header -->
        <div class="space-y-2">
            <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-widest font-mono">
                SK Initiative Services
            </span>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white font-display uppercase tracking-tight leading-tight">
                {{ $initiative->title }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                {{ $initiative->description }}
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <form method="POST" action="{{ route('forms.custom.store', $initiative->id) }}" class="space-y-5" x-ref="customForm">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" required value="{{ old('first_name', auth()->user()->first_name ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                        @error('first_name')
                            <p class="text-rose-550 text-[10px] font-black mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" required value="{{ old('last_name', auth()->user()->last_name ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                        @error('last_name')
                            <p class="text-rose-550 text-[10px] font-black mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required value="{{ old('email', auth()->user()->email ?? '') }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition font-sans">
                    @error('email')
                        <p class="text-rose-550 text-[10px] font-black mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if(!empty($initiative->custom_fields) && is_array($initiative->custom_fields))
                    <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Additional Information Required</span>
                        <div class="space-y-4">
                            @foreach($initiative->custom_fields as $field)
                                @php
                                    $fieldName = $field['name'];
                                    $oldVal = old("custom_fields.{$fieldName}");
                                @endphp
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 mb-1.5 font-display">
                                        {{ $field['label'] }}
                                        @if($field['required'] ?? false)
                                            <span class="text-rose-500">*</span>
                                        @endif
                                    </label>

                                    @if($field['type'] === 'textarea')
                                        <textarea name="custom_fields[{{ $fieldName }}]" 
                                                  placeholder="{{ $field['placeholder'] ?? '' }}"
                                                  rows="3"
                                                  {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                  class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition resize-none">{{ $oldVal }}</textarea>
                                    @elseif($field['type'] === 'select')
                                        <select name="custom_fields[{{ $fieldName }}]"
                                                {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition cursor-pointer">
                                            <option value="">{{ $field['placeholder'] ?? 'Select an option' }}</option>
                                            @if(!empty($field['options']))
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}" {{ $oldVal === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @elseif($field['type'] === 'file')
                                        <input type="file" 
                                               name="custom_fields[{{ $fieldName }}]"
                                               {{ ($field['required'] ?? false) ? 'required' : '' }}
                                               class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#1e40af] dark:file:bg-slate-800 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-slate-750 transition cursor-pointer">
                                    @else
                                        <input type="{{ $field['type'] === 'number' ? 'number' : ($field['type'] === 'date' ? 'date' : 'text') }}"
                                               name="custom_fields[{{ $fieldName }}]"
                                               value="{{ $oldVal }}"
                                               placeholder="{{ $field['placeholder'] ?? '' }}"
                                               {{ ($field['required'] ?? false) ? 'required' : '' }}
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-955 px-3.5 py-2.5 text-xs dark:text-white outline-none focus:border-[#1e40af] focus:ring-4 focus:ring-blue-600/5 transition">
                                    @endif

                                    @error("custom_fields.{$fieldName}")
                                        <p class="text-rose-550 text-[10px] font-black mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-slate-800 gap-3">
                    <a href="{{ route('landing') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition select-none">
                        Cancel
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('track.index') }}" class="text-xs font-bold text-slate-500 hover:text-[#1e40af] transition mr-2">Track Status</a>
                        <button type="button" @click="showConfirm = true" :disabled="loading" class="inline-flex items-center px-6 py-3 rounded-xl bg-[#1e40af] hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-wider transition active:scale-95 shadow-sm disabled:opacity-50 select-none">
                            <span x-text="loading ? 'Processing...' : 'Submit Request'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- Confirm Submission Modal -->
    <div x-show="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-xl max-w-sm w-full space-y-4 text-center transform scale-100 transition duration-200">
            <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-950/45 text-amber-600 dark:text-amber-300 flex items-center justify-center text-xl mx-auto">⚠️</div>
            <div class="space-y-1">
                <h3 class="text-base font-black text-slate-800 dark:text-white uppercase font-display tracking-tight">Confirm Submission</h3>
                <p class="text-[11px] text-slate-400">Are you sure you want to process this request? Please review all inputs before confirming.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="showConfirm = false" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold uppercase transition hover:bg-slate-50">
                    Go Back
                </button>
                <button type="button" @click="showConfirm = false; loading = true; $refs.customForm.submit();" class="flex-1 py-2.5 rounded-xl bg-[#1e40af] hover:bg-blue-700 text-white text-xs font-bold uppercase transition shadow-md active:scale-95">
                    Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<x-mobile-bottom-action @click="showConfirm = true" :disabled="loading">
    Submit Request
</x-mobile-bottom-action>
@endsection
