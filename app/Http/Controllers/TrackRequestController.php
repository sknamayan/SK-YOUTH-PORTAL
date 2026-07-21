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
        // Tandaan: Kung ang findRequest ay gumagamit pa rin ng mga models na tinanggal,
        // kailangan mo ring i-update ang logic sa loob ng method na iyon.
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
}
