<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\GeneratesReferenceNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SilidKarununganRequest extends Model
{
    use HasComments, HasFactory, GeneratesReferenceNumber, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'user_id',
        'initiative_id',
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

    /**
     * Relationship: A request belongs to an initiative.
     */
    public function initiative()
    {
        return $this->belongsTo(Initiative::class, 'initiative_id');
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

            if ($model->preferred_date && $model->preferred_time) {
                $dateStr = $model->preferred_date instanceof \Carbon\Carbon 
                    ? $model->preferred_date->format('Y-m-d') 
                    : $model->preferred_date;
                
                \App\Models\Booking::updateOrCreate([
                    'bookable_type' => get_class($model),
                    'bookable_id' => $model->id,
                ], [
                    'user_id' => $model->user_id,
                    'booking_date' => $dateStr,
                    'booking_time' => $model->preferred_time,
                    'status' => $model->status ?? 'pending',
                ]);
            }
        });

        static::updated(function ($model) {
            if ($model->preferred_date && $model->preferred_time) {
                $dateStr = $model->preferred_date instanceof \Carbon\Carbon 
                    ? $model->preferred_date->format('Y-m-d') 
                    : $model->preferred_date;

                \App\Models\Booking::updateOrCreate([
                    'bookable_type' => get_class($model),
                    'bookable_id' => $model->id,
                ], [
                    'user_id' => $model->user_id,
                    'booking_date' => $dateStr,
                    'booking_time' => $model->preferred_time,
                    'status' => $model->status ?? 'pending',
                ]);
            }
        });
    }
}
