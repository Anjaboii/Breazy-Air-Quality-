<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AqiLocation;
use Illuminate\Http\Request;

class AqiLocationController extends Controller
{
    // Get all locations
    public function index()
    {
        return response()->json(AqiLocation::all());
    }

    // Get locations (with optional filtering)
    public function getLocations()
    {
        $locations = AqiLocation::all(); // Or with any filtering you need
        return response()->json($locations);
    }

    // Delete a location by ID
    public function deleteLocation($id)
    {
        $location = AqiLocation::find($id);
        
        if ($location) {
            $location->delete();
            return response()->json(['message' => 'Location deleted successfully'], 200);
        }

        return response()->json(['message' => 'Location not found'], 404);
    }

    // Edit a location by ID
    public function editLocation(Request $request, $id)
    {
        $location = AqiLocation::find($id);
        
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        // Validate incoming request data if needed
        $validated = $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            // Add any other fields to validate
        ]);

        // Update location
        $location->name = $validated['name'];
        $location->latitude = $validated['latitude'];
        $location->longitude = $validated['longitude'];
        // Update other fields as needed
        $location->save();

        return response()->json(['message' => 'Location updated successfully', 'data' => $location], 200);
    }
}
