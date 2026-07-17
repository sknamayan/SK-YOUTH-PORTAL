<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_approved',
        'contact_number',
        'avatar',
        'theme',
        'language',
        'notify_request_status',
        'notify_announcements',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'notify_request_status' => 'boolean',
            'notify_announcements' => 'boolean',
        ];
    }

    /**
     * Check if the user is a super administrator.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if the user is a staff member.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if the user is a Data Privacy Officer (kept for backwards compatibility).
     */
    public function isDpo(): bool
    {
        return $this->role === 'dpo';
    }

    /**
     * Check if the user has clearance to view raw PII.
     */
    public function hasPiiClearance(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'dpo']);
    }

    /**
     * Check if the user has access to the dashboard.
     */
    public function canAccessDashboard(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'staff', 'dpo']);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * Get the unread notifications for the user.
     */
    public function unreadNotifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notification::class)->whereNull('read_at')->latest();
    }
 
    /**
     * Get the KK Profile associated with the user.
     */
    public function kkProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KkProfile::class);
    }

    /**
     * Fetch the user's completed and approved KK Profile, if it exists.
     */
    public function approvedKkProfile(): ?KkProfile
    {
        return KkProfile::withoutGlobalScopes()
            ->where(function ($query) {
                $query->where('user_id', $this->id)
                      ->orWhere('email', $this->email);
            })
            ->where('status', 'approved')
            ->first();
    }

    protected static function booted(): void
    {
        static::created(function ($model) {
            app(\App\Services\MailDispatchService::class)->queueAccountCreated($model);
        });
    }
}
