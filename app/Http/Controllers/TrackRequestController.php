<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\RegistrationResponse;
use App\Models\SportsRegistration;
use App\Models\CustomRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackRequestController extends Controller
{
    /**
     * Show tracking index page. Supports query param or session fallback for redirect updates.
     */
    public function index(Request $request): View
    {
        $searchQuery = $request->query('email') ?? session('tracked_email');
        $results = collect();
        $searched = false;

        if ($searchQuery) {
            $searchQuery = trim($searchQuery);
            $searched = true;

            $isEmail = filter_var($searchQuery, FILTER_VALIDATE_EMAIL);
            $isRef = preg_match('/^SK-(HEA|MED|SIL|SPO|REQ)-(\d+)$/i', $searchQuery, $matches);

            if ($isEmail) {
                $health = HealthRequest::where('email', $searchQuery)->get();
                $medicine = MedicineRequest::where('email', $searchQuery)->get();
                $silid = SilidKarununganRequest::where('email', $searchQuery)->get();
                $sports1 = RegistrationResponse::where('citizen_email', $searchQuery)->get();
                $sports2 = SportsRegistration::where('email', $searchQuery)->get();
                $sports = $sports1->concat($sports2);
                $custom = CustomRequest::where('email', $searchQuery)->get();
            } elseif ($isRef) {
                $prefix = strtoupper($matches[1]);
                $id = intval($matches[2]);

                $health = $prefix === 'HEA' ? HealthRequest::where('id', $id)->get() : collect();
                $medicine = $prefix === 'MED' ? MedicineRequest::where('id', $id)->get() : collect();
                $silid = $prefix === 'SIL' ? SilidKarununganRequest::where('id', $id)->get() : collect();
                if ($prefix === 'SPO') {
                    $sports1 = RegistrationResponse::where('id', $id)->get();
                    $sports2 = SportsRegistration::where('id', $id)->get();
                    $sports = $sports1->concat($sports2);
                } else {
                    $sports = collect();
                }
                $custom = $prefix === 'REQ' ? CustomRequest::where('id', $id)->get() : collect();
            } else {
                $health = collect();
                $medicine = collect();
                $silid = collect();
                $sports = collect();
                $custom = collect();
            }

            $health = $health->map(function ($item) {
                $item->type_label = 'Health Consultation';
                $item->type_prefix = 'HEA';
                $item->type_slug = 'health';
                $item->icon = '🏥';
                $item->icon_name = 'health';
                $item->title = $item->first_name . ' ' . $item->last_name;
                $item->summary = 'Preferred Appointment: ' . $item->preferred_date->format('M d, Y') . ' @ ' . $item->preferred_time;
                
                $submitted = [
                    'Full Name' => $item->first_name . ' ' . ($item->middle_name ? $item->middle_name . ' ' : '') . $item->last_name,
                    'Age' => $item->age,
                    'Gender' => $item->gender,
                    'Email Address' => $item->email,
                    'Contact Number' => $item->contact_number,
                    'Concerns/Details' => $item->concerns,
                    'Preferred Date' => $item->preferred_date ? $item->preferred_date->format('M d, Y') : null,
                    'Preferred Time' => $item->preferred_time,
                ];
                $customFields = $item->custom_fields;
                if (is_string($customFields)) {
                    $customFields = json_decode($customFields, true);
                }
                if (is_array($customFields)) {
                    foreach ($customFields as $k => $v) {
                        $submitted[ucwords(str_replace('_', ' ', $k))] = $v;
                    }
                }
                $item->submitted_data = $submitted;
                return $item;
            });

            $medicine = $medicine->map(function ($item) {
                $item->type_label = 'Pabili Medicine Services';
                $item->type_prefix = 'MED';
                $item->type_slug = 'medicine';
                $item->icon = '💊';
                $item->icon_name = 'medicine';
                $item->title = $item->requestor_first_name . ' ' . $item->requestor_last_name;
                $item->summary = 'Address: ' . $item->complete_address;
                
                $submitted = [
                    'Full Name' => $item->requestor_first_name . ' ' . $item->requestor_last_name,
                    'Age' => $item->requestor_age,
                    'Gender' => $item->requestor_gender,
                    'Email Address' => $item->email,
                    'Contact Number' => $item->contact_number,
                    'Complete Address' => $item->complete_address,
                ];
                $customFields = $item->custom_fields;
                if (is_string($customFields)) {
                    $customFields = json_decode($customFields, true);
                }
                if (is_array($customFields)) {
                    foreach ($customFields as $k => $v) {
                        $submitted[ucwords(str_replace('_', ' ', $k))] = $v;
                    }
                }
                $item->submitted_data = $submitted;
                return $item;
            });

            $silid = $silid->map(function ($item) {
                $item->type_label = 'Silid Karunungan Booking';
                $item->type_prefix = 'SIL';
                $item->type_slug = 'silid';
                $item->icon = '📚';
                $item->icon_name = 'education';
                $item->title = $item->requestor_first_name . ' ' . $item->requestor_last_name;
                $item->summary = 'Schedule: ' . $item->preferred_date->format('M d, Y') . ' @ ' . $item->preferred_time;
                
                $submitted = [
                    'Full Name' => $item->requestor_first_name . ' ' . ($item->requestor_middle_name ? $item->requestor_middle_name . ' ' : '') . $item->requestor_last_name,
                    'Age' => $item->requestor_age,
                    'Email Address' => $item->email,
                    'Contact Number' => $item->contact_number,
                    'Preferred Date' => $item->preferred_date ? $item->preferred_date->format('M d, Y') : null,
                    'Preferred Time' => $item->preferred_time,
                ];
                $customFields = $item->custom_fields;
                if (is_string($customFields)) {
                    $customFields = json_decode($customFields, true);
                }
                if (is_array($customFields)) {
                    foreach ($customFields as $k => $v) {
                        $submitted[ucwords(str_replace('_', ' ', $k))] = $v;
                    }
                }
                $item->submitted_data = $submitted;
                return $item;
            });

            $sports = $sports->map(function ($item) {
                $item->type_label = 'Sports Registration';
                $item->type_prefix = 'SPO';
                $item->type_slug = 'sports';
                $item->icon = '⚽';
                $item->icon_name = 'sports';
                $item->title = $item->first_name . ' ' . $item->last_name;
                $item->summary = 'Sport: ' . $item->sport . ' (Team: ' . ($item->team_name ?? 'None') . ')';
                
                if ($item instanceof \App\Models\SportsRegistration) {
                    $submitted = [
                        'Full Name' => $item->first_name . ' ' . ($item->middle_name ? $item->middle_name . ' ' : '') . $item->last_name,
                        'Age' => $item->age,
                        'Gender' => $item->gender,
                        'Email Address' => $item->email,
                        'Contact Number' => $item->contact_number,
                        'Sport' => $item->sport,
                        'Division' => $item->division,
                        'Team Name' => $item->team_name,
                        'Event Date' => $item->event_date ? $item->event_date->format('M d, Y') : null,
                        'Remarks' => $item->remarks,
                    ];
                    $customFields = $item->custom_fields;
                    if (is_string($customFields)) {
                        $customFields = json_decode($customFields, true);
                    }
                    if (is_array($customFields)) {
                        foreach ($customFields as $k => $v) {
                            $submitted[ucwords(str_replace('_', ' ', $k))] = $v;
                        }
                    }
                } else {
                    $submitted = [
                        'Full Name' => $item->citizen_name,
                        'Email Address' => $item->citizen_email,
                    ];
                    $answers = $item->answers;
                    if (is_string($answers)) {
                        $answers = json_decode($answers, true);
                    }
                    if (is_array($answers)) {
                        foreach ($answers as $k => $v) {
                            if ($k === 'remarks') continue;
                            if (is_array($v)) {
                                $v = implode(', ', $v);
                            }
                            $submitted[ucwords(str_replace('_', ' ', $k))] = $v;
                        }
                    }
                }
                $item->submitted_data = $submitted;
                return $item;
            });

            $custom = $custom->map(function ($item) {
                $item->type_label = $item->initiative ? $item->initiative->title : 'Custom Request';
                $item->type_prefix = 'REQ';
                $item->type_slug = 'custom';
                $item->icon = '📝';
                $item->icon_name = 'forms';
                $item->title = $item->first_name . ' ' . $item->last_name;
                $item->summary = 'Form Submission for ' . ($item->initiative ? $item->initiative->title : 'Initiative');
                
                $submitted = [
                    'Full Name' => $item->first_name . ' ' . $item->last_name,
                    'Email Address' => $item->email,
                ];
                $customFields = $item->custom_fields;
                if (is_string($customFields)) {
                    $customFields = json_decode($customFields, true);
                }
                if (is_array($customFields)) {
                    foreach ($customFields as $k => $v) {
                        if (is_array($v)) {
                            $v = implode(', ', $v);
                        }
                        $submitted[ucwords(str_replace('_', ' ', $k))] = $v;
                    }
                }
                $item->submitted_data = $submitted;
                return $item;
            });

            $results = collect()
                ->concat($health)
                ->concat($medicine)
                ->concat($silid)
                ->concat($sports)
                ->concat($custom)
                ->sortByDesc('created_at');
        }

        return view('track.index', [
            'results' => $results,
            'email' => $searchQuery,
            'searched' => $searched
        ]);
    }

    /**
     * Search request status by email or reference number.
     */
    public function search(Request $request): View
    {
        $request->validate([
            'email' => ['required', 'string'],
        ]);

        $email = $request->input('email');
        session(['tracked_email' => $email]);

        return $this->index($request);
    }

    /**
     * Helper to resolve single request based on type and id
     */
    private function findRequest(string $type, $id)
    {
        return match ($type) {
            'health' => HealthRequest::findOrFail($id),
            'medicine' => MedicineRequest::findOrFail($id),
            'silid' => SilidKarununganRequest::findOrFail($id),
            'sports' => SportsRegistration::findOrFail($id),
            'custom' => CustomRequest::findOrFail($id),
            default => abort(404, 'Invalid request type')
        };
    }

    /**
     * Edit a pending request.
     */
    public function edit(string $type, $id): View
    {
        $req = $this->findRequest($type, $id);

        if ($req->status !== 'pending') {
            return abort(403, 'Only pending requests can be edited.');
        }

        $formRoute = match ($type) {
            'health' => 'forms.health.create',
            'medicine' => 'forms.medicine.create',
            'silid' => 'forms.silid.create',
            'sports' => 'forms.sports.create',
            default => null
        };
        $initiative = $formRoute ? \App\Models\Initiative::where('form_route', $formRoute)->first() : null;
        if ($type === 'custom') {
            $initiative = $req->initiative;
        }

        return view('track.edit', [
            'type' => $type,
            'req' => $req,
            'initiative' => $initiative
        ]);
    }

    /**
     * Update a pending request.
     */
    public function update(Request $request, string $type, $id)
    {
        $req = $this->findRequest($type, $id);

        if ($req->status !== 'pending') {
            return abort(403, 'Only pending requests can be updated.');
        }

        $formRoute = match ($type) {
            'health' => 'forms.health.create',
            'medicine' => 'forms.medicine.create',
            'silid' => 'forms.silid.create',
            'sports' => 'forms.sports.create',
            default => null
        };
        $initiative = $formRoute ? \App\Models\Initiative::where('form_route', $formRoute)->first() : null;
        if ($type === 'custom') {
            $initiative = $req->initiative;
        }

        $rules = match ($type) {
            'health' => [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'age' => ['required', 'integer', 'min:0', 'max:120'],
                'gender' => ['required', 'in:Male,Female,Prefer not to say'],
                'email' => ['required', 'email', 'max:255'],
                'contact_number' => ['required', 'string', 'max:20'],
                'concerns' => ['required', 'string'],
                'preferred_date' => ['required', 'date', 'after_or_equal:today'],
                'preferred_time' => ['required', 'string'],
            ],
            'medicine' => [
                'requestor_first_name' => ['required', 'string', 'max:255'],
                'requestor_last_name' => ['required', 'string', 'max:255'],
                'requestor_age' => ['required', 'integer', 'min:0', 'max:120'],
                'requestor_gender' => ['required', 'in:Male,Female,Prefer not to say'],
                'email' => ['required', 'email', 'max:255'],
                'contact_number' => ['required', 'string', 'max:20'],
                'complete_address' => ['required', 'string'],
            ],
            'silid' => [
                'requestor_first_name' => ['required', 'string', 'max:255'],
                'requestor_last_name' => ['required', 'string', 'max:255'],
                'requestor_middle_name' => ['nullable', 'string', 'max:255'],
                'requestor_age' => ['required', 'integer', 'min:0', 'max:120'],
                'email' => ['required', 'email', 'max:255'],
                'contact_number' => ['required', 'string', 'max:20'],
                'preferred_date' => ['required', 'date', 'after_or_equal:today'],
                'preferred_time' => ['required', 'string'],
            ],
            'sports' => [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'age' => ['required', 'integer', 'min:10', 'max:30'],
                'gender' => ['required', 'in:Male,Female,Prefer not to say'],
                'email' => ['required', 'email', 'max:255'],
                'contact_number' => ['required', 'string', 'max:20'],
                'sport' => ['required', 'string'],
                'team_name' => ['nullable', 'string', 'max:255'],
                'event_date' => ['required', 'date', 'after_or_equal:today'],
                'remarks' => ['nullable', 'string'],
            ],
            'custom' => [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
            ]
        };

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
        $req->update($validated);

        session(['tracked_email' => $req->email]);

        return redirect()->route('track.index')->with('success', 'Your request has been successfully updated.');
    }

    /**
     * Cancel/withdraw a pending request.
     */
    public function cancel(string $type, $id)
    {
        $req = $this->findRequest($type, $id);

        if ($req->status !== 'pending') {
            return abort(403, 'Only pending requests can be cancelled.');
        }

        $email = $req->email;
        $req->delete();

        session(['tracked_email' => $email]);

        return redirect()->route('track.index')->with('success', 'Your pending request was successfully cancelled and withdrawn.');
    }
}
