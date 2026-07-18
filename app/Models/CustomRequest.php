<?php

namespace App\Models;

use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRequest extends Model
{
    use HasComments, HasFactory;

    protected $fillable = [
        'user_id',
        'initiative_id',
        'first_name',
        'last_name',
        'email',
        'status',
        'custom_fields',
        'processed_by',
        'reference_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function initiative()
    {
        return $this->belongsTo(Initiative::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    protected function casts(): array
    {
        return [
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
