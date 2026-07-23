<?php

namespace App\Http\Controllers;

use App\Helpers\PrivacyHelper;
use App\Models\ActivityLog;
use App\Services\RequestManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RequestManagementService $requestManagement
    ) {}

    /**
     * Show admin dashboard overview.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
 
        if ($user->canAccessDashboard()) {
            $totalYouth = \App\Models\KkProfile::count();
            $totalIsy = \App\Models\KkProfile::where('youth_classification', 'ISY')->count();
            $totalOsy = \App\Models\KkProfile::where('youth_classification', 'OSY')->count();
            $totalWy = \App\Models\KkProfile::where('youth_classification', 'WY')->count();
            $totalSkVoters = \App\Models\KkProfile::where('registered_sk_voter', true)->count();
 
            $puroksData = \App\Models\Purok::withCount('kkProfiles')->orderBy('purok_name')->get();
 
            $chartData = $puroksData->map(fn ($purok) => [
                'purok' => $purok->purok_name,
                'count' => $purok->kk_profiles_count,
            ])->toArray();
 
            $classificationDistribution = [
                'isy' => $totalIsy,
                'osy' => $totalOsy,
                'wy' => $totalWy,
            ];
 
            $accomplishedByProgram = [
                'health' => \App\Models\HealthRequest::where('status', 'approved')->count(),
                'medicine' => \App\Models\MedicineRequest::where('status', 'approved')->count(),
                'silid' => \App\Models\SilidKarununganRequest::where('status', 'approved')->count(),
                'sports' => \App\Models\SportsRegistration::where('status', 'approved')->count(),
            ];
 
            $accomplishmentTrends = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $year = $date->year;
                $month = $date->month;
                $label = $date->format('M Y');
 
                $healthCount = \App\Models\HealthRequest::where('status', 'approved')
                     ->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                $medicineCount = \App\Models\MedicineRequest::where('status', 'approved')
                     ->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                $silidCount = \App\Models\SilidKarununganRequest::where('status', 'approved')
                     ->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                $sportsCount = \App\Models\SportsRegistration::where('status', 'approved')
                     ->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
 
                $accomplishmentTrends[] = [
                    'label' => $label,
                    'count' => $healthCount + $medicineCount + $silidCount + $sportsCount,
                ];
            }
 
            return view('dashboard.admin-index', compact(
                'totalYouth',
                'totalIsy',
                'totalOsy',
                'totalWy',
                'totalSkVoters',
                'chartData',
                'classificationDistribution',
                'accomplishedByProgram',
                'accomplishmentTrends'
            ));
        }
 
        // Citizen Dashboard: redirect to my-requests page directly
        return redirect()->route('profile.my-requests');
    }

    /**
     * Show citizen requests list (database-level UNION pagination).
     */
    public function requestsIndex(Request $request): View
    {
        return view('dashboard.requests-index', $this->requestManagement->buildRequestsIndexData($request));
    }

    /**
     * Show individual request details.
     */
    public function show(string $type, int $id): View
    {
        $rawRequestModel = $this->requestManagement->resolveModel($type, $id)->load(['processedBy', 'comments.user']);

        $this->requestManagement->markUnderReviewIfPending($rawRequestModel, $type);

        $currentUser = auth()->user();

        if ($currentUser && $currentUser->hasPiiClearance() && strtolower($currentUser->email) !== strtolower($rawRequestModel->email)) {
            ActivityLog::record('pii_accessed', $rawRequestModel, [
                'accessed_by' => $currentUser->email,
                'role' => $currentUser->role,
            ]);
        }

        $requestModel = PrivacyHelper::filterPII($rawRequestModel, $currentUser);

        $logs = ActivityLog::where('subject_type', get_class($rawRequestModel))
            ->where('subject_id', $id)
            ->with('user')
            ->oldest()
            ->get();

        return view('dashboard.show', [
            'req' => $requestModel,
            'type' => $type,
            'typeName' => $this->requestManagement->resolveTypeName($type),
            'basename' => class_basename($rawRequestModel),
            'logs' => $logs,
            'comments' => $rawRequestModel->comments()->with('user')->oldest()->get(),
        ]);
    }

    /**
     * Update request status.
     */
    public function updateStatus(string $type, int $id, string $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'review', 'approved', 'declined'])) {
            return back()->with('error', 'Invalid status state.');
        }

        $this->requestManagement->updateStatus($type, $id, $status);

        return redirect()->route('dashboard.requests.show', [$type, $id])
            ->with('success', 'Request status updated successfully to ' . ucfirst($status) . '.');
    }
}
