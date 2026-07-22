<?php

namespace App\Http\Controllers;

use App\Models\KkProfile;
use App\Models\Purok;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Notification;
use App\Models\User;

class KkProfileController extends Controller
{
    /**
     * Display KK Profiling Dashboard List View.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $purokFilter = $request->input('purok');
        $classFilter = $request->input('classification');
        $yearFilter = $request->input('year');
        $sexFilter = $request->input('sex');
        $skVoterFilter = $request->input('sk_voter');
        $nationalVoterFilter = $request->input('national_voter');
        $lgbtqiaFilter = $request->input('lgbtqia');
        $pwdFilter = $request->input('pwd');
        
        $limit = $request->input('limit', 15);
        if (!in_array($limit, [10, 15, 25, 50, 100])) {
            $limit = 15;
        }

        $showArchive = $request->boolean('archive', false);
        $categoryFilter = $request->input('category', 'sk_youth');
        if (!in_array($categoryFilter, ['child', 'sk_youth', 'adult', 'all'])) {
            $categoryFilter = 'sk_youth';
        }

        if ($showArchive) {
            $query = KkProfile::withoutGlobalScopes()
                ->withTrashed()
                ->where(function($q) {
                    $q->whereNotNull('deleted_at')
                      ->orWhere('age', '>', 30)
                      ->orWhere('category', '!=', 'sk_youth');
                })
                ->with(['purok', 'processedBy'])->latest();
        } else {
            $query = KkProfile::withoutGlobalScopes()->with(['purok', 'processedBy'])->latest();

            if ($categoryFilter === 'all') {
                // No category filter, just active
            } elseif ($categoryFilter === 'child') {
                $query->where('category', 'child')->whereBetween('age', [6, 14]);
            } elseif ($categoryFilter === 'adult') {
                $query->where('category', 'adult')->whereBetween('age', [31, 39]);
            } else { // default 'sk_youth'
                $query->where('category', 'sk_youth')->whereBetween('age', [15, 30]);
            }
        }

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('surname', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // Purok Filter
        if ($purokFilter) {
            $query->where('purok_id', $purokFilter);
        }

        // Youth Classification Filter
        if ($classFilter) {
            $query->where('youth_classification', $classFilter);
        }

        // Registration Year Filter
        if ($yearFilter) {
            $query->whereYear('created_at', $yearFilter);
        }

        // Sex Filter
        if ($sexFilter && in_array($sexFilter, ['Male', 'Female'])) {
            $query->where('sex', $sexFilter);
        }

        // Voter status
        if ($skVoterFilter !== null && $skVoterFilter !== '') {
            $query->where('registered_sk_voter', (bool) $skVoterFilter);
        }
        if ($nationalVoterFilter !== null && $nationalVoterFilter !== '') {
            $query->where('registered_national_voter', (bool) $nationalVoterFilter);
        }

        // LGBTQIA Community Filter
        if ($lgbtqiaFilter !== null && $lgbtqiaFilter !== '') {
            $query->where('part_of_lgbtqia', (bool) $lgbtqiaFilter);
        }

        // PWD Status
        if ($pwdFilter !== null && $pwdFilter !== '') {
            $query->where('pwd', (bool) $pwdFilter);
        }

        $statusFilter = $request->input('status', 'all');
        if (!in_array($statusFilter, ['approved', 'pending', 'declined', 'all'])) {
            $statusFilter = 'all';
        }

        $baseCountQuery = KkProfile::withoutGlobalScopes();
        if ($categoryFilter === 'all') {
            // No filter
        } elseif ($categoryFilter === 'child') {
            $baseCountQuery->where('category', 'child')->whereBetween('age', [6, 14]);
        } elseif ($categoryFilter === 'adult') {
            $baseCountQuery->where('category', 'adult')->whereBetween('age', [31, 39]);
        } else { // default 'sk_youth'
            $baseCountQuery->where('category', 'sk_youth')->whereBetween('age', [15, 30]);
        }

        $approvedCount = (clone $baseCountQuery)->where('status', 'approved')->count();
        $pendingCount = (clone $baseCountQuery)->where('status', 'pending')->count();
        $declinedCount = (clone $baseCountQuery)->where('status', 'declined')->count();
        $archivedCount = KkProfile::withoutGlobalScopes()
            ->withTrashed()
            ->where(function($q) {
                $q->whereNotNull('deleted_at')
                  ->orWhere('age', '>', 30)
                  ->orWhere('category', '!=', 'sk_youth');
            })->count();

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $profiles = $query->paginate($limit)->withQueryString();
        $puroks = Purok::orderBy('purok_name')->get();

        // Extract distinct registration years database-agnostically
        $driver = \DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $years = KkProfile::selectRaw("strftime('%Y', created_at) as year")
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        } else {
            $years = KkProfile::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        }
        if (empty($years)) {
            $years = [date('Y')];
        }

        $historyLogs = ActivityLog::with('user')
            ->where(function ($q) {
                $q->where('subject_type', KkProfile::class)
                  ->orWhere('action', 'like', 'kk_profile_%');
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.profiling.index', compact(
            'profiles', 'puroks', 'search', 'purokFilter', 'classFilter',
            'yearFilter', 'sexFilter', 'skVoterFilter', 'nationalVoterFilter',
            'lgbtqiaFilter', 'pwdFilter', 'limit', 'years', 'historyLogs',
            'statusFilter', 'approvedCount', 'pendingCount', 'declinedCount',
            'showArchive', 'archivedCount', 'categoryFilter'
        ));
    }

    /**
     * Store new KK Profile.
     */
    public function store(Request $request)
    {
        $this->prepareInputsForUppercase($request);

        $validated = $request->validate([
            // Step 1: Personal Details
            'surname' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['required', 'string', 'max:50'],
            'ext' => ['nullable', 'string', 'max:10'],
            'age' => ['required', 'integer', 'min:6', 'max:39'],
            'sex' => ['required', 'in:Male,Female'],
            'gender' => ['nullable', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before_or_equal:today'],
            'civil_status' => ['required', 'in:Single,Married,Widowed,Divorced,Separated'],
            'purok_id' => ['required', 'exists:puroks,id'],
            'street_address' => ['nullable', 'string', 'max:500'],
            'youth_classification' => ['required', 'in:ISY,OSY,WY'],
            'contact_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],

            // Step 2: Affiliations
            'registered_sk_voter' => ['required', 'boolean'],
            'registered_national_voter' => ['required', 'boolean'],
            'attended_kk_assembly' => ['required', 'boolean'],
            'part_of_youth_org' => ['required', 'boolean'],
            'youth_org_name' => ['required_if:part_of_youth_org,1', 'nullable', 'string', 'max:255'],
            'interested_in_joining' => ['required', 'boolean'],

            // Step 3: Inclusivity & Education
            'part_of_lgbtqia' => ['required', 'boolean'],
            'pwd' => ['required', 'boolean'],
            'registered_disability' => ['required_if:pwd,1', 'nullable', 'string', 'max:255'],
            'highest_educational_attainment' => ['required', 'string', 'max:255'],
            'consent_given' => ['required', 'accepted'],
        ]);

        $age = (int) $validated['age'];
        if ($age >= 6 && $age <= 14) {
            $category = 'child';
        } elseif ($age >= 31 && $age <= 39) {
            $category = 'adult';
        } else {
            $category = 'sk_youth';
        }

        $profile = KkProfile::create(array_merge($validated, [
            'processed_by' => auth()->id(),
            'category' => $category,
        ]));

        // Record Activity Log
        ActivityLog::record('kk_profile_created', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
        ]);

        return redirect()->route('dashboard.profiling.index')
            ->with('success', 'Katipunan ng Kabataan profile has been successfully added.');
    }

