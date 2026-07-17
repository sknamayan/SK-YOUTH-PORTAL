<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class AdultProfile extends KkProfile
{
    protected $table = 'kk_profiles';

    protected static function booted(): void
    {
        // Override parent booted to apply adult-specific scope
        static::addGlobalScope('adult_only', function (Builder $builder) {
            $builder->where('category', 'adult')
                    ->whereBetween('age', [31, 39]);
        });
    }
}
