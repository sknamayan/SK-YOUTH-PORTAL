<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\GeneratesReferenceNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\SafeEncrypted;

use Illuminate\Database\Eloquent\SoftDeletes;

class HealthRequest extends Model
{
    use HasComments, HasFactory, GeneratesReferenceNumber, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'first_name',
        'last_name',
        'middle_name',
        'age',
        'gender',
        'email',
        'contact_number',
        'concerns',
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
            'age' => 'integer',
            'custom_fields' => 'array',
            'contact_number' => SafeEncrypted::class,
            'concerns' => SafeEncrypted::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($model) {
            app(\App\Services\MailDispatchService::class)->queueRequestReceived($model);
        });
    }
}