    /**
     * Display self-profiling form for citizens.
     */
    public function selfCreate(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Check if citizen has already completed profiling
        $existingProfile = KkProfile::withoutGlobalScopes()
            ->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('email', auth()->user()->email);
            })
            ->first();
        if ($existingProfile && $existingProfile->status !== 'declined') {
            return redirect()->route('profile.my-requests')
                ->with('info', 'Your Katipunan ng Kabataan profile is already registered.');
        }

        $puroks = Purok::orderBy('purok_name')->get();
        $user = auth()->user();

        return view('profile.self-profiling', compact('puroks', 'user'));
    }

    /**
     * Store self-profiling registry entry for citizens.
     */
    public function selfStore(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('KK Profiling selfStore endpoint hit', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'input_keys' => array_keys($request->all()),
        ]);

        // Check if citizen has already completed profiling
        $existingProfile = KkProfile::where('user_id', auth()->id())
            ->orWhere('email', auth()->user()->email)
            ->first();
        if ($existingProfile) {
            if ($existingProfile->status === 'declined') {
                $existingProfile->delete();
            } else {
                return redirect()->route('profile.my-requests')
                    ->with('info', 'Your Katipunan ng Kabataan profile is already registered.');
            }
        }

        $this->prepareInputsForUppercase($request);

        try {
            $validated = $request->validate([
                // Step 1: Personal Details
                'surname' => ['required', 'string', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['required', 'string', 'max:50'],
                'ext' => ['nullable', 'string', 'max:10'],
                'age' => ['required', 'integer', 'min:6', 'max:39'],
                'sex' => ['required', 'in:Male,Female'],
                'gender' => ['nullable', 'string', 'max:255'],
                'dob' => ['required', 'date', 'before_or_equal:today'],
                'civil_status' => ['required', 'in:Single,Married,Widowed,Divorced,Separated'],
                'purok_id' => ['required', 'exists:puroks,id'],
                'street_address' => ['nullable', 'string', 'max:500'],
                'youth_classification' => ['required', 'in:ISY,OSY,WY'],
                'contact_number' => ['required', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],

                // Step 2: Affiliations
                'registered_sk_voter' => ['required', 'boolean'],
                'registered_national_voter' => ['required', 'boolean'],
                'attended_kk_assembly' => ['required', 'boolean'],
                'part_of_youth_org' => ['required', 'boolean'],
                'youth_org_name' => ['required_if:part_of_youth_org,1', 'nullable', 'string', 'max:255'],
                'interested_in_joining' => ['required', 'boolean'],

                // Step 3: Inclusivity & Education
                'part_of_lgbtqia' => ['required', 'boolean'],
                'pwd' => ['required', 'boolean'],
                'registered_disability' => ['required_if:pwd,1', 'nullable', 'string', 'max:255'],
                'highest_educational_attainment' => ['required', 'string', 'max:255'],
                'consent_given' => ['required', 'accepted'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('KK Profiling selfStore Validation Failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        // Force user's own email to prevent spoofing
        $validated['email'] = auth()->user()->email;

        $age = (int) $validated['age'];
        if ($age >= 6 && $age <= 14) {
            $category = 'child';
        } elseif ($age >= 31 && $age <= 39) {
            $category = 'adult';
        } else {
            $category = 'sk_youth';
        }

        $profile = KkProfile::create(array_merge($validated, [
            'processed_by' => auth()->id(),
            'user_id' => auth()->id(),
            'status' => 'pending',
            'category' => $category,
        ]));

        \Illuminate\Support\Facades\Log::info('KK Profiling Created Successfully', ['profile_id' => $profile->id]);

        // Record Activity Log
        ActivityLog::record('kk_profile_created', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
            'self_profiled' => true
        ], auth()->id());

        return redirect()->route('profile.my-requests')
            ->with('success', 'Your Katipunan ng Kabataan profile has been successfully registered!');
    }

    /**
     * Update an existing KK Profile.
     */
    public function update(Request $request, KkProfile $profile)
    {
        $data = $request->all();

        if (empty($data['dob'])) {
            $data['dob'] = $profile->dob ? $profile->dob->format('Y-m-d') : null;
        }
        if (empty($data['contact_number'])) {
            $data['contact_number'] = $profile->contact_number;
        }
        if (empty($data['email'])) {
            $data['email'] = $profile->email;
        }
        if ($data['pwd'] === '' || $data['pwd'] === null) {
            $data['pwd'] = $profile->pwd ? 1 : 0;
            if (empty($data['registered_disability'])) {
                $data['registered_disability'] = $profile->registered_disability;
            }
        }

        $request->merge($data);

        $this->prepareInputsForUppercase($request);

        $validated = $request->validate([
            // Step 1: Personal Details
            'surname' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['required', 'string', 'max:50'],
            'ext' => ['nullable', 'string', 'max:10'],
            'age' => ['required', 'integer', 'min:6', 'max:39'],
            'sex' => ['required', 'in:Male,Female'],
            'gender' => ['nullable', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before_or_equal:today'],
            'civil_status' => ['required', 'in:Single,Married,Widowed,Divorced,Separated'],
            'purok_id' => ['required', 'exists:puroks,id'],
            'street_address' => ['nullable', 'string', 'max:500'],
            'youth_classification' => ['required', 'in:ISY,OSY,WY'],
            'contact_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],

            // Step 2: Affiliations
            'registered_sk_voter' => ['required', 'boolean'],
            'registered_national_voter' => ['required', 'boolean'],
            'attended_kk_assembly' => ['required', 'boolean'],
            'part_of_youth_org' => ['required', 'boolean'],
            'youth_org_name' => ['required_if:part_of_youth_org,1', 'nullable', 'string', 'max:255'],
            'interested_in_joining' => ['required', 'boolean'],

            // Step 3: Inclusivity & Education
            'part_of_lgbtqia' => ['required', 'boolean'],
            'pwd' => ['required', 'boolean'],
            'registered_disability' => ['required_if:pwd,1', 'nullable', 'string', 'max:255'],
            'highest_educational_attainment' => ['required', 'string', 'max:255'],
            'consent_given' => ['required', 'boolean'],
        ]);

        $age = (int) $validated['age'];
        if ($age >= 6 && $age <= 14) {
            $category = 'child';
        } elseif ($age >= 31 && $age <= 39) {
            $category = 'adult';
        } else {
            $category = 'sk_youth';
        }

        $profile->update(array_merge($validated, [
            'processed_by' => auth()->id(),
            'category' => $category,
        ]));

        // Record Activity Log
        ActivityLog::record('kk_profile_updated', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
        ]);

        return redirect()->route('dashboard.profiling.index')
            ->with('success', 'Katipunan ng Kabataan profile has been successfully updated.');
    }

    /**
     * Approve a pending KK Profile.
     */
    public function approve(KkProfile $profile)
    {
        $profile->update(['status' => 'approved']);

        ActivityLog::record('kk_profile_updated', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
            'status' => 'approved',
        ]);

        // Notify user
        $user = User::where('email', $profile->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'KK Profile Approved',
                'message' => 'Your Katipunan ng Kabataan profile registry has been approved. All service requests are now unlocked.',
                'url' => route('profile.my-requests')
            ]);
        }

        return redirect()->back()->with('success', 'Katipunan ng Kabataan profile has been successfully approved.');
    }

    /**
     * Decline a pending KK Profile.
     */
    public function decline(KkProfile $profile)
    {
        $profile->update(['status' => 'declined']);

        ActivityLog::record('kk_profile_updated', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
            'status' => 'declined',
        ]);

        // Notify user
        $user = User::where('email', $profile->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'KK Profile Declined',
                'message' => 'Your Katipunan ng Kabataan profile registry has been declined. Please re-submit your details.',
                'url' => route('profile.my-requests')
            ]);
        }

        return redirect()->back()->with('success', 'Katipunan ng Kabataan profile has been successfully declined.');
    }

    /**
     * Delete a KK Profile.
     */
    public function destroy(KkProfile $profile)
    {
        // Record Activity Log BEFORE delete to capture profile details
        ActivityLog::record('kk_profile_deleted', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
        ]);

        $profile->delete();

        return redirect()->route('dashboard.profiling.index')
            ->with('success', 'Katipunan ng Kabataan profile has been successfully deleted.');
    }

    /**
     * Restore a deleted KK Profile.
     */
    public function restore($id)
    {
        $profile = KkProfile::onlyTrashed()->findOrFail($id);
        $profile->restore();

        ActivityLog::record('kk_profile_restored', $profile, [
            'name' => $profile->full_name,
            'email' => $profile->email,
        ]);

        return redirect()->route('dashboard.profiling.index', ['archive' => '1'])
            ->with('success', 'Katipunan ng Kabataan profile has been successfully restored.');
     }

    /**
     * Transform text fields to uppercase in request data.
     */
    private function prepareInputsForUppercase(Request $request): void
    {
        $fields = [
            'surname',
            'first_name',
            'middle_name',
            'ext',
            'gender',
            'street_address',
            'youth_org_name',
            'registered_disability',
            'highest_educational_attainment',
        ];

        $input = $request->all();
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);
                if (is_string($value)) {
                    $input[$field] = mb_strtoupper($value, 'UTF-8');
                }
            }
        }
        $request->merge($input);
    }
}

