<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'read_time',
        'excerpt',
        'content',
        'image_path',
        'is_featured',
        'is_trending',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($article) {
            if (empty($article->slug) || $article->isDirty('title')) {
                $article->slug = static::generateUniqueSlug($article->title, $article->id);
            }
        });
    }

    private static function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        
        if (empty($slug)) {
            $slug = 'news-article';
        }
        
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Scope for published articles.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
