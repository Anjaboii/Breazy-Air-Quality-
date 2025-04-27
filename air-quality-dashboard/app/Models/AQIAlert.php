<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AQIAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'aqi_value',
        'message',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'aqi_value' => 'integer'
    ];

    /**
     * Get the location that the alert belongs to
     */
    public function location()
    {
        return $this->belongsTo(AqiLocation::class);
    }
}