<?php

use App\Http\Controllers\Admin\AccomplishmentReportController;
use App\Http\Controllers\Admin\OfficialController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\TransparencyPostController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\StructureManagementController;
use App\Http\Controllers\Admin\UnifiedAuditLogController;
use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\Admin\CarouselSlideController;
use App\Http\Controllers\Admin\SportsLeagueController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\GovernanceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectExplorerController;
use App\Http\Controllers\TrackRequestController;
use App\Http\Controllers\KkProfileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Static routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/about', function() { return view('about.index'); })->name('about');
Route::get('/confirmation', [ConfirmationController::class, 'show'])->name('confirmation');
Route::get('/news', [LandingController::class, 'newsIndex'])->name('news.index');
Route::get('/news/{slug}', [LandingController::class, 'showNews'])->name('news.show');

// Helper to clear config cache and run database migrations in production hosting
Route::get('/clear-cache', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Artisan migrate call skipped in clear-cache: ' . $e->getMessage());
        }

        // Ensure active user's KK Profile is explicitly linked and marked approved in DB
        $user = auth()->user();
        if ($user) {
            \App\Models\KkProfile::withoutGlobalScopes()
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('email', $user->email);
                })
                ->update([
                    'user_id' => $user->id,
                    'status' => 'approved'
                ]);
        }

        // Verify schema details for otp_code column
        $columnType = 'unknown';
        try {
            $columnType = \Illuminate\Support\Facades\Schema::getColumnType('users', 'otp_code');
        } catch (\Throwable $e) {
            $columnType = 'error: ' . $e->getMessage();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Laravel configuration, route, and view caches cleared successfully!',
            'schema_verification' => [
                'table' => 'users',
                'column' => 'otp_code',
                'detected_type' => $columnType,
                'status' => in_array($columnType, ['string', 'varchar', 'text']) ? 'HEALTHY (Ready for 60-char Bcrypt OTP)' : 'ATTENTION REQUIRED',
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});


Route::get('/officials', [GovernanceController::class, 'officialsIndex'])->name('officials.index');
Route::get('/officials/{slug}', [GovernanceController::class, 'officialShow'])->name('officials.show');
Route::get('/transparency', [GovernanceController::class, 'transparencyIndex'])->name('transparency.index');
Route::get('/transparency/{slug}', [GovernanceController::class, 'transparencyShow'])->name('transparency.show');

Route::get('/projects/{project_slug}/committees/{committee_slug}', [ProjectExplorerController::class, 'showCommittee'])->name('projects.committee');
Route::get('/projects/{project_slug}/committees/{committee_slug}/initiatives/{initiative_id}', [ProjectExplorerController::class, 'show'])->name('projects.explorer');
Route::get('/projects', function() {
    return redirect()->route('projects.committee', [
        'project_slug' => 'sk-namayan-youth-services',
        'committee_slug' => 'education'
    ]);
})->name('projects.index');


// Tracking Requests
Route::get('/track', [TrackRequestController::class, 'index'])->name('track.index');
Route::post('/track', [TrackRequestController::class, 'search'])->name('track.search');
Route::get('/track/{type}/{id}/edit', [TrackRequestController::class, 'edit'])->middleware('idor.prevent')->name('track.edit');
Route::put('/track/{type}/{id}', [TrackRequestController::class, 'update'])->middleware('idor.prevent')->name('track.update');
Route::delete('/track/{type}/{id}', [TrackRequestController::class, 'cancel'])->middleware('idor.prevent')->name('track.cancel');


