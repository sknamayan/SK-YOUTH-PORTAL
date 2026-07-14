<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransparencyPost extends Model
{
    public const CATEGORIES = [
        'budget' => 'Budget & Appropriations',
        'resolution' => 'Resolutions & Ordinances',
        'financial' => 'Financial Reports',
        'accomplishment' => 'Accomplishment Reports',
        'announcement' => 'Public Announcements',
    ];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'file_path',
        'image_path',
        'published_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $post) {
            if (empty($post->slug) || $post->isDirty('title')) {
                $post->slug = static::generateUniqueSlug($post->title, $post->id);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function fileUrl(): ?string
    {
        return storage_url($this->file_path);
    }

    public function imageUrl(): ?string
    {
        return storage_url($this->image_path);
    }

    private static function generateUniqueSlug(string $title, ?int $id = null): string
    {
        $slug = Str::slug($title) ?: 'transparency-post';
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
