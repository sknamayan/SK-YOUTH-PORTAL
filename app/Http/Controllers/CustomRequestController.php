<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use App\Models\CustomRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomRequestController extends Controller
{
    /**
     * Show the dynamic custom form for a specific initiative.
     */
    public function create(Initiative $initiative): View
    {
        if ($initiative->is_coming_soon) {
            abort(403, 'This form is not yet available for submissions.');
        }

        return view('forms.custom', compact('initiative'));
    }

    /**
     * Store the dynamic form submission.
     */
    public function store(Request $request, Initiative $initiative): RedirectResponse
    {
        if ($initiative->is_coming_soon) {
            abort(403, 'This form is not yet available for submissions.');
        }

        $input = $request->all();
        if (isset($input['first_name']) && is_string($input['first_name'])) {
            $input['first_name'] = mb_strtoupper($input['first_name'], 'UTF-8');
        }
        if (isset($input['last_name']) && is_string($input['last_name'])) {
            $input['last_name'] = mb_strtoupper($input['last_name'], 'UTF-8');
        }
        if (isset($input['custom_fields']) && is_array($input['custom_fields'])) {
            foreach ($input['custom_fields'] as $key => $val) {
                if (is_string($val)) {
                    $input['custom_fields'][$key] = mb_strtoupper($val, 'UTF-8');
                }
            }
        }
        $request->merge($input);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ];

        $customAttributes = [];
        if ($initiative && is_array($initiative->custom_fields)) {
            $rules['custom_fields'] = ['nullable', 'array'];
            foreach ($initiative->custom_fields as $field) {
                $fieldName = $field['name'] ?? null;
                if (!$fieldName) continue;

                $fieldRules = [];
                if ($field['required'] ?? false) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                $fieldRules[] = match ($field['type'] ?? 'text') {
                    'number' => 'numeric',
                    'date' => 'date',
                    default => 'string',
                };

                $rules["custom_fields.{$fieldName}"] = $fieldRules;
                $customAttributes["custom_fields.{$fieldName}"] = $field['label'] ?? $fieldName;
            }
        }

        $validated = $request->validate($rules, [], $customAttributes);

        $customReq = CustomRequest::create([
            'user_id' => auth()->id(),
            'initiative_id' => $initiative->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'status' => 'pending',
            'custom_fields' => $validated['custom_fields'] ?? [],
        ]);

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => $initiative->title,
            'referenceNumber' => $customReq->reference_number ?? ('SK-REQ-' . str_pad($customReq->id, 5, '0', STR_PAD_LEFT)),
            'name' => $customReq->first_name . ' ' . $customReq->last_name,
            'email' => $customReq->email,
            'detail' => 'Initiative Form Submission',
            'date' => $customReq->created_at->format('M d, Y h:i A'),
        ]);
    }
}
