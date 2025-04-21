<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AqiLocation;

class AqiDataSeeder extends Seeder
{
    public function run()
    {
        AqiLocation::create([
            'name' => 'Colombo City Center',
            'latitude' => 6.9271,
            'longitude' => 79.8612,
            'current_aqi' => 45
        ]);
    }
}
