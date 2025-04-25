<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AqiLocation extends Model
{
    // Define fillable properties for mass assignment
    protected $fillable = ['name', 'latitude', 'longitude', 'aqi'];

    // Define the relationship to AqiHistory
    public function aqiHistories()
    {
        return $this->hasMany(AqiHistory::class);
    }
}
