<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesReferenceNumber
{
    /**
     * Boot the GeneratesReferenceNumber trait.
     */
    protected static function bootGeneratesReferenceNumber(): void
    {
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                do {
                    // Generate unique alphanumeric code (mix of uppercase letters and numbers)
                    $ref = 'SK-REQ-' . strtoupper(Str::random(8));
                } while (static::where('reference_number', $ref)->exists());

                $model->reference_number = $ref;
            }
        });
    }
}
