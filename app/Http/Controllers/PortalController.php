<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    /**
     * Display the public landing page with categories.
     */
    public function index()
    {
        $categories = [
            'EDUCATION',
            'HEALTH',
            'GOVERNANCE',
            'ACTIVE CITIZENSHIP',
            'SOCIAL INCLUSION',
            'PEACE BUILDING',
            'ENVIRONMENT',
            'YOUTH EMPLOYMENT & EMPOWERMENT',
            'AGRICULTURE',
            'GLOBAL MOBILITY'
        ];

        return view('welcome', compact('categories'));
    }

    /**
     * Handle Health Service form submission.
     */
    public function submitHealth(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:0|max:120',
            'gender' => 'required|string|in:Male,Female,Other',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:50',
            'concerns' => 'required|string',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string|max:50',
        ]);

        $healthReq = HealthRequest::create($validated);

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Health Consultation',
            'referenceNumber' => $healthReq->reference_number ?? ('SK-REQ-' . str_pad($healthReq->id, 5, '0', STR_PAD_LEFT)),
            'name' => $healthReq->first_name . ' ' . $healthReq->last_name,
            'email' => $healthReq->email,
            'detail' => $healthReq->concerns,
            'date' => $healthReq->created_at->format('M d, Y h:i A'),
        ]);
    }

    /**
     * Handle Pabili Medicine Service form submission.
     */
    public function submitMedicine(Request $request)
    {
        $validated = $request->validate([
            'requestor_first_name' => 'required|string|max:255',
            'requestor_last_name' => 'required|string|max:255',
            'requestor_age' => 'required|integer|min:0|max:120',
            'requestor_gender' => 'required|string|in:Male,Female,Other',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:50',
            'complete_address' => 'required|string',
        ]);

        $medicineReq = MedicineRequest::create($validated);

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Pabili Medicine Services',
            'referenceNumber' => $medicineReq->reference_number ?? ('SK-REQ-' . str_pad($medicineReq->id, 5, '0', STR_PAD_LEFT)),
            'name' => $medicineReq->requestor_first_name . ' ' . $medicineReq->requestor_last_name,
            'email' => $medicineReq->email,
            'detail' => $medicineReq->complete_address,
            'date' => $medicineReq->created_at->format('M d, Y h:i A'),
        ]);
    }

    /**
     * Handle Silid Karunungan Service form submission.
     */
    public function submitSilidKarunungan(Request $request)
    {
        $validated = $request->validate([
            'requestor_first_name' => 'required|string|max:255',
            'requestor_last_name' => 'required|string|max:255',
            'requestor_middle_name' => 'nullable|string|max:255',
            'requestor_age' => 'required|integer|min:0|max:120',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:50',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string|max:50',
        ]);

        $silidReq = SilidKarununganRequest::create($validated);

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Silid Karunungan Booking',
            'referenceNumber' => $silidReq->reference_number ?? ('SK-REQ-' . str_pad($silidReq->id, 5, '0', STR_PAD_LEFT)),
            'name' => $silidReq->requestor_first_name . ' ' . $silidReq->requestor_last_name,
            'email' => $silidReq->email,
            'detail' => 'Booking Schedule: ' . $silidReq->preferred_date->format('M d, Y') . ' @ ' . $silidReq->preferred_time,
            'date' => $silidReq->created_at->format('M d, Y h:i A'),
        ]);
    }
}
