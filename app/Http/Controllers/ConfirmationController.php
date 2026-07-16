<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfirmationController extends Controller
{
    /**
     * Show confirmation details.
     */
    public function show(Request $request): View
    {
        $type = $request->query('type', 'General Request');
        $prefix = $request->query('prefix', 'REQ');
        $id = $request->query('id', 0);
        $name = $request->query('name', 'Anonymous Citizen');
        $email = $request->query('email', 'not-provided@sk.gov.ph');
        $detail = $request->query('detail', 'No details provided');
        $date = $request->query('date', now()->format('M d, Y h:i A'));

        // Resolve model to get reference number
        $model = match ($prefix) {
            'HEA' => \App\Models\HealthRequest::find($id),
            'MED' => \App\Models\MedicineRequest::find($id),
            'SIL' => \App\Models\SilidKarununganRequest::find($id),
            'SPO' => \App\Models\SportsRegistration::find($id) ?? \App\Models\RegistrationResponse::find($id),
            'REQ' => \App\Models\CustomRequest::find($id),
            default => null
        };

        $referenceNumber = ($model && !empty($model->reference_number))
            ? $model->reference_number
            : 'SK-' . $prefix . '-' . str_pad($id, 5, '0', STR_PAD_LEFT);

        return view('confirmation.index', compact(
            'type',
            'referenceNumber',
            'name',
            'email',
            'detail',
            'date'
        ));
    }
}