Route::middleware(['auth', 'throttle:forms'])->group(function () {
    Route::get('/forms/initiative/{initiative}', [\App\Http\Controllers\CustomRequestController::class, 'create'])->name('forms.custom.create');
    Route::post('/forms/initiative/{initiative}', [\App\Http\Controllers\CustomRequestController::class, 'store'])->name('forms.custom.store');

    Route::get('/forms/health-consultation', function() {
        return redirect()->route('landing', ['form' => 'health']);
    })->name('forms.health.create');
    Route::post('/forms/health-consultation', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string',
            'concerns' => 'required|string',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string',
            'custom_fields' => 'nullable|array',
        ]);

        $req = \App\Models\HealthRequest::create([
            'user_id' => auth()->id(),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'concerns' => $validated['concerns'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'custom_fields' => $validated['custom_fields'] ?? [],
            'status' => 'pending',
        ]);

        $referenceNumber = $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT));

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Health Consultation',
            'referenceNumber' => $referenceNumber,
            'name' => $req->first_name . ' ' . $req->last_name,
            'email' => $req->email,
            'detail' => 'Consultation date: ' . (\Carbon\Carbon::parse($req->preferred_date)->format('Y-m-d')),
            'date' => $req->created_at->format('M d, Y h:i A'),
        ]);
    })->name('forms.health.store');

    Route::get('/forms/mental-health', function() {
        return redirect()->route('landing', ['form' => 'mental-health']);
    })->name('forms.mental-health.create');
    Route::post('/forms/mental-health', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string',
            'concerns' => 'required|string',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string',
            'custom_fields' => 'nullable|array',
        ]);

        $req = \App\Models\HealthRequest::create([
            'user_id' => auth()->id(),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'concerns' => '[Mental Health] ' . $validated['concerns'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'custom_fields' => $validated['custom_fields'] ?? [],
            'status' => 'pending',
        ]);

        $referenceNumber = $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT));

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Mental Health Consultation',
            'referenceNumber' => $referenceNumber,
            'name' => $req->first_name . ' ' . $req->last_name,
            'email' => $req->email,
            'detail' => 'Consultation date: ' . (\Carbon\Carbon::parse($req->preferred_date)->format('Y-m-d')),
            'date' => $req->created_at->format('M d, Y h:i A'),
        ]);
    })->name('forms.mental-health.store');

    Route::get('/forms/pabili-medicine', function() {
        return redirect()->route('landing', ['form' => 'medicine']);
    })->name('forms.medicine.create');
    Route::post('/forms/pabili-medicine', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string',
            'complete_address' => 'required|string',
            'custom_fields' => 'nullable|array',
        ]);

        $req = \App\Models\MedicineRequest::create([
            'user_id' => auth()->id(),
            'requestor_first_name' => $validated['first_name'],
            'requestor_last_name' => $validated['last_name'],
            'requestor_age' => $validated['age'],
            'requestor_gender' => $validated['gender'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'complete_address' => $validated['complete_address'],
            'custom_fields' => $validated['custom_fields'] ?? [],
            'status' => 'pending',
        ]);

        $referenceNumber = $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT));

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Medicine Request',
            'referenceNumber' => $referenceNumber,
            'name' => $req->requestor_first_name . ' ' . $req->requestor_last_name,
            'email' => $req->email,
            'detail' => 'Address: ' . $req->complete_address,
            'date' => $req->created_at->format('M d, Y h:i A'),
        ]);
    })->name('forms.medicine.store');

    Route::get('/forms/silid-karunungan', function() {
        return redirect()->route('landing', ['form' => 'silid']);
    })->name('forms.silid.create');
    Route::post('/forms/silid-karunungan', function(\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'age' => 'required|integer|min:0',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string',
            'custom_fields' => 'nullable|array',
        ]);

        $req = \App\Models\SilidKarununganRequest::create([
            'user_id' => auth()->id(),
            'requestor_first_name' => $validated['first_name'],
            'requestor_last_name' => $validated['last_name'],
            'requestor_middle_name' => $validated['middle_name'] ?? null,
            'requestor_age' => $validated['age'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'custom_fields' => $validated['custom_fields'] ?? [],
            'status' => 'pending',
        ]);

        $referenceNumber = $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT));

        return redirect()->route('landing')->with([
            'submitted_success' => true,
            'type' => 'Silid Karunungan Request',
            'referenceNumber' => $referenceNumber,
            'name' => $req->requestor_first_name . ' ' . $req->requestor_last_name,
            'email' => $req->email,
            'detail' => 'Preferred Date: ' . (\Carbon\Carbon::parse($req->preferred_date)->format('Y-m-d')) . ' | Time: ' . $req->preferred_time,
            'date' => $req->created_at->format('M d, Y h:i A'),
        ]);
    })->name('forms.silid.store');

    Route::get('/api/silid-karunungan/booked-slots', function (\Illuminate\Http\Request $request) {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['booked_slots' => []]);
        }

        $bookedSlots = \App\Models\SilidKarununganRequest::where('preferred_date', $date)
            ->whereIn('status', ['pending', 'approved', 'review', 'confirmed'])
            ->pluck('preferred_time')
            ->toArray();

        return response()->json([
            'booked_slots' => array_values(array_unique($bookedSlots))
        ]);
    })->name('api.silid.booked-slots');

    Route::get('/forms/sports-registration', [\App\Http\Controllers\SportsRegistrationController::class, 'showRegistrationForm'])->name('forms.sports.create');
    Route::post('/forms/sports-registration', [\App\Http\Controllers\SportsRegistrationController::class, 'submitRegistration'])->name('forms.sports.store');
});

