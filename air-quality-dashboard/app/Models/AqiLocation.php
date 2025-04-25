<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AqiLocation extends Model
{
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        // Add other fillable fields
    ];

    public function aqiHistory(): HasMany
    {
        return $this->hasMany(AqiHistory::class);
    }
}