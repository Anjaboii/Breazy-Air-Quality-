<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AqiLocation;
use App\Models\AqiSensor;

class AqiController extends Controller
{
    public function getLocations()
    {
        // Get data from database
        $locations = AqiLocation::all()->map(function($location) {
            return [
                'id' => $location->id,
                'location' => $location->name,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'aqi' => $location->current_aqi,
                'timestamp' => $location->updated_at->toDateTimeString()
            ];
        });

        return response()->json([
            'success' => true,
            'locations' => $locations
        ]);
    }

    public function getSensors()
    {
        // Get data from database
        $sensors = AqiSensor::all()->map(function($sensor) {
            return [
                'id' => $sensor->id,
                'name' => $sensor->name,
                'description' => $sensor->description,
                'latitude' => $sensor->latitude,
                'longitude' => $sensor->longitude,
                'status' => $sensor->status
            ];
        });

        return response()->json($sensors);
    }
}