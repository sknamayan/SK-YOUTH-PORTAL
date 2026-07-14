<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'payload',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    /**
     * Polymorphic relation to get the parent subject model.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Relation to get the actor (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper to write a log record.
     */
    public static function record($action, Model $model, array $payload = null, $userId = null)
    {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => $payload,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Boot the model to register anomaly checking on creation.
     */
    protected static function booted(): void
    {
        static::created(function (self $log) {
            $anomalyDetected = false;
            $reason = '';

            if ($log->action === 'security_idor_blocked') {
                $anomalyDetected = true;
                $reason = 'Blocked IDOR attempt detected from IP: ' . ($log->ip_address ?? 'unknown');
            } elseif ($log->action === 'user_login_failed') {
                // Check if there are more than 5 failed logins from this IP in the last 15 minutes
                $recentFailed = self::where('action', 'user_login_failed')
                    ->where('ip_address', $log->ip_address)
                    ->where('created_at', '>=', now()->subMinutes(15))
                    ->count();

                if ($recentFailed >= 5) {
                    $anomalyDetected = true;
                    $reason = 'Brute-force login attempts detected (5+ failures in 15 mins) from IP: ' . ($log->ip_address ?? 'unknown');
                }
            } elseif (str_contains($log->action, 'export') || str_contains($log->action, 'download')) {
                // Check if user has performed multiple exports in the last 5 minutes
                if ($log->user_id) {
                    $recentExports = self::where('user_id', $log->user_id)
                        ->where(function($q) {
                            $q->where('action', 'like', '%export%')
                              ->orWhere('action', 'like', '%download%');
                        })
                        ->where('created_at', '>=', now()->subMinutes(5))
                        ->count();

                    if ($recentExports >= 5) {
                        $anomalyDetected = true;
                        $reason = 'High-frequency data export detected from User ID: ' . $log->user_id;
                    }
                }
            }

            if ($anomalyDetected) {
                // Dispatch alert email to DPO
                try {
                    $dpos = \App\Models\User::where('role', 'dpo')->pluck('email')->toArray();
                    if (!empty($dpos)) {
                        \Illuminate\Support\Facades\Mail::raw(
                            "WARNING: A security anomaly has been detected on the portal.\n\n" .
                            "Event: {$log->action}\n" .
                            "Reason: {$reason}\n" .
                            "Actor (User ID): " . ($log->user_id ?? 'Unauthenticated') . "\n" .
                            "IP Address: {$log->ip_address}\n" .
                            "Time: " . now()->toDateTimeString() . "\n" .
                            "Payload: " . json_encode($log->payload, JSON_PRETTY_PRINT),
                            function ($message) use ($dpos) {
                                $message->to($dpos)
                                    ->subject('SECURITY ALERT: Portal Anomaly Detected');
                            }
                        );
                    }
                } catch (\Exception $e) {
                    // Fail silently so database log creation is never blocked
                    \Illuminate\Support\Facades\Log::error("Failed to dispatch anomaly alert email: " . $e->getMessage());
                }
            }
        });
    }

    /**
     * Scope a query to only include records related to sensitive data access (DPO).
     */
    public function scopeDpo($query)
    {
        return $query->where(function ($q) {
            $q->where('action', 'like', '%pii%')
              ->orWhere('payload', 'like', '%pii%');
        });
    }
}