// Authenticated User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/attachments/{path}', [\App\Http\Controllers\AttachmentController::class, 'download'])
        ->name('attachments.download')
        ->where('path', '.*');

    Route::get('/profile/my-requests', [ProfileController::class, 'myRequests'])->name('profile.my-requests');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.update-preferences');
    Route::post('/profile/sessions/logout-others', [ProfileController::class, 'logoutOtherSessions'])->name('profile.logout-other-sessions');
    Route::get('/profile/download-data', [ProfileController::class, 'downloadData'])->name('profile.download-data');
    Route::post('/locale/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'fil'])) {
            session(['locale' => $locale]);
            if (auth()->check()) {
                auth()->user()->update(['language' => $locale]);
            }
        }
        return back();
    })->name('locale.switch');

    // Citizen Self-Profiling
    Route::get('/profile/profiling', [KkProfileController::class, 'selfCreate'])->name('profile.profiling.create');
    Route::post('/profile/profiling', [KkProfileController::class, 'selfStore'])->name('profile.profiling.store');

    // Citizen SKonsulta Consultation Threads API
    Route::get('/skonsulta/api/threads', [\App\Http\Controllers\Admin\ConsultationController::class, 'getThreadsJson'])->name('skonsulta.api.threads');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // Shared Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/index', [DashboardController::class, 'index'])->name('dashboard.index');
 
    // Citizen SKONSULTA (Pivoted Complaint Chat)
    Route::middleware('kk.profile.completed')->group(function () {
        Route::get('/skonsulta', [App\Http\Controllers\Admin\ConsultationController::class, 'citizenIndex'])->name('skonsulta.index');
        Route::post('/skonsulta', [App\Http\Controllers\Admin\ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/skonsulta/{consultation}', [App\Http\Controllers\Admin\ConsultationController::class, 'show'])->name('skonsulta.show');
        Route::get('/skonsulta/{consultation}/messages', [App\Http\Controllers\Admin\ConsultationController::class, 'getMessages'])->name('skonsulta.messages');
        Route::post('/skonsulta/{consultation}/messages', [App\Http\Controllers\Admin\ConsultationController::class, 'sendMessage'])->name('skonsulta.send-message');
        Route::get('/skonsulta/api/threads', [App\Http\Controllers\Admin\ConsultationController::class, 'getThreadsJson'])->name('skonsulta.api.threads');
        Route::get('/skonsulta/api/citizen-requests', [App\Http\Controllers\Admin\ConsultationController::class, 'citizenRequests'])->name('skonsulta.api.citizen-requests');
    });
});

