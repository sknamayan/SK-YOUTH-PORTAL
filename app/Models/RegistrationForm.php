<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegistrationForm extends Model
{
    use HasFactory;

    protected $fillable = ['league_id', 'division_name', 'description', 'is_active', 'type'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function registrationResponses(): HasMany
    {
        return $this->hasMany(RegistrationResponse::class);
    }
}
