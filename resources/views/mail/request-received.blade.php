@extends('mail.email-layout')

@section('content')
<div class="content">
    <h2>Hello {{ $requestModel->first_name ?? $requestModel->requestor_first_name }},</h2>
    <p>Thank you for submitting your request. We have successfully registered your application in the SK Namayan Youth Portal.</p>
    <div class="reference-box">
        <span class="reference-title">Request Type</span>
        <span style="font-weight: 700; font-size: 14px; color: #1e40af;">{{ $typeLabel }}</span>
        <span class="reference-title" style="margin-top: 10px;">Reference Number</span>
        <span class="reference-number">{{ $referenceNumber }}</span>
        <span class="reference-title" style="margin-top: 10px;">Initial Status</span>
        <span style="font-weight: 700; color: #d97706; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px;">{{ ucfirst($requestModel->status ?? 'pending') }}</span>
    </div>
    <p>Your application is now queued for verification by our Barangay desk officers. We will review your details shortly and update you when there is a status update.</p>
    <p style="margin-top: 25px;">You can track the live progress of this submission or cancel/edit it while it is pending review by clicking the button below:</p>
    <div style="text-align: center;">
        <a href="{{ route('track.index') }}?email={{ urlencode($requestModel->email) }}" class="btn" style="color: #ffffff;">Track Your Request</a>
    </div>
</div>
@endsection
