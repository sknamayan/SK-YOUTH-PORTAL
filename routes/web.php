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
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return 'Laravel Cache cleared and database migrations run successfully!';
    } catch (\Exception $e) {
        return 'Error clearing cache: ' . $e->getMessage();
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
    Route::post('/forms/health-consultation', function() {
        return redirect()->route('projects.index');
    })->name('forms.health.store');

    Route::get('/forms/mental-health', function() {
        return redirect()->route('landing', ['form' => 'mental-health']);
    })->name('forms.mental-health.create');
    Route::post('/forms/mental-health', function() {
        return redirect()->route('projects.index');
    })->name('forms.mental-health.store');

    Route::get('/forms/pabili-medicine', function() {
        return redirect()->route('landing', ['form' => 'medicine']);
    })->name('forms.medicine.create');
    Route::post('/forms/pabili-medicine', function() {
        return redirect()->route('projects.index');
    })->name('forms.medicine.store');

    Route::get('/forms/silid-karunungan', function() {
        return redirect()->route('landing', ['form' => 'silid']);
    })->name('forms.silid.create');
    Route::post('/forms/silid-karunungan', function() {
        return redirect()->route('projects.index');
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

    Route::middleware('kk.profile.completed')->group(function () {
        Route::get('/forms/sports-registration', [\App\Http\Controllers\SportsRegistrationController::class, 'showRegistrationForm'])->name('forms.sports.create');
        Route::post('/forms/sports-registration', [\App\Http\Controllers\SportsRegistrationController::class, 'submitRegistration'])->name('forms.sports.store');
    });
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
