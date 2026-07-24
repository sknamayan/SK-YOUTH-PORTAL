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
    public function create(Initiative $initiative): View|\Illuminate\Http\RedirectResponse
    {
        if ($initiative->is_coming_soon) {
            abort(403, 'This form is not yet available for submissions.');
        }

        $user = auth()->user();
        $hasProfiling = \App\Models\KkProfile::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->exists();

        if (!$hasProfiling) {
            return redirect()->route('profile.profiling.create')
                ->with('error', 'Please complete your KK Profiling before requesting services.');
        }

        $kkProfile = $user->approvedKkProfile();
        return view('forms.custom', compact('initiative', 'kkProfile'));
    }

    /**
     * Store the dynamic form submission.
     */
    public function store(Request $request, Initiative $initiative): RedirectResponse
    {
        if ($initiative->is_coming_soon) {
            abort(403, 'This form is not yet available for submissions.');
        }

        $user = auth()->user();
        $hasProfiling = \App\Models\KkProfile::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->exists();

        if (!$hasProfiling) {
            return redirect()->route('profile.profiling.create')
                ->with('error', 'Please complete your KK Profiling before requesting services.');
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
        $fields = $initiative->form_structure ?? $initiative->custom_fields;
        if ($initiative && is_array($fields)) {
            $rules['custom_fields'] = ['nullable', 'array'];
            foreach ($fields as $field) {
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

        try {
            $customReq = CustomRequest::create([
                'user_id' => auth()->id(),
                'initiative_id' => $initiative->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'status' => 'pending',
                'custom_fields' => $validated['custom_fields'] ?? [],
            ]);

            $referenceNumber = $customReq->reference_number ?? ('SK-REQ-' . str_pad($customReq->id, 5, '0', STR_PAD_LEFT));

            // Safely attempt to send confirmation mail without crashing user submission on SMTP timeouts
            try {
                if (class_exists(\App\Mail\ServiceRequestConfirmationMail::class)) {
                    \Illuminate\Support\Facades\Mail::to($customReq->email)->send(new \App\Mail\ServiceRequestConfirmationMail($customReq));
                    \Illuminate\Support\Facades\Log::info('Initiative request confirmation email sent', ['ref' => $referenceNumber]);
                }
            } catch (\Throwable $mailException) {
                \Illuminate\Support\Facades\Log::error('SMTP dispatch failed on initiative request creation: ' . $mailException->getMessage(), [
                    'reference' => $referenceNumber,
                    'email' => $customReq->email,
                    'trace' => $mailException->getTraceAsString(),
                ]);
            }

            return redirect()->route('landing')->with([
                'submitted_success' => true,
                'type' => $initiative->title,
                'referenceNumber' => $referenceNumber,
                'name' => $customReq->first_name . ' ' . $customReq->last_name,
                'email' => $customReq->email,
                'detail' => 'Initiative Form Submission',
                'date' => $customReq->created_at->format('M d, Y h:i A'),
            ]);

        } catch (\Throwable $dbException) {
            \Illuminate\Support\Facades\Log::emergency('Initiative Request Creation Failed: ' . $dbException->getMessage(), [
                'user_id' => auth()->id(),
                'initiative' => $initiative->id,
                'trace' => $dbException->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'An unexpected server error occurred while processing your request. Please try again.'])->withInput();
        }
    }
}
