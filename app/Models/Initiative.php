<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Initiative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['committee_id', 'title', 'description', 'picture_path', 'form_route', 'custom_fields', 'form_structure', 'is_coming_soon', 'show_in_quick_forms', 'is_highlighted'];

    public function coverPhotoUrl(): string
    {
        if ($this->picture_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->picture_path)) {
            return asset('storage/' . $this->picture_path);
        }

        return asset('images/default-initiative-cover.jpg');
    }

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
            'form_structure' => 'array',
            'is_coming_soon' => 'boolean',
            'show_in_quick_forms' => 'boolean',
            'is_highlighted' => 'boolean',
        ];
    }

    public function committee(): BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    public function accomplishmentReports(): HasMany
    {
        return $this->hasMany(AccomplishmentReport::class);
    }
}
