<?php

namespace App\Providers;

use App\Models\CustomRequest;
use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\RegistrationResponse;
use App\Models\SportsRegistration;
use App\Models\User;
use App\Models\Committee;
use App\Models\Initiative;
use App\Models\Partner;
use App\Models\AccomplishmentReport;
use App\Models\ActivityLog;
use App\Models\CarouselSlide;
use App\Observers\RequestObserver;
use App\Observers\SystemAuditObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register request observers
        HealthRequest::observe(RequestObserver::class);
        MedicineRequest::observe(RequestObserver::class);
        SilidKarununganRequest::observe(RequestObserver::class);
        RegistrationResponse::observe(RequestObserver::class);
        CustomRequest::observe(RequestObserver::class);
        SportsRegistration::observe(RequestObserver::class);

        // Register system audit observers
        User::observe(SystemAuditObserver::class);
        Committee::observe(SystemAuditObserver::class);
        Initiative::observe(SystemAuditObserver::class);
        Partner::observe(SystemAuditObserver::class);
        AccomplishmentReport::observe(SystemAuditObserver::class);
        CarouselSlide::observe(SystemAuditObserver::class);

        // Register Auth audit event listeners
        Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'user_login',
                'subject_type' => get_class($event->user),
                'subject_id' => $event->user->id,
                'payload' => ['email' => $event->user->email],
                'ip_address' => request()->ip()
            ]);
        });

        Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                ActivityLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'user_logout',
                    'subject_type' => get_class($event->user),
                    'subject_id' => $event->user->id,
                    'payload' => ['email' => $event->user->email],
                    'ip_address' => request()->ip()
                ]);
            }
        });

        Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            ActivityLog::create([
                'user_id' => null,
                'action' => 'user_login_failed',
                'subject_type' => User::class,
                'subject_id' => 0,
                'payload' => ['email' => $event->credentials['email'] ?? 'unknown'],
                'ip_address' => request()->ip()
            ]);
        });

        Event::listen(\Illuminate\Auth\Events\Registered::class, function ($event) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'user_registered',
                'subject_type' => get_class($event->user),
                'subject_id' => $event->user->id,
                'payload' => ['email' => $event->user->email],
                'ip_address' => request()->ip()
            ]);
        });

        // Register default tailwind pagination view
        Paginator::defaultView('vendor.pagination.tailwind');

        // Share pending counts with all views for dashboard navigation badges
        view()->composer('*', function ($view) {
            if (auth()->check() && auth()->user()->canAccessDashboard()) {
                $pendingUserApprovalsCount = 0;
                if (auth()->user()->isAdmin() || auth()->user()->isDpo()) {
                    $pendingUserApprovalsCount = \App\Models\User::where('is_approved', false)->count();
                }

                $pendingServiceRequestsCount = \App\Models\HealthRequest::whereIn('status', ['pending', 'review'])->count()
                    + \App\Models\MedicineRequest::whereIn('status', ['pending', 'review'])->count()
                    + \App\Models\SilidKarununganRequest::whereIn('status', ['pending', 'review'])->count()
                    + \App\Models\SportsRegistration::whereIn('status', ['pending', 'review'])->count();

                $pendingKkProfilesCount = \App\Models\KkProfile::where('status', 'pending')->count();

                $pendingSportsRegistrationsCount = \App\Models\SportsRegistration::whereIn('status', ['pending', 'review', 'Pending'])->count();

                $view->with([
                    'pendingUserApprovalsCount' => $pendingUserApprovalsCount,
                    'pendingServiceRequestsCount' => $pendingServiceRequestsCount,
                    'pendingKkProfilesCount' => $pendingKkProfilesCount,
                    'pendingSportsRegistrationsCount' => $pendingSportsRegistrationsCount,
                ]);
            }
        });

        // Setup rate limiter for forms submission
        RateLimiter::for('forms', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return back()->withInput()->withErrors([
                        'rate_limit' => 'Too many requests. You are allowed 10 submissions per minute.'
                    ]);
                });
        });
    }
}
