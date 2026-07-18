@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12 flex-1">
    
    @php
        $genderOptions = [
            'Male' => 'Male',
            'Female' => 'Female',
            'Prefer not to say' => 'Prefer not to say'
        ];
        
        $sportOptions = [
            'Basketball' => 'Basketball',
            'Volleyball' => 'Volleyball',
            'Football' => 'Football',
            'Badminton' => 'Badminton',
            'Table Tennis' => 'Table Tennis',
            'Swimming' => 'Swimming',
            'Athletics' => 'Athletics',
            'Boxing' => 'Boxing',
            'Martial Arts' => 'Martial Arts',
            'Esports' => 'Esports',
            'Other' => 'Other'
        ];

        $timeOptions = [];
        for ($h = 8; $h <= 17; $h++) {
            $formattedH = str_pad($h, 2, '0', STR_PAD_LEFT);
            $timeOptions["$formattedH:00"] = "$formattedH:00";
            if ($h < 17) {
                $timeOptions["$formattedH:30"] = "$formattedH:30";
            }
        }

        $typeLabel = match($type) {
            'health' => 'Health Consultation',
            'medicine' => 'Pabili Medicine Services',
            'silid' => 'Silid Karunungan Booking',
            'sports' => 'Sports Registration',
            'custom' => ($req->initiative ? $req->initiative->title : 'Custom Request'),
            default => 'Request'
        };
    @endphp

    <div class="mb-6">
        <a href="{{ route('track.index', ['email' => $req->email]) }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-[#1e40af] uppercase tracking-wider transition">
            &larr; Back to Tracking
        </a>
    </div>

    <x-form-card 
        title="Edit {{ $typeLabel }}" 
        subtitle="Modify your pending request details. Changes will be reflected instantly." 
        action="{{ route('track.update', [$type, $req->id]) }}"
    >
        @method('PUT')

        @if($type === 'health')
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="First Name" name="first_name" required="true" value="{{ $req->first_name }}" />
                <x-form-input label="Last Name" name="last_name" required="true" value="{{ $req->last_name }}" />
                <x-form-input label="Middle Name" name="middle_name" value="{{ $req->middle_name }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Age" name="age" type="number" min="0" max="120" required="true" value="{{ $req->age }}" />
                <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" selected="{{ $req->gender }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $req->email }}" />
                <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $req->contact_number }}" />
            </div>

            <x-form-input label="Concerns" name="concerns" type="textarea" required="true" placeholder="Detail your symptoms, advice needed, or other medical inquiries..." value="{{ $req->concerns }}" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" value="{{ $req->preferred_date?->format('Y-m-d') }}" />
                <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" selected="{{ $req->preferred_time }}" />
            </div>

        @elseif($type === 'medicine')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" value="{{ $req->requestor_first_name }}" />
                <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" value="{{ $req->requestor_last_name }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" value="{{ $req->requestor_age }}" />
                <x-form-select label="Requestor Gender" name="requestor_gender" required="true" :options="$genderOptions" selected="{{ $req->requestor_gender }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $req->email }}" />
                <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $req->contact_number }}" />
            </div>

            <x-form-input label="Complete Delivery Address" name="complete_address" type="textarea" required="true" placeholder="Enter house number, street, barangay, and landmark..." value="{{ $req->complete_address }}" />

        @elseif($type === 'silid')
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="Requestor First Name" name="requestor_first_name" required="true" value="{{ $req->requestor_first_name }}" />
                <x-form-input label="Requestor Last Name" name="requestor_last_name" required="true" value="{{ $req->requestor_last_name }}" />
                <x-form-input label="Requestor Middle Name" name="requestor_middle_name" value="{{ $req->requestor_middle_name }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="Requestor Age" name="requestor_age" type="number" min="0" max="120" required="true" value="{{ $req->requestor_age }}" />
                <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $req->email }}" />
                <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $req->contact_number }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Preferred Date" name="preferred_date" type="date" min="{{ date('Y-m-d') }}" required="true" value="{{ $req->preferred_date?->format('Y-m-d') }}" />
                <x-form-select label="Preferred Time Slot" name="preferred_time" required="true" :options="$timeOptions" selected="{{ $req->preferred_time }}" />
            </div>

        @elseif($type === 'sports')
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="First Name" name="first_name" required="true" value="{{ $req->first_name }}" />
                <x-form-input label="Last Name" name="last_name" required="true" value="{{ $req->last_name }}" />
                <x-form-input label="Middle Name" name="middle_name" value="{{ $req->middle_name }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Age (10–30)" name="age" type="number" min="10" max="30" required="true" value="{{ $req->age }}" />
                <x-form-select label="Gender" name="gender" required="true" :options="$genderOptions" selected="{{ $req->gender }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $req->email }}" />
                <x-form-input label="Contact Number" name="contact_number" required="true" placeholder="e.g. 09123456789" value="{{ $req->contact_number }}" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-select label="Choose Sport" name="sport" required="true" :options="$sportOptions" selected="{{ $req->sport }}" />
                <x-form-input label="Team Name (Optional)" name="team_name" placeholder="Leave empty if signing up individually" value="{{ $req->team_name }}" />
            </div>

            <x-form-input label="Preferred Event Date" name="event_date" type="date" min="{{ date('Y-m-d') }}" required="true" value="{{ $req->event_date?->format('Y-m-d') }}" />

            <x-form-input label="Remarks / Queries" name="remarks" type="textarea" placeholder="Add any special requirements, team configurations, or general remarks..." value="{{ $req->remarks }}" />
        @elseif($type === 'custom')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="First Name" name="first_name" required="true" value="{{ $req->first_name }}" />
                <x-form-input label="Last Name" name="last_name" required="true" value="{{ $req->last_name }}" />
            </div>
            <x-form-input label="Email Address" name="email" type="email" required="true" value="{{ $req->email }}" />
        @endif

        @if($initiative && is_array($initiative->custom_fields) && count($initiative->custom_fields) > 0)
            <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                <span class="text-[10px] font-black text-[#1e40af] uppercase tracking-widest block font-display">Additional Information Required</span>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($initiative->custom_fields as $field)
                        @php
                            $fieldName = $field['name'] ?? '';
                            $val = $req->custom_fields[$fieldName] ?? '';
                        @endphp
                        <x-form-input 
                            label="{{ $field['label'] }}" 
                            name="custom_fields[{{ $fieldName }}]" 
                            type="{{ $field['type'] ?? 'text' }}" 
                            required="{{ ($field['required'] ?? false) ? 'true' : 'false' }}" 
                            placeholder="{{ $field['placeholder'] ?? '' }}" 
                            value="{{ $val }}"
                        />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pt-4 flex items-center justify-between gap-4">
            <a href="{{ route('track.index', ['email' => $req->email]) }}" class="btn-secondary text-center flex-1 sm:flex-initial py-2.5">
                Cancel
            </a>
            <button type="submit" class="btn-primary flex-1 sm:flex-initial py-2.5">
                Save Changes
            </button>
        </div>
    </x-form-card>

</div>
@endsection
