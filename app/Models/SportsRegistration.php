<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\GeneratesReferenceNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SportsRegistration extends Model
{
    use HasComments, HasFactory, GeneratesReferenceNumber, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'user_id',
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'gender',
        'email',
        'contact_number',
        'sport',
        'division',
        'position',
        'team_name',
        'event_date',
        'remarks',
        'status',
        'custom_fields',
        'processed_by',
        'birthdate',
        'address',
        'kk_profiling_status',
        'profile_picture',
        'guardian_first_name',
        'guardian_middle_name',
        'guardian_last_name',
        'guardian_age',
        'guardian_relation',
        'guardian_contact_number',
        'guardian_address',
        'guardian_gov_id',
        'voter_cert',
        'health_declaration',
        'consent_waiver',
    ];

    /**
     * Relationship: A request has a user who processed it.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'birthdate' => 'date',
            'age' => 'integer',
            'guardian_age' => 'integer',
            'consent_waiver' => 'boolean',
            'custom_fields' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($model) {
            app(\App\Services\MailDispatchService::class)->queueRequestReceived($model);
        });
    }
}
