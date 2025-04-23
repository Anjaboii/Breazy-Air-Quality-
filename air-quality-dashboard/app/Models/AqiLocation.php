<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AqiLocation extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'aqi'];
}