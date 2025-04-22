<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude', 'is_active'];

public function readings()
{
    return $this->hasMany(Reading::class);
}
}
