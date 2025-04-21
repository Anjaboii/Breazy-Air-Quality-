<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->get();
        return view('admin.locations', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'aqi' => 'required|numeric'
        ]);

        Location::create($validated);

        return response()->json(['message' => 'Location added successfully']);
    }

    public function destroy($id)
    {
        Location::destroy($id);
        return response()->json(['message' => 'Location deleted successfully']);
    }
}