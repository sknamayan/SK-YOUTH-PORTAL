<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purok extends Model
{
    use HasFactory;

    protected $fillable = [
        'purok_name',
        'purok_code',
        'street_name',
    ];

    /**
     * Relationship: A Purok has many Katipunan ng Kabataan profiles.
     */
    public function kkProfiles()
    {
        return $this->hasMany(KkProfile::class);
    }
}
