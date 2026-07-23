<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\RedactsPii;

class RegistrationResponse extends Model
{
    use HasFactory, SoftDeletes, RedactsPii;

    protected $fillable = [
        'registration_form_id',
        'user_id',
        'citizen_name',
        'citizen_email',
        'answers',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function registrationForm(): BelongsTo
    {
        return $this->belongsTo(RegistrationForm::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function kkProfile(): BelongsTo
    {
        return $this->belongsTo(KkProfile::class, 'citizen_email', 'email');
    }

    public function getEmailAttribute(): ?string
    {
        return $this->citizen_email;
    }

    public function getFirstNameAttribute(): string
    {
        $parts = explode(' ', $this->citizen_name ?? '');
        return $parts[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        $parts = explode(' ', $this->citizen_name ?? '');
        return count($parts) > 1 ? end($parts) : ($parts[0] ?? '');
    }

    public function getMiddleNameAttribute(): ?string
    {
        return $this->kkProfile?->middle_name ?? $this->answers['middle_name'] ?? null;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->kkProfile?->age ?? (isset($this->answers['age']) ? intval($this->answers['age']) : null);
    }

    public function getGenderAttribute(): ?string
    {
        return $this->kkProfile?->gender ?? $this->kkProfile?->sex ?? $this->answers['gender'] ?? $this->answers['sex'] ?? 'Prefer not to say';
    }

    public function getContactNumberAttribute(): ?string
    {
        return $this->kkProfile?->contact_number ?? $this->answers['contact_number'] ?? $this->answers['contact'] ?? null;
    }

    public function getSportAttribute(): ?string
    {
        return $this->registrationForm?->league?->sport ?? $this->answers['sport'] ?? null;
    }

    public function getDivisionAttribute(): ?string
    {
        return $this->registrationForm?->division_name ?? $this->answers['division'] ?? null;
    }

    public function getTeamNameAttribute(): ?string
    {
        return $this->answers['team_name'] ?? $this->answers['team'] ?? null;
    }

    public function getEventDateAttribute()
    {
        $date = $this->answers['event_date'] ?? $this->answers['preferred_event_date'] ?? null;
        if ($date) {
            try {
                return \Carbon\Carbon::parse($date);
            } catch (\Exception $e) {
                // Ignore parse errors
            }
        }
        return $this->created_at;
    }

    public function getRemarksAttribute(): ?string
    {
        return $this->answers['remarks'] ?? null;
    }
}
