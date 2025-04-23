<?php

namespace Database\Seeders;

use App\Models\AqiLocation;
use Illuminate\Database\Seeder;

class AqiLocationsTableSeeder extends Seeder
{
    public function run()
    {
        AqiLocation::create([
            'name' => 'Colombo Central',
            'latitude' => 6.9271,
            'longitude' => 79.8612,
            'aqi' => 45
        ]);
        
        // Add more locations as needed
    }
}