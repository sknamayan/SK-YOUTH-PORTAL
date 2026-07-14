@extends('mail.email-layout')

@section('content')
<div class="content">
    <h2>Hello {{ $requestModel->first_name ?? $requestModel->requestor_first_name }},</h2>
    <p>The status of your service request has been updated by our Barangay desk officers.</p>
    <div class="reference-box">
        <span class="reference-title">Request Type</span>
        <span style="font-weight: 700; font-size: 14px; color: #1e40af;">{{ $typeLabel }}</span>
        <span class="reference-title" style="margin-top: 10px;">Reference Number</span>
        <span class="reference-number">{{ $referenceNumber }}</span>
        <span class="reference-title" style="margin-top: 10px;">New Status</span>
        @if($requestModel->status == 'approved')
            <span class="badge badge-approved">Approved</span>
        @elseif($requestModel->status == 'declined')
            <span class="badge badge-declined">Declined</span>
        @elseif($requestModel->status == 'review')
            <span class="badge badge-review">Under Review</span>
        @else
            <span class="badge badge-pending">Pending</span>
        @endif
    </div>
    @if($requestModel->status == 'approved')
        <p>🎉 <strong>Congratulations!</strong> Your request has been reviewed and approved. Our staff will coordinate the details of the appointment or service delivery with you shortly.</p>
    @elseif($requestModel->status == 'declined')
        <p>⚠️ <strong>Request Declined:</strong> Unfortunately, your request could not be processed at this time. This may be due to missing details, scheduling conflicts, or program capacity limits. You are welcome to submit a new application with revised inputs.</p>
    @elseif($requestModel->status == 'review')
        <p>⏳ <strong>Under Review:</strong> Your request is now actively being reviewed by our desk officers. We will notify you once evaluation is complete.</p>
    @else
        <p>⏳ Your request is currently under review by our desk officers. No action is required from you at this moment.</p>
    @endif
    <p style="margin-top: 25px;">To track this request or check the live logs, please click the button below:</p>
    <div style="text-align: center;">
        <a href="{{ route('track.index') }}?email={{ urlencode($requestModel->email) }}" class="btn" style="color: #ffffff;">Track Your Request</a>
    </div>
</div>
@endsection
