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

        HealthRequest::create($validated);

        return redirect()->back()->with('success', 'Health consultation request submitted successfully!');
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

        MedicineRequest::create($validated);

        return redirect()->back()->with('success', 'Pabili medicine request submitted successfully!');
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

        SilidKarununganRequest::create($validated);

        return redirect()->back()->with('success', 'Silid Karunungan request submitted successfully!');
    }
}
