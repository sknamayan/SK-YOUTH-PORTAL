<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccomplishmentReport extends Model
{
    use HasFactory;

    protected $fillable = ['initiative_id', 'report_title', 'file_path', 'reporting_period'];

    protected $casts = [
        'reporting_period' => 'date',
    ];

    public function initiative(): BelongsTo
    {
        return $this->belongsTo(Initiative::class);
    }
}
