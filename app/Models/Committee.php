<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Committee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['project_id', 'name', 'slug'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($committee) {
            if ($committee->isForceDeleting()) {
                $committee->initiatives()->withTrashed()->each(function ($initiative) {
                    // Delete custom requests
                    \App\Models\CustomRequest::where('initiative_id', $initiative->id)->delete();
                    // Delete predefined forms associated
                    if ($initiative->form_route) {
                        // Predefined models deleted.
                    }
                    $initiative->forceDelete();
                });
            } else {
                $committee->initiatives()->each(function ($initiative) {
                    $initiative->delete();
                });
            }
        });

        static::restoring(function ($committee) {
            $committee->initiatives()->onlyTrashed()->each(function ($initiative) {
                $initiative->restore();
            });
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function initiatives(): HasMany
    {
        return $this->hasMany(Initiative::class);
    }

    public function officials(): HasMany
    {
        return $this->hasMany(SkOfficial::class)->active()->ordered();
    }
}
