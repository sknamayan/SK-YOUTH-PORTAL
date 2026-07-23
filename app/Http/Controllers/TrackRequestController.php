<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackRequestController extends Controller
{
    /**
     * Display tracking index or search results.
     */
    public function index(Request $request): View
    {
        $referenceNumber = session('tracked_reference_number');
        $searched = !empty($referenceNumber);
        $requests = collect();
        $results = collect();

        if ($searched) {
            $referenceNumber = trim($referenceNumber);

            $match = \App\Models\CustomRequest::whereRaw('LOWER(reference_number) = ?', [strtolower($referenceNumber)])->first()
                ?? \App\Models\SportsRegistration::whereRaw('LOWER(reference_number) = ?', [strtolower($referenceNumber)])->first()
                ?? \App\Models\HealthRequest::whereRaw('LOWER(reference_number) = ?', [strtolower($referenceNumber)])->first()
                ?? \App\Models\MedicineRequest::whereRaw('LOWER(reference_number) = ?', [strtolower($referenceNumber)])->first()
                ?? \App\Models\SilidKarununganRequest::whereRaw('LOWER(reference_number) = ?', [strtolower($referenceNumber)])->first();

            if ($match) {
                $type = 'custom';
                if ($match instanceof \App\Models\SportsRegistration) {
                    $type = 'sports';
                } elseif ($match instanceof \App\Models\HealthRequest) {
                    $type = 'health';
                } elseif ($match instanceof \App\Models\MedicineRequest) {
                    $type = 'medicine';
                } elseif ($match instanceof \App\Models\SilidKarununganRequest) {
                    $type = 'silid';
                }

                $match->type_slug = $type;
                $match->type_label = match($type) {
                    'sports' => 'Sports Registration',
                    'health' => 'Health Request',
                    'medicine' => 'Medicine Request',
                    'silid' => 'Silid Karunungan Request',
                    default => object_get($match, 'type') ?? 'Custom Request',
                };

                $match->title = $match->type_label;
                
                if ($type === 'custom' && $match->initiative) {
                    $match->title = $match->initiative->title;
                }

                $match->summary = match($type) {
                    'sports' => 'Tournament registration for ' . ($match->sport ?? 'SIKLAB') . ' (' . ($match->division ?? 'General') . ')',
                    'health' => 'Health consultation booking.',
                    'medicine' => 'SK Pabili Medicine support request.',
                    'silid' => 'Silid Karunungan study space booking.',
                    default => 'Initiative Form Submission',
                };

                // Map submitted data for key-value list
                $match->submitted_data = match($type) {
                    'sports' => [
                        'First Name' => $match->first_name,
                        'Last Name' => $match->last_name,
                        'Email' => $match->email,
                        'Contact' => $match->contact_number,
                        'Sport' => $match->sport,
                        'Division' => $match->division,
                        'Team Name' => $match->team_name,
                        'Position' => $match->position,
                    ],
                    'health' => [
                        'First Name' => $match->first_name,
                        'Last Name' => $match->last_name,
                        'Email' => $match->email,
                        'Concerns' => $match->concerns,
                        'Preferred Date' => $match->preferred_date ? $match->preferred_date->format('Y-m-d') : null,
                        'Preferred Time' => $match->preferred_time,
                    ],
                    'medicine' => [
                        'Requestor Name' => $match->requestor_first_name . ' ' . $match->requestor_last_name,
                        'Email' => $match->email,
                        'Contact' => $match->contact_number,
                        'Complete Address' => $match->complete_address,
                    ],
                    'silid' => [
                        'Requestor Name' => $match->requestor_first_name . ' ' . $match->requestor_last_name,
                        'Email' => $match->email,
                        'Preferred Date' => $match->preferred_date ? $match->preferred_date->format('Y-m-d') : null,
                        'Preferred Time' => $match->preferred_time,
                    ],
                    default => [
                        'First Name' => $match->first_name,
                        'Last Name' => $match->last_name,
                        'Email' => $match->email,
                    ]
                };

                $results = collect([$match]);
            }
        }

        $requests = $results;

        return view('track.index', compact('requests', 'results', 'referenceNumber', 'searched'));
    }

    /**
     * Search for requests exclusively by reference number.
     */
    public function search(Request $request): View
    {
        $request->validate([
            'reference_number' => ['required', 'string'],
        ]);

        $referenceNumber = $request->input('reference_number');
        session(['tracked_reference_number' => $referenceNumber]);

        return $this->index($request);
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

        $referenceNumber = $req->reference_number ?? null;
        $req->delete();

        if ($referenceNumber) {
            session(['tracked_reference_number' => $referenceNumber]);
        }

        return redirect()->route('track.index')->with('success', 'Your pending request was successfully cancelled and withdrawn.');
    }

    private function findRequest(string $type, $id)
    {
        $class = match($type) {
            'custom' => \App\Models\CustomRequest::class,
            'sports' => \App\Models\SportsRegistration::class,
            'health' => \App\Models\HealthRequest::class,
            'medicine' => \App\Models\MedicineRequest::class,
            'silid' => \App\Models\SilidKarununganRequest::class,
            default => abort(404, 'Invalid request type.'),
        };

        return $class::findOrFail($id);
    }
}
