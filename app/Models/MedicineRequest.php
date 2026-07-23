<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\GeneratesReferenceNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\SafeEncrypted;

use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineRequest extends Model
{
    use HasComments, HasFactory, GeneratesReferenceNumber, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'user_id',
        'requestor_first_name',
        'requestor_last_name',
        'requestor_age',
        'requestor_gender',
        'email',
        'contact_number',
        'complete_address',
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
            'requestor_age' => 'integer',
            'custom_fields' => 'array',
            'contact_number' => SafeEncrypted::class,
            'complete_address' => SafeEncrypted::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($model) {
            app(\App\Services\MailDispatchService::class)->queueRequestReceived($model);
        });
    }
}
