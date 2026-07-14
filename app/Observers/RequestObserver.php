<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class RequestObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        ActivityLog::record('request_created', $model, [
            'type' => class_basename($model)
        ], null);

        $isSports = ($model instanceof \App\Models\RegistrationResponse || $model instanceof \App\Models\SportsRegistration);
        $type = $isSports ? 'sports_league' : 'service_request';

        // 1. Notify the citizen who submitted the request
        $email = $model->email ?? $model->citizen_email ?? null;
        if ($email) {
            $user = \App\Models\User::where('email', $email)->first();
            if ($user) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title' => $isSports ? 'Sports League Registration Submitted' : 'Request Submitted',
                    'message' => $isSports ? 'Your sports league registration has been submitted successfully.' : 'Your service request has been submitted successfully.',
                    'url' => route('profile.my-requests'),
                    'type' => $type,
                ]);
            }
        }

        // 2. Notify all admin/staff who can access the dashboard
        $admins = \App\Models\User::all()->filter(fn ($u) => $u->canAccessDashboard());
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'title' => $isSports ? 'New Sports Registration' : 'New Service Request',
                'message' => $isSports ? 'A new sports league registration has been submitted.' : 'A new service request has been submitted.',
                'url' => $isSports ? route('admin.sports-league.index') : route('dashboard.requests.index'),
                'type' => $type,
            ]);
        }
    }

    /**
     * Handle the Model "updating" event.
     */
    public function updating(Model $model): void
    {
        if ($model->isDirty('status')) {
            if (in_array(strtolower($model->status), ['pending'])) {
                $model->processed_by = null;
            } elseif (auth()->check()) {
                $model->processed_by = auth()->id();
            }
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($model->isDirty('status')) {
            ActivityLog::record('status_changed', $model, [
                'from' => $model->getOriginal('status'),
                'to' => $model->status,
            ], auth()->id());
        } else {
            $changes = [];
            foreach ($model->getDirty() as $key => $value) {
                $changes[$key] = [
                    'from' => $model->getOriginal($key),
                    'to' => $value
                ];
            }
            ActivityLog::record('request_updated', $model, [
                'type' => class_basename($model),
                'changes' => $changes
            ], auth()->id());
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        ActivityLog::record('request_cancelled', $model, [
            'type' => class_basename($model),
            'email' => $model->email
        ], auth()->id());
    }
}
