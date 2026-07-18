<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\GeneratesReferenceNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SilidKarununganRequest extends Model
{
    use HasComments, HasFactory, GeneratesReferenceNumber;

    protected $fillable = [
        'reference_number',
        'requestor_first_name',
        'requestor_last_name',
        'requestor_middle_name',
        'requestor_age',
        'email',
        'contact_number',
        'preferred_date',
        'preferred_time',
        'status',
        'custom_fields',
        'processed_by',
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
            'preferred_date' => 'date',
            'requestor_age' => 'integer',
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
