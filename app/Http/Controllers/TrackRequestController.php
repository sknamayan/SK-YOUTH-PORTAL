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
        $email = session('tracked_email');
        $searched = !empty($email);
        $requests = collect();
        $results = collect();

        return view('track.index', compact('requests', 'results', 'email', 'searched'));
    }

    /**
     * Search for requests by email or tracking ID.
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

        $email = $req->email;
        $req->delete();

        session(['tracked_email' => $email]);

        return redirect()->route('track.index')->with('success', 'Your pending request was successfully cancelled and withdrawn.');
    }
}
