<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class ChildProfile extends KkProfile
{
    protected $table = 'kk_profiles';

    protected static function booted(): void
    {
        // Override parent booted to apply child-specific scope
        static::addGlobalScope('child_only', function (Builder $builder) {
            $builder->where('category', 'child')
                    ->whereBetween('age', [6, 12]);
        });
    }
}
