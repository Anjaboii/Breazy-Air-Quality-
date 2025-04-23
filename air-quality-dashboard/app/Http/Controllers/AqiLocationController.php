<?php

namespace App\Http\Controllers;

use App\Models\AqiLocation;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AqiLocationController extends Controller
{
    // Show the form to add a new AQI location
    public function create()
    {
        return view('admin.aqi_locations.create');
    }

    // Store the new AQI location in the database
    public function store(Request $request)
    {
        // Validate input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',  // Ensuring latitude is within valid range
            'longitude' => 'required|numeric|between:-180,180', // Ensuring longitude is within valid range
        ]);

        // Fetch AQI data from the WAQI API
        try {
            $client = new Client();
            $response = $client->get("https://api.waqi.info/feed/geo:{$request->latitude};{$request->longitude}/?token=4b98b49468bc4a44cc2df7ac4e0007163f430796");



            $data = json_decode($response->getBody()->getContents(), true);

            // Extract AQI data from the API response
            $aqi = isset($data['data']['aqi']) ? $data['data']['aqi'] : null;
        } catch (\Exception $e) {
            // Log the error if the API request fails
            Log::error('Failed to fetch AQI data: ' . $e->getMessage());

            // Redirect the user with an error message
            return redirect()->route('aqi_locations.create')->with('error', 'Failed to fetch AQI data. Please try again later.');
        }

        // Create the AQI location in the database
        AqiLocation::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'aqi' => $aqi,
        ]);

        // Redirect back to the index page with a success message
        return redirect()->route('aqi_locations.index')->with('success', 'AQI Location added successfully!');
    }

    // Display all AQI locations
    public function index()
{
    // Fetch all AQI locations from the database
    $aqiLocations = AqiLocation::all();  // Fetch all records from the aqi_locations table

    // Pass the variable to the view using compact
    return view('admin.aqi_locations.index', compact('aqiLocations'));  // Passing the variable to the view
}
}