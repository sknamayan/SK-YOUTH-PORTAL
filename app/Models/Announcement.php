<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'type',
        'is_active',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Scope to filter active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('published_at', '<=', now());
    }

    /**
     * Relation to author of the announcement.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
