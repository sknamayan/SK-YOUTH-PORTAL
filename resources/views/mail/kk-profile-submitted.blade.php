@extends('mail.email-layout')

@section('content')
<div class="content">
    <h2>Hello {{ $profile->first_name }},</h2>
    <p>Thank you for completing your Katipunan ng Kabataan (KK) Profiling registry. We have successfully received your profile details.</p>
    <div class="reference-box">
        <span class="reference-title">Registry Submission</span>
        <span style="font-weight: 700; font-size: 14px; color: #1e40af;">Katipunan ng Kabataan Profile</span>
        <span class="reference-title" style="margin-top: 10px;">Submission Status</span>
        <span style="font-weight: 700; color: #d97706; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">{{ ucfirst($profile->status ?? 'pending') }}</span>
    </div>
    <p>Your profile registry details are now queued for review by our SK Namayan secretariat. Once approved, all community portal transactions (service requests and tournament registrations) will be unlocked for you.</p>
    <p style="margin-top: 25px;">You can track your profile verification status by visiting your account settings:</p>
    <div style="text-align: center;">
        <a href="{{ route('profile.edit') }}" class="btn" style="color: #ffffff;">Visit Account Settings</a>
    </div>
</div>
@endsection
