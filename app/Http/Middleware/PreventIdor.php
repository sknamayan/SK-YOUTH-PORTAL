<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class PreventIdor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $type = $request->route('type');
        $id = $request->route('id');

        if (!$type || !$id) {
            return $next($request);
        }

        // Find the target request model
        $model = match ($type) {
            'health' => \App\Models\HealthRequest::find($id),
            'medicine' => \App\Models\MedicineRequest::find($id),
            'silid' => \App\Models\SilidKarununganRequest::find($id),
            'sports' => \App\Models\SportsRegistration::find($id),
            'custom' => \App\Models\CustomRequest::find($id),
            default => null
        };

        if (!$model) {
            return $next($request);
        }

        // Fetch request email
        $requestEmail = $model->email ?? $model->citizen_email ?? null;

        // Perform authorization check
        $authorized = false;

        if (auth()->check()) {
            $user = auth()->user();

            // Admin, DPO, and Staff roles have universal access
            if ($user->canAccessDashboard()) {
                $authorized = true;
            } else {
                // Citizens must own the record (email matching)
                if ($requestEmail && strtolower($user->email) === strtolower($requestEmail)) {
                    $authorized = true;
                }
            }
        } else {
            // For unauthenticated request tracking, verify via session tracked_email
            $sessionEmail = session('tracked_email');
            if ($sessionEmail && $requestEmail && strtolower($sessionEmail) === strtolower($requestEmail)) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            // Log security warning / anomaly log
            $ip = $request->ip();
            $userId = auth()->id() ?? 'Guest';
            Log::warning("SECURITY WARNING: Potential IDOR attempt blocked. User ID: {$userId}, IP: {$ip}, Action: {$request->method()} on request {$type} #{$id}");

            // Create an audit log entry for anomaly detection
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'security_idor_blocked',
                'subject_type' => get_class($model),
                'subject_id' => $id,
                'payload' => [
                    'ip_address' => $ip,
                    'user_email' => auth()->user()?->email ?? 'Guest',
                    'record_email' => $requestEmail,
                    'path' => $request->path()
                ],
                'ip_address' => $ip
            ]);

            abort(403, 'Unauthorized access: You do not have permission to view or modify this record.');
        }

        return $next($request);
    }
}