// Dashboard (Middleware: auth, admin.staff)
Route::middleware(['auth', 'admin.staff'])->group(function () {
    Route::get('/dashboard/requests', [DashboardController::class, 'requestsIndex'])->name('dashboard.requests.index');
    Route::get('/dashboard/requests/{type}/{id}', [DashboardController::class, 'show'])->name('dashboard.requests.show');
    Route::patch('/dashboard/requests/{type}/{id}/status/{status}', [DashboardController::class, 'updateStatus'])->name('dashboard.requests.status');
    Route::post('/dashboard/requests/{type}/{id}/comments', [CommentController::class, 'store'])->name('dashboard.requests.comments.store');
    Route::get('/dashboard/requests/{type}/{id}/comments/{comment}/attachment', [CommentController::class, 'downloadAttachment'])->name('dashboard.requests.comments.attachment');
    Route::get('/dashboard/export/{type}', [ExportController::class, 'export'])->name('dashboard.export');
    Route::get('/dashboard/calendar', [CalendarController::class, 'index'])->name('dashboard.calendar.index');
    Route::get('/dashboard/calendar/events', [CalendarController::class, 'events'])->name('dashboard.calendar.events');
    Route::post('/dashboard/calendar/events', [CalendarController::class, 'store'])->name('dashboard.calendar.events.store');
    Route::put('/dashboard/calendar/events/{event}', [CalendarController::class, 'update'])->name('dashboard.calendar.events.update');
    Route::delete('/dashboard/calendar/events/{event}', [CalendarController::class, 'destroy'])->name('dashboard.calendar.events.destroy');

    // KK Profiling Routes
    Route::get('/dashboard/profiling', [KkProfileController::class, 'index'])->name('dashboard.profiling.index');
    Route::post('/dashboard/profiling', [KkProfileController::class, 'store'])->name('dashboard.profiling.store');
    Route::put('/dashboard/profiling/{profile}', [KkProfileController::class, 'update'])->name('dashboard.profiling.update');
    Route::delete('/dashboard/profiling/{profile}', [KkProfileController::class, 'destroy'])->name('dashboard.profiling.destroy');
    Route::post('/dashboard/profiling/{id}/restore', [KkProfileController::class, 'restore'])->name('dashboard.profiling.restore');
    Route::patch('/dashboard/profiling/{profile}/approve', [KkProfileController::class, 'approve'])->name('dashboard.profiling.approve');
    Route::patch('/dashboard/profiling/{profile}/decline', [KkProfileController::class, 'decline'])->name('dashboard.profiling.decline');

    // Consultation Management (SKONSULTA)
    Route::get('/dashboard/consultations', [App\Http\Controllers\Admin\ConsultationController::class, 'index'])->name('admin.consultations.index');
    Route::get('/dashboard/consultations/{consultation}', [App\Http\Controllers\Admin\ConsultationController::class, 'show'])->name('admin.consultations.show');
    Route::patch('/dashboard/consultations/{consultation}/status', [App\Http\Controllers\Admin\ConsultationController::class, 'updateStatus'])->name('admin.consultations.update-status');
});

// Admin and Superadmin Actions (Middleware: auth, admin.dpo)
Route::middleware(['auth', 'admin.dpo'])->group(function () {
    // Accomplishment Reports management
    Route::resource('/admin/reports', AccomplishmentReportController::class)->names([
        'index' => 'admin.reports.index',
        'create' => 'admin.reports.create',
        'store' => 'admin.reports.store',
        'edit' => 'admin.reports.edit',
        'update' => 'admin.reports.update',
        'destroy' => 'admin.reports.destroy',
    ])->except(['show']);

    // News Articles management
    Route::resource('/admin/news', NewsController::class)->names([
        'index' => 'admin.news.index',
        'create' => 'admin.news.create',
        'store' => 'admin.news.store',
        'edit' => 'admin.news.edit',
        'update' => 'admin.news.update',
        'destroy' => 'admin.news.destroy',
    ])->except(['show']);

    // SK Officials profiles
    Route::resource('/admin/officials', OfficialController::class)->names([
        'index' => 'admin.officials.index',
        'create' => 'admin.officials.create',
        'store' => 'admin.officials.store',
        'edit' => 'admin.officials.edit',
        'update' => 'admin.officials.update',
        'destroy' => 'admin.officials.destroy',
    ])->except(['show']);

    // Transparency board posts
    Route::resource('/admin/transparency', TransparencyPostController::class)->names([
        'index' => 'admin.transparency.index',
        'create' => 'admin.transparency.create',
        'store' => 'admin.transparency.store',
        'edit' => 'admin.transparency.edit',
        'update' => 'admin.transparency.update',
        'destroy' => 'admin.transparency.destroy',
    ])->except(['show']);

    // Sports League Management
    Route::get('/admin/sports-league', [SportsLeagueController::class, 'index'])->name('admin.sports-league.index');
    Route::get('/admin/sports-league/export', [SportsLeagueController::class, 'export'])->name('admin.sports-league.export');
    Route::post('/admin/sports-league/bulk-archive', [SportsLeagueController::class, 'bulkArchive'])->name('admin.sports-league.bulk-archive');
    Route::get('/admin/sports-league/register', [SportsLeagueController::class, 'create'])->name('admin.sports-league.create');
    Route::post('/admin/sports-league/register', [SportsLeagueController::class, 'store'])->name('admin.sports-league.store');

    Route::get('/admin/sports-league/{id}', [SportsLeagueController::class, 'show'])->name('admin.sports-league.show');
    Route::patch('/admin/sports-league/{id}/status/{status}', [SportsLeagueController::class, 'updateStatus'])->name('admin.sports-league.status');
    Route::get('/admin/sports-league/{id}/edit', [SportsLeagueController::class, 'edit'])->name('admin.sports-league.edit');
    Route::put('/admin/sports-league/{id}', [SportsLeagueController::class, 'update'])->name('admin.sports-league.update');
    Route::delete('/admin/sports-league/{id}', [SportsLeagueController::class, 'destroy'])->name('admin.sports-league.destroy');
});



