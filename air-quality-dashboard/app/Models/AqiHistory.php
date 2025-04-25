<?php

// app/Models/AqiHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AqiHistory extends Model
{
    protected $table = 'aqi_histories';
    
    protected $fillable = [
        'location_id',
        'aqi',
        'date'
    ];
    
    public function location()
    {
        return $this->belongsTo(AqiLocation::class);
    }
}