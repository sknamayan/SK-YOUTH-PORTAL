@extends('mail.email-layout')

@section('content')
<div class="content">
    <h2>Hello {{ $user->first_name }},</h2>
    <p>Welcome to the SK Namayan Youth Portal! Your account has been successfully created.</p>
    <p>To access all of our community services (including consultations, medicine procurement support, silid karunungan booking, and SIKLAB), you must complete your Katipunan ng Kabataan (KK) Profiling registry.</p>
    <p style="margin-top: 25px;">You can log in to your account and submit your profile questionnaire by clicking the button below:</p>
    <div style="text-align: center;">
        <a href="{{ route('login') }}" class="btn" style="color: #ffffff;">Log In to Portal</a>
    </div>
</div>
@endsection