// Superadmin-Only Actions (Middleware: auth, admin.only)
Route::middleware(['auth', 'admin.only'])->group(function () {
    // User Management
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.role');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/admin/users/{user}/approve', [UserManagementController::class, 'approve'])->name('admin.users.approve');

    // Partnerships/Sponsors management
    Route::resource('/admin/partners', PartnerController::class)->names([
        'index' => 'admin.partners.index',
        'create' => 'admin.partners.create',
        'store' => 'admin.partners.store',
        'edit' => 'admin.partners.edit',
        'update' => 'admin.partners.update',
        'destroy' => 'admin.partners.destroy',
    ])->except(['show']);

    // Portal Structure management
    Route::get('/admin/structure', [StructureManagementController::class, 'index'])->name('admin.structure.index');
    Route::post('/admin/structure/committees', [StructureManagementController::class, 'storeCommittee'])->name('admin.structure.committee.store');
    Route::delete('/admin/structure/committees/{committee}', [StructureManagementController::class, 'destroyCommittee'])->name('admin.structure.committee.destroy');
    Route::post('/admin/structure/committees/{id}/restore', [StructureManagementController::class, 'restoreCommittee'])->name('admin.structure.committee.restore');
    Route::delete('/admin/structure/committees/{id}/force-delete', [StructureManagementController::class, 'forceDeleteCommittee'])->name('admin.structure.committee.force-delete');
    Route::post('/admin/structure/initiatives', [StructureManagementController::class, 'storeInitiative'])->name('admin.structure.initiative.store');
    Route::put('/admin/structure/initiatives/{initiative}', [StructureManagementController::class, 'updateInitiative'])->name('admin.structure.initiative.update');
    Route::delete('/admin/structure/initiatives/{initiative}', [StructureManagementController::class, 'destroyInitiative'])->name('admin.structure.initiative.destroy');
    Route::post('/admin/structure/initiatives/{id}/restore', [StructureManagementController::class, 'restoreInitiative'])->name('admin.structure.initiative.restore');
    Route::delete('/admin/structure/initiatives/{id}/force-delete', [StructureManagementController::class, 'forceDeleteInitiative'])->name('admin.structure.initiative.force-delete');
    Route::get('/admin/structure/initiatives/{initiative}/form-builder', [FormBuilderController::class, 'edit'])->name('admin.structure.form-builder.edit');
    Route::put('/admin/structure/initiatives/{initiative}/form-builder', [FormBuilderController::class, 'update'])->name('admin.structure.form-builder.update');

    // Dynamic Sports Form Builder
    Route::get('/admin/sports-league/form-builder/create', [\App\Http\Controllers\Admin\SportsLeague\SportsFormBuilderController::class, 'create'])->name('admin.sports-league.form-builder.create');
    Route::post('/admin/sports-league/form-builder', [\App\Http\Controllers\Admin\SportsLeague\SportsFormBuilderController::class, 'store'])->name('admin.sports-league.form-builder.store');

    // Unified Audit Logs
    Route::get('/admin/logs', [UnifiedAuditLogController::class, 'index'])->name('admin.logs.index');
    Route::get('/admin/logs/export', [UnifiedAuditLogController::class, 'export'])->name('admin.logs.export');

    // Hero Carousel management
    Route::get('/admin/carousel', [CarouselSlideController::class, 'index'])->name('admin.carousel.index');
    Route::post('/admin/carousel', [CarouselSlideController::class, 'store'])->name('admin.carousel.store');
    Route::post('/admin/carousel/reorder', [CarouselSlideController::class, 'reorder'])->name('admin.carousel.reorder');
    Route::put('/admin/carousel/{carousel}', [CarouselSlideController::class, 'update'])->name('admin.carousel.update');
    Route::delete('/admin/carousel/{carousel}', [CarouselSlideController::class, 'destroy'])->name('admin.carousel.destroy');

    // Master Recycle Bin
    Route::middleware(['auth', 'admin.only', 'password.confirm'])->group(function () {
        Route::get('/admin/recycle-bin', [App\Http\Controllers\Admin\RecycleBinController::class, 'index'])->name('admin.recycle-bin.index');
        Route::post('/admin/recycle-bin/{type}/{id}/restore', [App\Http\Controllers\Admin\RecycleBinController::class, 'restore'])->name('admin.recycle-bin.restore');
        Route::delete('/admin/recycle-bin/{type}/{id}/force-delete', [App\Http\Controllers\Admin\RecycleBinController::class, 'forceDelete'])->name('admin.recycle-bin.force-delete');
    });
});



