<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    protected $fillable = ['sensor_id', 'aqi', 'components', 'timestamp'];
protected $casts = [
    'components' => 'array',
    'timestamp' => 'datetime'
];
}
