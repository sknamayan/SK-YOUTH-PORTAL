<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Casts\SafeEncrypted;

class KkProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'surname',
        'first_name',
        'middle_name',
        'ext',
        'age',
        'sex',
        'gender',
        'dob',
        'civil_status',
        'purok_id',
        'street_address',
        'youth_classification',
        'contact_number',
        'email',
        'registered_sk_voter',
        'registered_national_voter',
        'attended_kk_assembly',
        'part_of_youth_org',
        'youth_org_name',
        'interested_in_joining',
        'part_of_lgbtqia',
        'pwd',
        'registered_disability',
        'highest_educational_attainment',
        'consent_given',
        'processed_by',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'registered_sk_voter' => 'boolean',
            'registered_national_voter' => 'boolean',
            'attended_kk_assembly' => 'boolean',
            'part_of_youth_org' => 'boolean',
            'interested_in_joining' => 'boolean',
            'part_of_lgbtqia' => 'boolean',
            'pwd' => 'boolean',
            'consent_given' => 'boolean',
            'street_address' => SafeEncrypted::class,
            'contact_number' => SafeEncrypted::class,
            'middle_name' => SafeEncrypted::class,
        ];
    }

    /**
     * Relationship: A profile belongs to a Purok.
     */
    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }
 
    /**
     * Relationship: A profile belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A profile has a user who processed/registered it.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Helper to get full name (Surname, First Name Middle Name Ext).
     */
    public function getFullNameAttribute(): string
    {
        $fullName = "{$this->surname}, {$this->first_name}";
        if ($this->middle_name) {
            $fullName .= " {$this->middle_name}";
        }
        if ($this->ext) {
            $fullName .= " {$this->ext}";
        }
        return $fullName;
    }

    /**
     * Get profile representation suitable for presentation to a given user.
     */
    public function toPresentableArray(User $user): array
    {
        $data = $this->toArray();
        $data['purokName'] = $this->purok ? $this->purok->purok_name : '';

        if (!$user->isSuperAdmin()) {
            $data['dob'] = '';
            $data['contact_number'] = '';
            $data['email'] = '';
            $data['registered_disability'] = '';
            $data['pwd'] = '';
        } else {
            $data['dob'] = $this->dob ? $this->dob->format('Y-m-d') : null;
        }

        return $data;
    }

    protected static function booted(): void
    {
        static::created(function ($model) {
            app(\App\Services\MailDispatchService::class)->queueKkProfileSubmitted($model);
        });
    }
}
