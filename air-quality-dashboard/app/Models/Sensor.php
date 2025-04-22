<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sensor_id',
        'name',
        'latitude',
        'longitude',
        'location_description',
        'is_active',
    ];
    
    public function readings()
    {
        return $this->hasMany(AqiReading::class);
    }
    
    public function latestReading()
    {
        return $this->hasOne(AqiReading::class)->latestOfMany('reading_timestamp');
    }
}