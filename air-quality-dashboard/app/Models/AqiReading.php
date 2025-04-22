<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AqiReading extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sensor_id',
        'aqi_value',
        'pm25',
        'pm10',
        'o3',
        'no2',
        'so2',
        'co',
        'category',
        'reading_timestamp',
    ];
    
    protected $casts = [
        'reading_timestamp' => 'datetime',
    ];
    
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}