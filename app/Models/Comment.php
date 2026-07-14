<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Comment extends Model
{
    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'user_id',
        'author_name',
        'author_email',
        'body',
        'attachment_path',
        'attachment_original_name',
        'attachment_mime',
        'is_staff',
    ];

    protected function casts(): array
    {
        return [
            'is_staff' => 'boolean',
        ];
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authorLabel(): string
    {
        if ($this->user) {
            return $this->user->name;
        }

        return $this->author_name ?? 'Citizen';
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function attachmentUrl(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return storage_url($this->attachment_path);
    }

    public function isImageAttachment(): bool
    {
        return $this->attachment_mime && str_starts_with($this->attachment_mime, 'image/');
    }
}