Route::get('/fix-storage-link', function () {
    $publicStoragePath = public_path('storage');
    if (is_link($publicStoragePath) || file_exists($publicStoragePath)) {
        if (is_link($publicStoragePath)) {
            unlink($publicStoragePath);
        } else {
            \Illuminate\Support\Facades\File::deleteDirectory($publicStoragePath);
        }
    }
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage link fixed successfully! Public path is: ' . $publicStoragePath;
});

// Fallback route to serve uploaded files if public storage symlink is missing or broken on production/shared hosting
Route::get('/storage/{path}', function ($path) {
    $fullPath = 'public/' . $path;
    if (!\Illuminate\Support\Facades\Storage::exists($fullPath)) {
        abort(404);
    }
    $file = \Illuminate\Support\Facades\Storage::path($fullPath);
    $type = mime_content_type($file);
    return \Illuminate\Support\Facades\Response::file($file, [
        'Content-Type' => $type,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

// Standalone Authentication Routes
require __DIR__.'/auth.php';

Route::get('/debug-my-requests', function() {
    $user = auth()->user();
    if (!$user) {
        return "Not logged in";
    }
    $email = trim(strtolower($user->email));
    
    $res = [];
    $res['user'] = [
        'id' => $user->id,
        'email' => $user->email,
    ];
    
    $res['counts'] = [
        'custom_requests' => \App\Models\CustomRequest::count(),
        'sports_registrations' => \App\Models\SportsRegistration::count(),
        'health_requests' => \App\Models\HealthRequest::count(),
        'medicine_requests' => \App\Models\MedicineRequest::count(),
        'silid_karunungan_requests' => \App\Models\SilidKarununganRequest::count(),
        'registration_responses' => \App\Models\RegistrationResponse::count(),
    ];
    
    $res['custom_requests_by_user_id'] = \App\Models\CustomRequest::where('user_id', $user->id)->get()->toArray();
    $res['custom_requests_by_email'] = \App\Models\CustomRequest::whereRaw('LOWER(email) = ?', [$email])->get()->toArray();
    
    $res['sports_by_user_id'] = \App\Models\SportsRegistration::where('user_id', $user->id)->get()->toArray();
    $res['sports_by_email'] = \App\Models\SportsRegistration::whereRaw('LOWER(email) = ?', [$email])->get()->toArray();
    
    $res['health_by_user_id'] = \App\Models\HealthRequest::where('user_id', $user->id)->get()->toArray();
    $res['health_by_email'] = \App\Models\HealthRequest::whereRaw('LOWER(email) = ?', [$email])->get()->toArray();

    $res['medicine_by_user_id'] = \App\Models\MedicineRequest::where('user_id', $user->id)->get()->toArray();
    $res['medicine_by_email'] = \App\Models\MedicineRequest::whereRaw('LOWER(email) = ?', [$email])->get()->toArray();

    $res['silid_by_user_id'] = \App\Models\SilidKarununganRequest::where('user_id', $user->id)->get()->toArray();
    $res['silid_by_email'] = \App\Models\SilidKarununganRequest::whereRaw('LOWER(email) = ?', [$email])->get()->toArray();

    return response()->json($res);
});
