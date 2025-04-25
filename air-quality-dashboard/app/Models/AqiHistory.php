<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AqiHistory extends Model
{
    protected $fillable = [
        'location_id',
        'aqi',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(AqiLocation::class);
    }
}