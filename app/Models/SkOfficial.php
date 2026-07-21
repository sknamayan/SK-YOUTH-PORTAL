<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SkOfficial extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'position',
        'bio',
        'photo_path',
        'email',
        'contact_number',
        'term',
        'sort_order',
        'is_active',
        'committee_id',
    ];

    public function committee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Committee::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $official) {
            if (empty($official->slug) || $official->isDirty('name')) {
                $official->slug = static::generateUniqueSlug($official->name, $official->id);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function photoUrl(): ?string
    {
        return storage_url($this->photo_path);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials ?: 'SK';
    }

    private static function generateUniqueSlug(string $name, ?int $id = null): string
    {
        $slug = Str::slug($name) ?: 'official';
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
