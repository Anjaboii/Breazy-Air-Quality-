<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AqiLocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'latitude', 'longitude', 'aqi'];  // Define the fillable fields
}
