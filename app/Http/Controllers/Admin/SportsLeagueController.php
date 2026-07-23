<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportsRegistration;
use App\Models\League;
use App\Models\User;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Helpers\PrivacyHelper;
use App\Services\MailDispatchService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SportsLeagueController extends Controller
{
    public function __construct(
        private readonly MailDispatchService $mailDispatch
    ) {}

    /**
     * Display a listing of sports registrations.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $yearFilter = $request->input('year');
        $divisionFilter = $request->input('division');
        
        $limit = $request->input('limit', 15);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 15;
        }

        $query = SportsRegistration::with(['processedBy'])->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $normalized = strtolower($status);
            if ($normalized === 'pending') {
                $query->whereIn('status', ['pending', 'Pending']);
            } elseif ($normalized === 'review' || $normalized === 'under review' || $normalized === 'under_review') {
                $query->whereIn('status', ['review', 'Review', 'under review', 'Under Review']);
            } elseif ($normalized === 'approved') {
                $query->whereIn('status', ['approved', 'Approved']);
            } elseif ($normalized === 'rejected' || $normalized === 'declined') {
                $query->whereIn('status', ['rejected', 'Rejected', 'declined', 'Declined']);
            } elseif ($normalized === 'completed') {
                $query->whereIn('status', ['completed', 'Completed']);
            }
        }

        if ($yearFilter) {
            $query->whereYear('created_at', $yearFilter);
        }

        if ($divisionFilter) {
            $query->where('division', $divisionFilter);
        }

        $paginatedRequests = $query->paginate($limit)->withQueryString();

        // Mask PII on paginated records based on role clearance
        $currentUser = auth()->user();
        $paginatedRequests->getCollection()->transform(fn($item) => PrivacyHelper::filterPII($item, $currentUser));

        // Extract distinct request years
        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $years = SportsRegistration::selectRaw("strftime('%Y', created_at) as year")->distinct()->pluck('year')->toArray();
        } else {
            $years = SportsRegistration::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year')->toArray();
        }
        rsort($years);
        if (empty($years)) {
            $years = [date('Y')];
        }

        $leagues = League::with('registrationForms')->get();

        // Calculate counts for Alpine.js powered tabs
        $pendingCount = SportsRegistration::whereIn('status', ['pending', 'Pending'])->count();
        $underReviewCount = SportsRegistration::whereIn('status', ['review', 'Review', 'under review', 'Under Review'])->count();
        $approvedCount = SportsRegistration::whereIn('status', ['approved', 'Approved'])->count();
        $rejectedCount = SportsRegistration::whereIn('status', ['rejected', 'Rejected', 'declined', 'Declined'])->count();
        $completedCount = SportsRegistration::whereIn('status', ['completed', 'Completed'])->count();

        return view('admin.sports-league.index', compact(
            'paginatedRequests',
            'search',
            'status',
            'yearFilter',
            'divisionFilter',
            'limit',
            'years',
            'leagues',
            'pendingCount',
            'underReviewCount',
            'approvedCount',
            'rejectedCount',
            'completedCount'
        ));
    }

    /**
     * Show the form for creating a new sports registration.
     */
    public function create(): View
    {
        $leagues = League::where('status', 'Active')->get();
        return view('admin.sports-league.create', compact('leagues'));
    }

    /**
     * Store a newly created sports registration in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Enforce 1 citizen = 1 sports league registration constraint
        $existing = SportsRegistration::where('email', $request->input('email'))
            ->whereIn('status', ['pending', 'review', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This citizen is already registered for ' . $existing->sport . ' (' . $existing->division . ').');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:0'],
            'gender' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'sport' => ['required', 'string', 'in:Basketball,Volleyball'],
            'division' => ['required', 'string'],
            'team_name' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'health_declaration' => ['nullable', 'string'],
            'consent_waiver' => ['required', 'boolean'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'guardian_gov_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'voter_cert' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];

        if ($request->input('age') < 18) {
            $rules = array_merge($rules, [
                'guardian_first_name' => ['required', 'string', 'max:255'],
                'guardian_middle_name' => ['nullable', 'string', 'max:255'],
                'guardian_last_name' => ['required', 'string', 'max:255'],
                'guardian_age' => ['required', 'integer', 'min:18'],
                'guardian_relation' => ['required', 'string', 'max:255'],
                'guardian_contact_number' => ['required', 'string', 'max:20'],
                'guardian_address' => ['required', 'string', 'max:255'],
            ]);
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('failed_modal', 'register');
        }
        $data = $validator->validated();

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('sports/profile-pictures', 'public');
        }
        if ($request->hasFile('guardian_gov_id')) {
            $data['guardian_gov_id'] = $request->file('guardian_gov_id')->store('sports/guardian-ids', 'public');
        }
        if ($request->hasFile('voter_cert')) {
            $data['voter_cert'] = $request->file('voter_cert')->store('sports/voter-certs', 'public');
        }

        $data['event_date'] = now()->toDateString();
        $data['status'] = 'approved';
        $data['processed_by'] = auth()->id();

        $registration = SportsRegistration::create($data);

        // Audit Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sports_registration_created_by_admin',
            'subject_type' => get_class($registration),
            'subject_id' => $registration->id,
            'payload' => json_encode([
                'citizen_email' => $registration->email,
                'processed_by' => auth()->user()->email
            ])
        ]);

        return redirect()->route('admin.sports-league.index')->with('success', 'Citizen registered successfully.');
    }

    /**
     * Show individual sports registration details.
     */
    public function show($id): View
    {
        $req = SportsRegistration::with(['processedBy'])->findOrFail($id);

        if (in_array(strtolower($req->status), ['pending'])) {
            $req->status = 'review';
            $req->processed_by = auth()->id();
            $req->save();

            // Notify user
            $user = User::where('email', $req->email)->first();
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Sports Request Under Review',
                    'message' => 'Your sports registration request is now under review.',
                    'url' => route('profile.edit'),
                    'type' => 'sports_league',
                ]);
            }

            try {
                $this->mailDispatch->queueStatusChanged($req);
            } catch (\Exception $e) {
                // Silently swallow
            }
        }

        $currentUser = auth()->user();

        // Audit PII access
        if ($currentUser && $currentUser->hasPiiClearance() && strtolower($currentUser->email) !== strtolower($req->email)) {
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'pii_accessed',
                'subject_type' => get_class($req),
                'subject_id' => $req->id,
                'payload' => json_encode([
                    'accessed_by' => $currentUser->email,
                    'role' => $currentUser->role,
                    'ip_address' => request()->ip()
                ])
            ]);
        }

        $req = PrivacyHelper::filterPII($req, $currentUser);

        return view('admin.sports-league.show', compact('req'));
    }

    /**
     * Update the status of sports registration.
     */
    public function updateStatus(Request $request, $id, $status): RedirectResponse
    {
        $normalized = strtolower($status);
        if (!in_array($normalized, ['approved', 'rejected', 'declined', 'review', 'under review', 'completed', 'pending'])) {
            return back()->with('error', 'Invalid status state.');
        }

        $mappedStatus = match ($normalized) {
            'approved' => 'approved',
            'rejected', 'declined' => 'declined',
            'review', 'under review' => 'review',
            'pending' => 'pending',
            default => 'pending',
        };

        $req = SportsRegistration::findOrFail($id);
        $oldStatus = $req->status;
        $req->status = $mappedStatus;
        
        $req->remarks = $request->input('remarks', $req->remarks ?? '');
        $req->processed_by = auth()->id();
        $req->save();

        // Send Notification & Email
        $user = User::where('email', $req->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Sports Request Status: ' . $mappedStatus,
                'message' => 'Your sports registration has been marked as ' . $mappedStatus . '.',
                'url' => route('profile.edit'),
                'type' => 'sports_league',
            ]);
        }

        try {
            $this->mailDispatch->queueStatusChanged($req);
        } catch (\Exception $e) {
            // Silently swallow
        }

        // Audit Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'status_updated',
            'subject_type' => get_class($req),
            'subject_id' => $req->id,
            'payload' => json_encode([
                'old_status' => $oldStatus,
                'new_status' => $mappedStatus,
                'processed_by' => auth()->user()->email
            ])
        ]);

        return back()->with('success', 'Registration status updated to ' . $mappedStatus);
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $req = SportsRegistration::findOrFail($id);
        $req->delete();

        // Log archive action in activity log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sports_registration_archived',
            'subject_type' => get_class($req),
            'subject_id' => $req->id,
            'payload' => json_encode([
                'citizen_email' => $req->email,
                'processed_by' => auth()->user()->email
            ])
        ]);

        return redirect()->route('admin.sports-league.index')->with('success', 'Registration removed successfully.');
    }

    public function edit($id): View
    {
        $req = SportsRegistration::findOrFail($id);
        return view('admin.sports-league.edit', compact('req'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:0'],
            'gender' => ['required', 'string', 'in:Male,Female,Prefer not to say'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'sport' => ['required', 'string', 'in:Basketball,Volleyball'],
            'division' => ['required', 'string'],
            'team_name' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];

        // Conditional guardian validation if age is under 18
        if ($request->input('age') < 18) {
            $rules = array_merge($rules, [
                'guardian_first_name' => ['required', 'string', 'max:255'],
                'guardian_middle_name' => ['nullable', 'string', 'max:255'],
                'guardian_last_name' => ['required', 'string', 'max:255'],
                'guardian_age' => ['required', 'integer', 'min:18'],
                'guardian_relation' => ['required', 'string', 'max:255'],
                'guardian_contact_number' => ['required', 'string', 'max:20'],
                'guardian_address' => ['required', 'string', 'max:255'],
            ]);
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('failed_modal', 'edit')
                ->with('failed_id', $id);
        }
        $validated = $validator->validated();

        $req = SportsRegistration::findOrFail($id);
        $req->update($validated);

        // Audit Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sports_registration_updated',
            'subject_type' => get_class($req),
            'subject_id' => $req->id,
            'payload' => json_encode([
                'citizen_email' => $req->email,
                'processed_by' => auth()->user()->email
            ])
        ]);

        return redirect()->route('admin.sports-league.index')->with('success', 'Registration details updated successfully.');
    }

    public function bulkArchive(Request $request): RedirectResponse
    {
        $query = SportsRegistration::whereIn('status', ['approved', 'Approved', 'rejected', 'Rejected', 'declined', 'Declined', 'completed', 'Completed']);
        $count = $query->count();
        $query->delete();

        // Audit Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sports_registrations_bulk_archived',
            'subject_type' => SportsRegistration::class,
            'subject_id' => null,
            'payload' => json_encode([
                'count' => $count,
                'processed_by' => auth()->user()->email
            ])
        ]);

        return redirect()->route('admin.sports-league.index')->with('success', "Successfully archived {$count} processed registrations.");
    }

    /**
     * Export registration responses to CSV, respecting DPO rules.
     */
    public function export(Request $request)
    {
        $currentUser = auth()->user();
        $hasClearance = $currentUser && $currentUser->hasPiiClearance();

        $search = $request->input('search');
        $status = $request->input('status');
        $yearFilter = $request->input('year');
        $divisionFilter = $request->input('division');

        $query = SportsRegistration::with(['processedBy'])->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $normalized = strtolower($status);
            if ($normalized === 'pending') {
                $query->whereIn('status', ['pending', 'Pending']);
            } elseif ($normalized === 'review' || $normalized === 'under review' || $normalized === 'under_review') {
                $query->whereIn('status', ['review', 'Review', 'under review', 'Under Review']);
            } elseif ($normalized === 'approved') {
                $query->whereIn('status', ['approved', 'Approved']);
            } elseif ($normalized === 'rejected' || $normalized === 'declined') {
                $query->whereIn('status', ['rejected', 'Rejected', 'declined', 'Declined']);
            } elseif ($normalized === 'completed') {
                $query->whereIn('status', ['completed', 'Completed']);
            }
        }

        if ($yearFilter) {
            $query->whereYear('created_at', $yearFilter);
        }

        if ($divisionFilter) {
            $query->where('division', $divisionFilter);
        }

        $records = $query->get();

        // Log export mutation
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'sports_registrations_exported',
            'subject_type' => SportsRegistration::class,
            'subject_id' => null,
            'payload' => json_encode([
                'count' => $records->count(),
                'processed_by' => $currentUser->email,
                'pii_unmasked' => $hasClearance
            ])
        ]);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sports_registrations_export_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($records, $currentUser) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Middle Name', 'Age', 'Gender', 'Email', 'Contact Number', 'Sport', 'Team Name', 'Event Date', 'Remarks', 'Status', 'Date Submitted']);

            foreach ($records as $record) {
                // Apply RedactsPii logic if user doesn't have clearance
                $item = $record;
                if (!PrivacyHelper::canViewUnmaskedPii($currentUser, $record)) {
                    $item = PrivacyHelper::filterPII($record, $currentUser);
                }

                fputcsv($file, [
                    $item->id,
                    $item->first_name,
                    $item->last_name,
                    $item->middle_name,
                    $item->age,
                    $item->gender,
                    $item->email,
                    $item->contact_number,
                    $item->sport,
                    $item->team_name,
                    $item->event_date instanceof \Carbon\Carbon ? $item->event_date->format('Y-m-d') : $item->event_date,
                    $item->remarks,
                    $item->status,
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
