<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\SportsRegistration;
use App\Http\Requests\SportsRegistrationFormRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SportsRegistrationController extends Controller
{
    /**
     * Show the public sports league registration form.
     */
    public function showRegistrationForm(Request $request): View
    {
        $leagues = League::where('status', 'Active')->get();
        
        $alreadyRegistered = null;
        $kkProfile = null;
        if (auth()->check()) {
            $alreadyRegistered = SportsRegistration::where('email', auth()->user()->email)
                ->whereIn('status', ['pending', 'review', 'approved'])
                ->first();
            $kkProfile = auth()->user()->approvedKkProfile();
        }

        return view('forms.sports-registration', compact('leagues', 'alreadyRegistered', 'kkProfile'));
    }

    /**
     * Submit citizen sports registration response.
     */
    public function submitRegistration(SportsRegistrationFormRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Enforce 1 citizen = 1 sports league registration constraint
        $existing = SportsRegistration::where('email', $request->input('email'))
            ->whereIn('status', ['pending', 'review', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have an active registration for ' . $existing->sport . ' (' . $existing->division . '). Citizens are limited to one active tournament registration.');
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('sports/profile-pictures', 'public');
        }

        // Handle conditional minor file upload (Guardian ID)
        if ($request->hasFile('guardian_gov_id')) {
            $data['guardian_gov_id'] = $request->file('guardian_gov_id')->store('sports/guardian-ids', 'public');
        }

        // Handle conditional adult file upload (Voter Certificate)
        if ($request->hasFile('voter_cert')) {
            $data['voter_cert'] = $request->file('voter_cert')->store('sports/voter-certs', 'public');
        }

        // Assign non-null required database columns
        $data['event_date'] = now()->toDateString();
        $data['status'] = 'pending';

        $registration = SportsRegistration::create($data);

        // Audit Logs integration
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sports_registration_submitted',
            'subject_type' => get_class($registration),
            'subject_id' => $registration->id,
            'payload' => [
                'citizen_name' => $registration->first_name . ' ' . $registration->last_name,
                'citizen_email' => $registration->email,
                'sport' => $registration->sport,
                'division' => $registration->division
            ],
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('forms.sports.create')
            ->with('success', 'Your sports league registration has been submitted successfully.');
    }
}
