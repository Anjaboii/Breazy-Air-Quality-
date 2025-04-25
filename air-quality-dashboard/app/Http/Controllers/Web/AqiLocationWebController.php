<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller; 
use App\Models\AqiLocation;
use App\Models\AqiHistory;
use Illuminate\Http\Request;

class AqiLocationWebController extends Controller
{
    // Method to show all locations
    public function index()
    {
        $locations = AqiLocation::all(); // Get all AQI locations
        return view('aqi.locations', compact('locations'));
    }

    // Method to show the AQI history for a specific location
    public function show(AqiLocation $location)
    {
        $aqiHistory = AqiHistory::where('location_id', $location->id)
                      ->orderBy('date', 'desc')
                      ->paginate(10);

        return view('aqi.location_history', compact('location', 'aqiHistory'));
    }
}
